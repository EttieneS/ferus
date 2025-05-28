<?php

namespace App\DTOs;

use App\Models\Mail;
use Illuminate\Support\Carbon;

class MailViewDTO {
    public int $id;
    public int $ticketId;
    public string $from;
    public array $to;
    public array $cc;
    public string $subject;
    public string $body;
    public string $sentAt;
    public int $mailType; // 0 = outgoing, 1 = incoming

    public function __construct(
        Mail $mail,
        string $from,
        array $to,
        array $cc,
        int $mailType
    ) {
        $this->id = $mail->id;
        $this->subject = $mail->mailBody->subject;
        $this->body = $mail->mailBody->body;
        $this->from = $from;
        $this->to = $to;
        $this->cc = $cc;
        $this->mailType = $mailType;
        $this->sentAt = Carbon::parse($mail->created_at)->toISOString();
    }
}
