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
use App\Models\MailRecipient;

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
    public function send(MailDTO $dto): array {
        Log::info("send mail service");

        $ticket = Ticket::with('queue')->findOrFail($dto->ticketId);
        $queue = $ticket->queue;

        $mail = Mail::create([
            'ticket_id' => $ticket->id,
            'user_id' => $dto->fromUser ?? auth('api')->id(),
            'user_type' => 0,
            'mail_type' => 0,
            'subject' => $dto->subject,
            'body' => $dto->body,
            'is_internal' => empty($dto->toCustomers) && empty($dto->ccCustomers),
        ]);

        $recipients = collect([
            ...$dto->toUsers,
            ...$dto->ccUsers,
            ...$dto->toCustomers,
            ...$dto->ccCustomers,
        ]);

        $sent = [];

        foreach ($recipients as $recipient) {
            $email = null;
            $type = 0; // assume user
            $recipientId = null;

            if (is_numeric($recipient)) {
                $user = User::find($recipient);
                if (!$user) {
                    $customer = Customer::find($recipient);
                    if ($customer) {
                        $type = 1;
                        $email = $customer->email;
                        $recipientId = $customer->id;
                    }
                } else {
                    $email = $user->email;
                    $recipientId = $user->id;
                }
            } elseif (is_string($recipient)) {
                $email = $recipient;
                $customer = Customer::firstOrCreate(
                    ['email' => $email],
                    ['full_name' => $email]
                );
                $type = 1;
                $recipientId = $customer->id;
            }
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
