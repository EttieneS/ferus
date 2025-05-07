<?php

namespace App\Services;

use App\DTOs\OutgoingMailDTO;
use App\Models\OutgoingMail;
use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Mail\GenericMail;
use Illuminate\Support\Facades\Log;
use App\Models\OutgoingMailRecipient;
use Illuminate\Support\Facades\Auth;

class OutgoingMailService {
    protected function resolveEmails(array $list): array {
        return collect($list)->map(function ($entry) {
            if (is_numeric($entry)) {
                $user = \App\Models\User::find($entry);
                return $user?->email;
            }
            return $entry;
        })->filter()->unique()->values()->all();
    }

    // public function send(OutgoingMailDTO $dto): array {
    //     $ticket = Ticket::with(['queue'])->findOrFail($dto->outgoingMail->ticket_id);
    //     $mailer = $ticket->queue->mailer ?? config('mail.default');

    //     $mail = OutgoingMail::create([
    //         'ticket_id' => $ticket->id,
    //         'user_id' => $dto->userId ?? Auth::id(),
    //         'subject' => $dto->subject,
    //         'body' => $dto->body,
    //     ]);

    //     $toRecipients = collect($dto->to)->map(fn($r) => is_numeric($r) ? User::find($r) : $r)->filter()->map(fn($r) => ['model' => $r, 'type' => 'to']);
    //     $ccRecipients = collect($dto->cc)->map(fn($r) => is_numeric($r) ? User::find($r) : $r)->filter()->map(fn($r) => ['model' => $r, 'type' => 'cc']);

    //     $all = $toRecipients->concat($ccRecipients);
    //     $sent = [];


    //     foreach ($all as $recipientData) {
    //         $recipient = $recipientData['model'];
    //         $type = $recipientData['type'];
    //         $email = $recipient instanceof User ? $recipient->email : $recipient;

    //         if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

    //         $toEmail = app()->environment('local') ? 'smithettiene@yahoo.com' : $email;

    //         $uniqueHash = strtoupper(substr(md5($email . microtime()), 0, 6));
    //         $subject = $dto->subject . " [$uniqueHash]";
    //         $body = $dto->body;

    //         if (app()->environment('local')) {
    //             $body .= "<br><br><small>Original: {$email}</small>";
    //         }

    //         try {
    //             Mail::mailer($mailer)->to($toEmail)->send(new GenericMail($subject, $body));
    //             Log::info("✅ Sent to {$toEmail} via [{$mailer}]");

    //             OutgoingMailRecipient::fromOutgoingMail($mail, $recipient, $type)->markSent();
    //             $sent[] = $toEmail;
    //         } catch (\Throwable $e) {
    //             Log::error("❌ Failed to send to {$toEmail} via [{$mailer}]: " . $e->getMessage());
    //             OutgoingMailRecipient::fromOutgoingMail($mail, $recipient, $type)->markFailed();
    //         }
    //     }

    //     return [
    //         'mail_id' => $mail->id,
    //         'recipients' => $mail->recipients()->get()->map(fn($r) => [
    //             'type' => $r->recipient_type,
    //             'email' => $r->recipient_email,
    //             'user_id' => $r->recipient_id,
    //             'status' => $r->status,
    //             'sent_at' => $r->created_at?->toDateTimeString(),
    //         ])
    //     ];
    // }

    // public function send(OutgoingMailDTO $dto): array {
    //     $ticket = Ticket::with(['queue'])->findOrFail($dto->outgoingMail->ticket_id);
    //     $mailer = $ticket->queue->mailer ?? config('mail.default');

    //     $mail = OutgoingMail::create([
    //         'ticket_id' => $ticket->id,
    //         'user_id' => $dto->userId ?? Auth::id(),
    //         'subject' => $dto->subject,
    //         'body' => $dto->body,
    //     ]);

    //     $toRecipients = collect($dto->to)
    //         ->map(fn($r) => is_numeric($r) ? User::find($r) : $r)
    //         ->filter()
    //         ->map(fn($r) => ['model' => $r, 'type' => 'to']);

    //     $ccRecipients = collect($dto->cc)
    //         ->map(fn($r) => is_numeric($r) ? User::find($r) : $r)
    //         ->filter()
    //         ->map(fn($r) => ['model' => $r, 'type' => 'cc']);

    //     foreach ($toRecipients as $r) {
    //         $this->sendToRecipient($mail, $r['model'], 'to', $dto->subject, $dto->body, $mailer);
    //     }

    //     foreach ($ccRecipients as $r) {
    //         $this->sendToRecipient($mail, $r['model'], 'cc', $dto->subject, $dto->body, $mailer);
    //     }

    //     return [
    //         'mail_id' => $mail->id,
    //         'recipients' => $mail->recipients()->get()->map(fn($r) => [
    //             'type' => $r->recipient_type,
    //             'email' => $r->recipient_email,
    //             'user_id' => $r->recipient_id,
    //             'status' => $r->status,
    //             'sent_at' => $r->created_at?->toDateTimeString(),
    //         ])
    //     ];
    // }

    // public function send(OutgoingMailDTO $dto): array {
    //     $ticket = Ticket::with(['queue'])->findOrFail($dto->outgoingMail->ticket_id);
    //     $mailer = $ticket->queue->mailer ?? config('mail.default');

    //     $mail = OutgoingMail::create([
    //         'ticket_id' => $ticket->id,
    //         'user_id' => $dto->userId ?? Auth::id(),
    //         'subject' => $dto->subject,
    //         'body' => $dto->body,
    //     ]);

    //     $recipients = collect(array_merge($dto->to, $dto->cc ?? []));
    //     $mappedRecipients = collect();

    //     foreach ($recipients as $recipient) {
    //         $model = null;

    //         if (is_numeric($recipient)) {
    //             $model = User::find($recipient);
    //         } elseif (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    //             $model = Customer::firstOrCreate(['email' => $recipient], [
    //                 'full_name' => 'Unknown',
    //                 'email' => $recipient,
    //             ]);
    //         }

    //         if (!$model || !filter_var($model->email, FILTER_VALIDATE_EMAIL)) continue;

    //         $toEmail = app()->environment('local') ? 'smithettiene@yahoo.com' : $model->email;
    //         $uniqueHash = strtoupper(substr(md5($model->email . microtime()), 0, 6));
    //         $subject = $dto->subject . " [$uniqueHash]";
    //         $body = $dto->body;

    //         if (app()->environment('local')) {
    //             $body .= "<br><br><small>Original: {$model->email}</small>";
    //         }

    //         try {
    //             Mail::mailer($mailer)->to($toEmail)->send(new GenericMail($subject, $body));
    //             Log::info("✅ Sent to {$toEmail} via [{$mailer}]");
    //         } catch (\Throwable $e) {
    //             Log::error("❌ Failed to send to {$toEmail} via [{$mailer}]: " . $e->getMessage());
    //         }

    //         $mappedRecipients->push([
    //             'type' => $model instanceof Customer ? 'customer' : 'user',
    //             'email' => $model->email,
    //             'id' => $model->id,
    //             'status' => 'sent',
    //             'sent_at' => now()->toDateTimeString(),
    //         ]);
    //     }

    //     return [
    //         'mail_id' => $mail->id,
    //         'recipients' => $mappedRecipients
    //     ];
    // }

    public function send(OutgoingMailDTO $dto): array {
        $ticket = Ticket::with('queue')->findOrFail($dto->outgoingMail->ticket_id);
        $mailer = $ticket->queue->mailer ?? config('mail.default');

        $mail = OutgoingMail::create([
            'ticket_id' => $ticket->id,
            'user_id' => $dto->userId ?? Auth::id(),
            'subject' => $dto->subject,
            'body' => $dto->body,
        ]);

        $recipients = collect(array_merge($dto->to, $dto->cc ?? []));
        $sent = [];

        foreach ($recipients as $recipient) {
            $email = null;
            $type = 'user';
            $recipientId = null;

            if (is_numeric($recipient)) {
                $user = User::find($recipient);
                if (!$user) {
                    $customer = Customer::find($recipient);
                    if ($customer) {
                        $type = 'customer';
                        $email = $customer->email;
                        $recipientId = $customer->id;
                    }
                } else {
                    $email = $user->email;
                    $recipientId = $user->id;
                }
            } elseif (is_string($recipient)) {
                $email = $recipient;
                $customer = Customer::firstOrCreate(['email' => $email], ['full_name' => $email]);
                $type = 'customer';
                $recipientId = $customer->id;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

            $toEmail = app()->environment('local') ? 'smithettiene@yahoo.com' : $email;

            try {
                Mail::mailer($mailer)->to($toEmail)->send(new GenericMail($dto->subject, $dto->body));
                $sent[] = ['type' => $type, 'email' => $email, 'id' => $recipientId, 'status' => 'sent', 'sent_at' => now()->toDateTimeString()];
            } catch (\Throwable $e) {
                Log::error("❌ Failed to send mail to $email: " . $e->getMessage());
                $sent[] = ['type' => $type, 'email' => $email, 'id' => $recipientId, 'status' => 'failed', 'sent_at' => null];
            }
        }

        return ['mail_id' => $mail->id, 'recipients' => $sent];
    }



    // private function sendToRecipient(OutgoingMail $mail, mixed $recipient, string $type, string $subject, string $body, string $mailer): void {
    //     $email = $recipient instanceof User ? $recipient->email : $recipient;
    //     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return;

    //     $toEmail = app()->environment('local') ? 'smithettiene@yahoo.com' : $email;
    //     $hash = strtoupper(substr(md5($email . microtime()), 0, 6));
    //     $finalSubject = $subject . " [$hash]";
    //     $finalBody = $body . (app()->environment('local') ? "<br><br><small>Original: {$email}</small>" : '');

    //     try {
    //         Mail::mailer($mailer)->to($toEmail)->send(new GenericMail($finalSubject, $finalBody));
    //         Log::info("✅ Sent to {$toEmail} via [{$mailer}]");
    //         OutgoingMailRecipient::fromOutgoingMail($mail, $recipient, $type)->markSent();
    //     } catch (\Throwable $e) {
    //         Log::error("❌ Failed to send to {$toEmail}: " . $e->getMessage());
    //         OutgoingMailRecipient::fromOutgoingMail($mail, $recipient, $type)->markFailed();
    //     }
    // }

    // public function getByTicketId(int $ticketId): array {
    //     return OutgoingMail::where('ticket_id', $ticketId)
    //         ->with('user')
    //         ->orderByDesc('created_at')
    //         ->get()
    //         ->map(function ($mail) {
    //             return new OutgoingMailDTO(
    //                 outgoingMail: $mail,
    //                 to: $this->extractRecipients($mail, 'to'),
    //                 cc: $this->extractRecipients($mail, 'cc'),
    //                 subject: $mail->subject,
    //                 body: $mail->body,
    //                 userId: $mail->user_id
    //             );
    //         })
    //         ->toArray();
    // }

    // private function extractRecipients(OutgoingMail $mail, string $type): array {
    //     return $mail->$type ?? [];
    // }

    public function getByTicketId(int $ticketId): array {
        return OutgoingMail::where('ticket_id', $ticketId)
            ->withTrashed() // if soft-deleted mails are allowed
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($mail) {
                return new OutgoingMailDTO(
                    outgoingMail: $mail,
                    to: $this->extractRecipients($mail, 'to'),
                    cc: $this->extractRecipients($mail, 'cc'),
                    subject: $mail->subject,
                    body: $mail->body,
                    userId: $mail->user_id
                );
            })
            ->toArray();
    }

    private function extractRecipients(OutgoingMail $mail, string $type): array {
        return \DB::table('outgoing_mail_recipients')
            ->where('outgoing_mail_id', $mail->id)
            ->where('mail_type', $type)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($r) {
                if ($r->recipient_type === 1) {
                    return User::find($r->recipient_id)?->email;
                } elseif ($r->recipient_type === 2) {
                    return Customer::find($r->recipient_id)?->email;
                }
                return null;
            })
            ->filter()
            ->toArray();
    }
}
