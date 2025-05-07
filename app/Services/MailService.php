<?php

namespace App\Services;

use App\Models\Mail;
use App\DTOs\MailViewDTO;

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

                $mailType = $mail->user_type ?? 0; // 0 = outgoing, 1 = incoming

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
}
