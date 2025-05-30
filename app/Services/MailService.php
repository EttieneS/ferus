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
        Log::info(json_encode($dto) . " dto mailservice");
        $queue = $dto->queue;                
        $sent = [];
        
        $toUserEmails = [];
        $ccUserEmails = [];
        $toCustomerEmails = [];
        $ccCustomerEmails = [];
        $ccCustomerIds = []

        foreach($dto->toUsers as $id) {
            if (is_numeric($id)) {
                $user = User::find($id);
                if (!$user || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) continue;
            } 
                        
            $toUsersEmails[] = $user->email; 
        }

        foreach($dto->ccUsers as $id) {
            if (is_numeric($id)) {
                $user = User::find($id);
                if (!$user || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) continue;
            } 
                        
            $ccUserEmails[] = $user->email; 
        }
                
        foreach ($dto->toCustomers as $customerInput) {
            if (is_numeric($customerInput)) {
                $customer = Customer::find($customerInput);
                if (!$customer || !filter_var($customer->email, FILTER_VALIDATE_EMAIL)) continue;
            } else {
                if (!filter_var($customerInput, FILTER_VALIDATE_EMAIL)) continue;
                $customer = Customer::where('email', $customerInput)->first();
                if (!$customer) {
                    $customer = Customer::create([
                        'full_name' => $customer->fullname ?? self::fullNameFromEmail($customerInput),
                        'email' => $customerInput
                    ]);
                }
            }
            $toCustomerIds[] = $customer->id;
            $toCustomerEmails[] = $customer->email;
        }

        foreach ($dto->ccCustomers as $customerInput) {
            if (is_numeric($customerInput)) {
                $customer = Customer::find($customerInput);
                $ccCustomerIds[] = $customer->id;
            } else {
                if (!filter_var($customerInput, FILTER_VALIDATE_EMAIL)) continue;
                $customer = Customer::firstOrCreate([
                    'full_name' => $customer->fullname ?? self::fullNameFromEmail($customerInput),    
                    'email' => $customerInput
                ]);
            }

            if (!$customer || !filter_var($customer->email, FILTER_VALIDATE_EMAIL)) continue;

            $ccCustomerEmails[] = $customer->email;

            // $this->sendMailTo($queue, $customer->email, $dto->subject, $dto->body, $sent);
            // $this->sendMailTo($queue, "smithettiene@yahoo.com", $dto->subject, $dto->body, $sent);
        }

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
             
        $name = str_replace(['.', '_'], ' ', $name);
        return ucwords($name);
    }

    private function sendMailTo(Queue $queue, array $toEmails, array $ccEmails, string $subject, string $body, array &$sent): void {
        $toEmails = app()->environment('local') ? 'smithettiene@yahoo.com' : $toEmails;

        try {
            $mailer = app()->make(MailManager::class)->mailer(
                $customName = $this->createCustomMailer($queue)
            );

            $mailer->to($toEmails)
                ->cc($ccEmails)
                ->send(new GenericMail($subject, $body));

            Log::info("✅ Sent mail to using mailer [$customName]");

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
            Log::error("❌ Failed to send mail to $email: " . $e->getMessage());

            $sent[] = [                
                'email' => $email,                
                'status' => 'failed',
                'failed_at' => now()->toDateTimeString()
            ];
        }
    }

    private function createCustomMailer(Queue $queue): string {
        $customName = 'custom_' . $queue;
        Log::info(json_encode($queue) . " used queue");

        config([
            "mail.mailers.$queue->mailer" => [
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
