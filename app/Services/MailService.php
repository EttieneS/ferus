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
use Crypt;
use App\Models\MailMeta;

class MailService {
    public function getByTicketId(int $ticketId): array {
        return Mail::where('ticket_id', $ticketId)
            ->with(['recipients', 'senderUser', 'senderCustomer'])
            ->orderBy('created_at')
            ->get()
            ->map(function ($mail) {
                $fromModel = $mail->senderUser ?? $mail->senderCustomer;
                $from = $fromModel?->email ?? 'unknown';

                $to = $mail->recipients
                    ->where('mail_role', 'to')
                    ->map(fn($r) => $r->user->email ?? $r->customer->email ?? 'unknown')
                    ->values()
                    ->all();

                $cc = $mail->recipients
                    ->where('mail_role', 'cc')
                    ->map(fn($r) => $r->user->email ?? $r->customer->email ?? 'unknown')
                    ->values()
                    ->all();

                $mailType = $mail->user_type ?? 0;

                return new MailViewDTO(
                    mail: $mail,
                    from: $from,
                    to: $to,
                    cc: $cc,
                    mailType: $mailType
                );
            })
            ->toArray();
    }

    // public function send(MailDTO $dto): array {
    //     Log::info("send mail service ");
    //     $ticket = Ticket::with('queue')->findOrFail($dto->mail->ticket_id);
    //     $queue = $ticket->queue;

    //     $mail = Mail::create([
    //         'ticket_id' => $ticket->id,
    //         'user_id' => $dto->userId ?? auth('api')->id(),
    //         'user_type' => 0,
    //         'mail_type' => 0,
    //         'subject' => $dto->mail->subject,
    //         'body' => $dto->mail->body,
    //     ]);

    //     $recipients = collect(array_merge($dto->to, $dto->cc ?? []));
    //     $sent = [];

    //     foreach ($recipients as $recipient) {
    //         $email = null;
    //         $type = 'user';
    //         $recipientId = null;

    //         if (is_numeric($recipient)) {
    //             $user = User::find($recipient);
    //             if (!$user) {
    //                 $customer = Customer::find($recipient);
    //                 if ($customer) {
    //                     $type = 'customer';
    //                     $email = $customer->email;
    //                     $recipientId = $customer->id;
    //                 }
    //             } else {
    //                 $email = $user->email;
    //                 $recipientId = $user->id;
    //             }
    //         } elseif (is_string($recipient)) {
    //             $email = $recipient;
    //             $customer = Customer::firstOrCreate(['email' => $email], ['full_name' => $email]);
    //             $type = 'customer';
    //             $recipientId = $customer->id;
    //         }

    //         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

    //         $toEmail = app()->environment('local') ? 'smithettiene@yahoo.com' : $email;

    //         try {
    //             $mailer = app()->make(MailManager::class)->mailer(
    //                 $this->createCustomMailer($queue)
    //             );

    //             $mailer->to($toEmail)->send(new GenericMail($dto->mail->subject, $dto->mail->body));

    //             $sent[] = [
    //                 'type' => $type,
    //                 'email' => $email,
    //                 'id' => $recipientId,
    //                 'status' => 'sent',
    //                 'sent_at' => now()->toDateTimeString()
    //             ];
    //         } catch (\Throwable $e) {
    //             Log::error("❌ Failed to send mail to $email: " . $e->getMessage());
    //             $sent[] = [
    //                 'type' => $type,
    //                 'email' => $email,
    //                 'id' => $recipientId,
    //                 'status' => 'failed',
    //                 'sent_at' => null
    //             ];
    //         }
    //     }

    //     return $sent;
    // }

    // public function send(MailDTO $dto): array {
    //     Log::info("send mail service");

    //     $ticket = Ticket::with('queue')->findOrFail($dto->ticketId);
    //     $queue = $ticket->queue;

    //     $mail = Mail::create([
    //         'ticket_id' => $ticket->id,
    //         'user_id' => $dto->fromUser ?? auth('api')->id(),
    //         'user_type' => 0,
    //         'mail_type' => 0,
    //         'subject' => $dto->subject,
    //         'body' => $dto->body,
    //         'is_internal' => empty($dto->toCustomers) && empty($dto->ccCustomers),
    //     ]);

    //     $recipients = collect([
    //         ...$dto->toUsers,
    //         ...$dto->ccUsers,
    //         ...$dto->toCustomers,
    //         ...$dto->ccCustomers,
    //     ]);

    //     $sent = [];

    //     foreach ($recipients as $recipient) {
    //         $email = null;
    //         $type = 0;
    //         $recipientId = null;

    //         if (is_numeric($recipient)) {
    //             $user = User::find($recipient);
    //             if (!$user) {
    //                 $customer = Customer::find($recipient);
    //                 if ($customer) {
    //                     $type = 1;
    //                     $email = $customer->email;
    //                     $recipientId = $customer->id;
    //                 }
    //             } else {
    //                 $email = $user->email;
    //                 $recipientId = $user->id;
    //             }
    //         } elseif (is_string($recipient)) {
    //             $email = $recipient;
    //             $customer = Customer::firstOrCreate(
    //                 ['email' => $email],
    //                 ['full_name' => $email]
    //             );
    //             $type = 1;
    //             $recipientId = $customer->id;
    //         }
    //     }
    // }

    // public function send(MailDTO $dto): array {
    //     Log::info("send mail service");

    //     $ticket = Ticket::with('queue')->findOrFail($dto->ticketId);
    //     $queue = $ticket->queue;

    //     $mail = Mail::create([
    //         'ticket_id' => $ticket->id,
    //         'user_id' => $dto->fromUser ?? auth('api')->id(),
    //         'user_type' => 0,
    //         'mail_type' => 0,
    //         'subject' => $dto->subject,
    //         'body' => $dto->body,
    //         'is_internal' => empty($dto->toCustomers) && empty($dto->ccCustomers),
    //     ]);

    //     $sent = [];

    //     // user recipients
    //     foreach (array_merge($dto->toUsers, $dto->ccUsers) as $userId) {
    //         $user = User::find($userId);
    //         if (!$user || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) continue;

    //         $role = in_array($userId, $dto->ccUsers) ? 'cc' : 'to';

    //         MailRecipient::create([
    //             'mail_id' => $mail->id,
    //             'recipient_id' => $user->id,
    //             'recipient_type' => 0,
    //             'recipient_role' => $role,
    //         ]);

    //         if (!$mail->is_internal) {
    //             $this->sendMailTo($queue, $user->email, $dto->subject, $dto->body, $sent, 0, $user->id);
    //         }
    //     }

    //     // customer recipients
    //     foreach (array_merge($dto->toCustomers, $dto->ccCustomers) as $entry) {
    //         $email = is_string($entry) ? $entry : null;
    //         $id = is_numeric($entry) ? $entry : null;

    //         if ($id) {
    //             $customer = Customer::find($id);
    //             if (!$customer || !filter_var($customer->email, FILTER_VALIDATE_EMAIL)) continue;
    //             $email = $customer->email;
    //         }

    //         if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

    //         $role = in_array($entry, $dto->ccCustomers) ? 'cc' : 'to';

    //         $customer = isset($customer)
    //             ? $customer
    //             : Customer::firstOrCreate(['email' => $email], ['full_name' => $email]);

    //         MailRecipient::create([
    //             'mail_id' => $mail->id,
    //             'recipient_id' => $customer->id,
    //             'recipient_type' => 1,
    //             'recipient_role' => $role,
    //         ]);

    //         if (!$mail->is_internal) {
    //             $this->sendMailTo($queue, $email, $dto->subject, $dto->body, $sent, 1, $customer->id);
    //         }
    //     }

    //     return $sent;
    // }

    public function send(MailDTO $dto): array {
        $ticket = Ticket::with('queue')->findOrFail($dto->ticketId);
        $queue = $ticket->queue;

        $mail = Mail::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $dto->fromUser ?? auth('api')->id(),
            'subject' => $dto->subject,
            'body' => $dto->body,
            'in_reply_to' => $dto->inReplyTo,
            'is_internal' => empty($dto->toCustomers) && empty($dto->ccCustomers) && empty($dto->ccUsers),
        ]);

        $sent = [];

        // Resolve ccCustomer wildcard emails into customer IDs
        $resolvedCcCustomerIds = [];
        foreach ($dto->ccCustomers as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

            $customer = Customer::firstOrCreate(
                ['email' => $email],
                ['full_name' => $email]
            );

            if ($customer) {
                $resolvedCcCustomerIds[] = $customer->id;

                if (!$mail->is_internal) {
                    $this->sendMailTo($queue, $customer->email, $dto->subject, $dto->body, $sent, MailMeta::RECIPIENT_TYPE_CUSTOMER, $customer->id);
                }
            }
        }

        // Send toUsers and ccUsers if external
        foreach (array_merge($dto->toUsers, $dto->ccUsers) as $userId) {
            $user = User::find($userId);
            if (!$user || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) continue;

            if (!$mail->is_internal) {
                $this->sendMailTo($queue, $user->email, $dto->subject, $dto->body, $sent, MailMeta::RECIPIENT_TYPE_USER, $user->id);
            }
        }

        // Send toCustomers (known IDs)
        foreach ($dto->toCustomers as $customerId) {
            $customer = Customer::find($customerId);
            if (!$customer || !filter_var($customer->email, FILTER_VALIDATE_EMAIL)) continue;

            if (!$mail->is_internal) {
                $this->sendMailTo($queue, $customer->email, $dto->subject, $dto->body, $sent, MailMeta::RECIPIENT_TYPE_CUSTOMER, $customer->id);
            }
        }

        // Save MailMeta with final resolved arrays
        MailMeta::create([
            'mail_id' => $mail->id,
            'in_reply_to' => $dto->inReplyTo,
            'toUsers' => $dto->toUsers,
            'ccUsers' => $dto->ccUsers,
            'toCustomers' => $dto->toCustomers,
            'ccCustomers' => $resolvedCcCustomerIds,
        ]);

        return $sent;
    }


    private function sendMailTo(Queue $queue, string $email, string $subject, string $body, array &$sent, int $type, int $id): void {
        $toEmail = app()->environment('local') ? 'smithettiene@yahoo.com' : $email;

        try {
            $mailer = app()->make(MailManager::class)->mailer(
                $this->createCustomMailer($queue)
            );

            $mailer->to($toEmail)->send(new GenericMail($subject, $body));

            $sent[] = [
                'type' => $type,
                'email' => $email,
                'id' => $id,
                'status' => 'sent',
                'sent_at' => now()->toDateTimeString()
            ];
        } catch (\Throwable $e) {
            Log::error("❌ Failed to send mail to $email: " . $e->getMessage());

            $sent[] = [
                'type' => $type,
                'email' => $email,
                'id' => $id,
                'status' => 'failed',
                'sent_at' => null
            ];
        }
    }

    private function createCustomMailer(Queue $queue): string {
        $customName = 'custom_' . $queue->id;

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
            ],
            "mail.from.address" => $queue->from_email,
            "mail.from.name" => $queue->from_name,
        ]);

        return $customName;
    }
}
