<?php

namespace App\Services;

use App\DTOs\MailViewDTO;
use App\Models\Customer;
use App\DTOs\OutgoingMailDTO;
use App\Models\Mail;
use App\Models\Ticket;
use App\Models\OutgoingMail;
use App\Models\User;
use App\Models\Queue;
use App\Mail\GenericMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Mail\MailManager;
use App\DTOs\MailDTO;
use App\Models\MailBody;
use Crypt;
use App\Models\MailMeta;
use PHPUnit\Event\Code\Throwable;

class MailService {
    public function getByTicketId(int $ticketId): array {
        $ticket = Ticket::with('mail')->findOrFail($ticketId);
        $rootMail = $ticket->mail;

        if (!$rootMail) {
            return [];
        }

        $replies = Mail::where('in_reply_to', $ticketId)
            ->orderBy('created_at')
            ->get();

        return $replies->map(fn($mail) => new MailViewDTO($mail))->toArray();
    }

    public function getReplies(int $mailId): array {
        $replies = Mail::where('in_reply_to', $mailId)
            ->orderBy('created_at')
            ->get();

        return $replies->map(fn($mail) => new MailViewDTO($mail))->toArray();
    }

    public function send(MailDTO $dto): array {
        Log::info(json_encode($dto) . " dto mailservice");
        $queue = $dto->queue;
        $sent = [];

        $toUserEmails = [];
        $ccUserEmails = [];
        $toCustomerEmails = [];
        $ccCustomerEmails = [];
        $toCustomerIds = [];
        $ccCustomerIds = [];

        foreach ($dto->toUsers as $id) {
            $user = null;
            if (is_numeric($id)) {
                $user = User::find($id);
                if (!$user || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) continue;
                $toUserEmails[] = $user->email;
            } elseif (is_string($id) && filter_var($id, FILTER_VALIDATE_EMAIL)) {
                $toUserEmails[] = $id;
            }
        }

        foreach ($dto->ccUsers as $id) {
            $user = null;
            if (is_numeric($id)) {
                $user = User::find($id);
                if (!$user || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) continue;
                $ccUserEmails[] = $user->email;
            } elseif (is_string($id) && filter_var($id, FILTER_VALIDATE_EMAIL)) {
                $ccUserEmails[] = $id;
            }
        }

        foreach ($dto->toCustomers as $customerInput) {
            $customer = null;
            if (is_numeric($customerInput)) {
                $customer = Customer::find($customerInput);
            } else {
                if (!filter_var($customerInput, FILTER_VALIDATE_EMAIL)) continue;
                $customer = Customer::where('email', $customerInput)->first();
                if (!$customer) {
                    $customer = Customer::create([
                        'full_name' => self::fullNameFromEmail($customerInput),
                        'email' => $customerInput
                    ]);
                }
            }
            if (!$customer || !filter_var($customer->email, FILTER_VALIDATE_EMAIL)) continue;
            $toCustomerIds[] = $customer->id;
            $toCustomerEmails[] = $customer->email;
        }
        log::info("toCustomerEmails: " . json_encode($toCustomerEmails));
        foreach ($dto->ccCustomers as $customerInput) {
            $customer = null;
            if (is_numeric($customerInput)) {
                $customer = Customer::find($customerInput);
            } else {
                $customer = Customer::where('email', $customerInput)->first();
                Log::info("Customer input: " . $customerInput);
                if (!$customer) {
                    $customer = Customer::create([
                        'full_name' => self::fullNameFromEmail($customerInput),
                        'email' => $customerInput
                    ]);
                }
            }

            if (!$customer || !filter_var($customer->email, FILTER_VALIDATE_EMAIL)) continue;

            $ccCustomerIds[] = $customer->id;
            $ccCustomerEmails[] = $customer->email;
        }

        $allToRecipients = array_merge($toUserEmails, $toCustomerEmails);
        $allCCRecipients = array_merge($ccUserEmails, $ccCustomerEmails);

        $this->sendMailTo($queue, $allToRecipients, $allCCRecipients, $dto->subject, $dto->body, $sent);

        $mail = Mail::create([
            'sender_id' => $dto->senderId ?? auth('api')->id(),
            'sender_type' => Mail::USER,
            'to_users' => $dto->toUsers, //json array([1 send::true], [2, send::false, )
            'cc_users' => $dto->ccUsers,
            'to_customers' => $toCustomerIds,
            'cc_customers' => $ccCustomerIds,
            'in_reply_to' => $dto->inReplyTo
        ]);

        $mailBody = MailBody::fromMailDTO($dto);
        $mailBody->mail_id = $mail->id;
        $mailBody->save();

        return $sent;
    }

    public static function fullNameFromEmail(string $email): string {

        $name = explode('@', $email)[0] ?? '';
        Log::info("Extracted name from email: " . $name);
        $name = str_replace(['.', '_'], ' ', $name);
        return ucwords($name);
    }

    private function sendMailTo(Queue $queue, array $toEmails, array $ccEmails, string $subject, string $body, array &$sent): void {
        // $toEmails = app()->environment('local') ? ['smithettiene@yahoo.com'] : $toEmails;
        $toEmails = is_array($toEmails) ? $toEmails : [$toEmails];
        $ccEmails = is_array($ccEmails) ? $ccEmails : [$ccEmails];

        try {
            $mailer = app()->make(MailManager::class)->mailer(
                $this->createCustomMailer($queue)
            );

            $mailer->to($toEmails)
                ->cc($ccEmails)
                ->send(
                    (new GenericMail($subject, $body))
                        ->from($queue->from_email, $queue->from_name)
                );

            foreach ($toEmails as $email) {
                $sent[] = [
                    'email' => $email,
                    'status' => 'sent',
                    'sent_at' => now()->toDateTimeString()
                ];
            }

            foreach ($ccEmails as $email) {
                $sent[] = [
                    'email' => $email,
                    'status' => 'sent',
                    'sent_at' => now()->toDateTimeString()
                ];
            }
        } catch (\Throwable $e) {
            Log::error("❌ Failed to send mail to $toEmails: " . $e->getMessage());

            $sent[] = [
                'email' => json_encode($toEmails),
                'status' => 'failed',
                'failed_at' => now()->toDateTimeString()
            ];
        }
    }

    private function createCustomMailer(Queue $queue): string {
        $queue = Queue::findOrFail($queue->id);
        $customName = 'custom_' . $queue->mailer;

        config([
            "mail.mailers.$customName" => [
                'transport' => 'smtp',
                'host' => $queue->host,
                'port' => $queue->port,
                'encryption' => $queue->encryption,
                'username' => $queue->username,
                'password' => Crypt::decryptString($queue->password),
                'timeout' => null,
                'auth_mode' => null,
            ]
        ]);

        config([
            "mail.from.address" => $queue->from_email,
            "mail.from.name" => $queue->from_name,
        ]);

        return $customName;
    }
}
