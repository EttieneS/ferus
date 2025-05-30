<?php

namespace App\DTOs;

use App\Models\Mail;
use Illuminate\Support\Carbon;

class MailViewDTO {
    public int $id;
    public int $ticketId;
    public array $sender;
    public array $to;
    public array $cc;
    public string $subject;
    public string $body;
    public string $sentAt;

    public function __construct(
        Mail $mail,
    ) {
        $this->id = $mail->id;
        $this->subject = $mail->mailBody->subject;
        $this->body = $mail->mailBody->body;
        $this->sender = [$mail->getSenderDetails()];
        $this->to = array_merge([$mail->toUsers()], [$mail->toCustomers()]);
        $this->cc = array_merge([$mail->ccUsers()], [$mail->ccCustomers()]);
        $this->sentAt = Carbon::parse($mail->created_at)->toISOString();
    }
}
