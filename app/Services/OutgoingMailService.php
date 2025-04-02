<?php

namespace App\Services;

use App\Models\OutgoingMail;
use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class OutgoingMailService {
    public function send(array $data): void {
        $ticket = Ticket::with(['queue', 'incomingMail.customer'])->findOrFail($data['ticket_id']);
        $mailer = $ticket->queue->mailer ?? config('mail.default');
        $toEmail = $ticket->incomingMail->customer->email;

        Mail::mailer($mailer)->send([], [], function ($message) use ($data, $toEmail) {
            $message->to($toEmail)
                ->subject($data['subject'])
                ->html($data['body']);
        });

        // return OutgoingMail::create([
        //     'ticket_id' => $ticket->id,
        //     'user_id' => $data['user_id'] ?? null,
        //     'subject' => $data['subject'],
        //     'body' => $data['body'],
        //     'sent_at' => now(),
        // ]);
    }
}
