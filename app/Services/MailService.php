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
    //I'm not even hitting createCustomMailer, can you see a problem with the code?

    public function send(MailDTO $dto): array {
        Log::error(json_encode($dto) . " dto mailservice");
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
                // if (!filter_var($customerInput, FILTER_VALIDATE_EMAIL)) continue;
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


        // $toUserEmails = array_merge($toUserEmails, $toCustomerEmails);
        // $ccUserEmails = array_merge($ccUserEmails, $ccCustomerEmails);

        $toUserEmails = array_merge($toUserEmails ?? [], $toCustomerEmails ?? []);
        $ccUserEmails = array_merge($ccUserEmails ?? [], $ccCustomerEmails ?? []);

        // Log::info("To User Emails: " . json_encode($toUserEmails));
        // Log::info("CC User Emails: " . json_encode($ccUserEmails));
        // // $toUserEmails = array_unique($toUserEmails);
        // // $ccUserEmails = array_unique($ccUserEmails);

        // // Remove duplicates and ensure all emails are strings
        // $toUserEmails = array_filter($toUserEmails, fn($email) => is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL));
        // $ccUserEmails = array_filter($ccUserEmails, fn($email) => is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL));
        // Log::info("To User Emails after filter: " . json_encode($toUserEmails));
        // Log::info("CC User Emails after filter: " . json_encode($ccUserEmails));
        Log::info('About to call sendMailTo');

        $this->sendMailTo($queue, $toUserEmails, $ccUserEmails, $dto->subject, $dto->body, $sent);

        // $mail = Mail::create([
        //     'sender_id' => $dto->senderId ?? auth('api')->id(),
        //     'sender_type' => Mail::USER,
        //     'to_users' => $dto->toUsers, //json array([1 send::true], [2, send::false, )
        //     'cc_users' => $dto->ccUsers,
        //     'to_customers' => $toCustomerIds,
        //     'cc_customers' => $ccCustomerIds,
        //     'in_reply_to' => $dto->inReplyTo
        // ]);

        // $mailBody = MailBody::fromMailDTO($dto);
        // $mailBody->mail_id = $mail->id;
        // $mailBody->save();

        return $sent;
    }

    public static function fullNameFromEmail(string $email): string {

        $name = explode('@', $email)[0] ?? '';
        Log::info("Extracted name from email: " . $name);
        $name = str_replace(['.', '_'], ' ', $name);
        return ucwords($name);
    }

    // private function sendMailTo(Queue $queue, array $toEmails, array $ccEmails, string $subject, string $body, array &$sent): void {
    //     if (app()->environment('local')) {
    //         $toEmails = ['smithettiene@yahoo.com'];
    //     }

    //     try {
    //         $mailer = app()->make(MailManager::class)->mailer(
    //             $customName = $this->createCustomMailer($queue)
    //         );

    //         $mailer->to($toEmails)
    //             ->cc($ccEmails)
    //             ->send(new GenericMail($subject, $body));

    //         Log::info("✅ Sent mail to using mailer [$customName]");

    //         foreach ($toEmails as $email) {
    //             $sent[] = [
    //                 'email' => $email,
    //                 'status' => 'sent',
    //                 'sent_at' => now()->toDateTimeString()
    //             ];
    //         }

    //         foreach ($ccEmails as $email) {
    //             $sent[] = [
    //                 'email' => $email,
    //                 'status' => 'sent',
    //                 'sent_at' => now()->toDateTimeString()
    //             ];
    //         }
    //     } catch (\Throwable $e) {
    //         Log::error("❌ Failed to send mail to: " . json_encode($toEmails) . " : " . $e->getMessage());

    //         foreach ($toEmails as $email) {
    //             $sent[] = [
    //                 'email' => $email,
    //                 'status' => 'failed',
    //                 'failed_at' => now()->toDateTimeString()
    //             ];
    //         }

    //         foreach ($ccEmails as $email) {
    //             $sent[] = [
    //                 'email' => $email,
    //                 'status' => 'failed',
    //                 'failed_at' => now()->toDateTimeString()
    //             ];
    //         }
    //     }
    // }

    // private function sendMailTo(Queue $queue, array $toEmails, array $ccEmails, string $subject, string $body, array &$sent): void {
    //     if (app()->environment('local')) {
    //         if (empty($toEmails)) {
    //             $toEmails = ['smithettiene@yahoo.com'];
    //         }
    //     }

    //     try {
    //         $customName = $this->createCustomMailer($queue);
    //         Log::info('Mailer config: ', config("mail.mailers.$customName"));
    //         $mailer = app()->make(MailManager::class)->mailer($customName);

    //         Log::info("Mailer config", config("mail.mailers.$customName"));
    //         Log::info("From config", [config('mail.from.address'), config('mail.from.name')]);

    //         $mailer->to($toEmails)
    //             ->cc($ccEmails)
    //             ->send(new GenericMail($subject, $body));

    //         Log::info("✅ Sent mail to using mailer [$customName]");

    //         foreach ($toEmails as $email) {
    //             $sent[] = [
    //                 'email' => $email,
    //                 'status' => 'sent',
    //                 'sent_at' => now()->toDateTimeString()
    //             ];
    //         }

    //         foreach ($ccEmails as $email) {
    //             $sent[] = [
    //                 'email' => $email,
    //                 'status' => 'sent',
    //                 'sent_at' => now()->toDateTimeString()
    //             ];
    //         }
    //     } catch (\Throwable $e) {
    //         Log::error("❌ Failed to send mail to: " . json_encode($toEmails) . " : " . $e->getMessage());

    //         foreach (array_merge($toEmails, $ccEmails) as $email) {
    //             $sent[] = [
    //                 'email' => $email,
    //                 'status' => 'failed',
    //                 'failed_at' => now()->toDateTimeString()
    //             ];
    //         }
    //     }
    // }

    // private function createCustomMailer(Queue $queue): string {
    //     $customName = 'custom_' . $queue->id;


    //     config([
    //         "mail.mailers.$customName" => [
    //             'transport' => 'smtp',
    //             'host' => $queue->host,
    //             'port' => $queue->port,
    //             'encryption' => $queue->encryption,
    //             'username' => $queue->username,
    //             'password' => Crypt::decryptString($queue->password),
    //             'timeout' => null,
    //             'auth_mode' => null,
    //         ],
    //         "mail.from.address" => $queue->from_email,
    //         "mail.from.name" => $queue->from_name,
    //     ]);

    //     Log::info('Mailer config for ' . $customName, config("mail.mailers.$customName"));
    //     Log::info('Decrypted pass: ' . Crypt::decryptString($queue->password));


    //     return $customName;
    // }

    private function sendMailTo(Queue $queue, array $toEmails, array $ccEmails, string $subject, string $body, array &$sent): array {
        $queue = Queue::find($queue->id ?? null);

        if (!$queue) {
            Log::error("❌ Queue not found with ID: " . json_encode($queue));
            return [['error' => 'Queue not found']];
        }

        // if (app()->environment('local')) {
        //     if (empty($toEmails)) {
        //         $toEmails = ['smithettiene@yahoo.com'];
        //     }
        //     $ccEmails = []; // also clear CC in local
        // }

        try {
            $customName = $this->createCustomMailer($queue);
            Log::info('Mailer config: ', (array) config("mail.mailers.$customName"));
            Log::info('TO EMAILS:', $toEmails);
            Log::info('CC EMAILS:', $ccEmails);
            Log::info('FROM:', [$queue->from_email, $queue->from_name]);

            $mailer = app()->make(MailManager::class)->mailer($customName);

            $mailer->to($toEmails)
                ->cc($ccEmails)
                ->send(
                    (new GenericMail($subject, $body))
                        ->from($queue->from_email, $queue->from_name)
                );

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
            Log::error("❌ Failed to send mail to: " . json_encode($toEmails) . " : " . $e->getMessage());

            foreach (array_merge($toEmails, $ccEmails) as $email) {
                $sent[] = [
                    'email' => $email,
                    'status' => 'failed',
                    'failed_at' => now()->toDateTimeString()
                ];
            }
        }

        return $sent;
    }

    private function createCustomMailer(Queue $queue): string {
        $customName = 'custom_' . $queue->id;
        Log::info("📦 Creating custom mailer: $customName");
        Log::info("Password: " . $queue->password);
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

        Log::info("📦 Custom mailer [$customName] loaded");
        Log::info("🔓 Decrypted pass: " . Crypt::decryptString($queue->password));
        Log::info('Mailer config for ' . $customName, config("mail.mailers.$customName"));
        Log::info("📤 From Address: " . $queue->from_email);
        Log::info("📤 From Name: " . $queue->from_name);

        return $customName;
    }
}
