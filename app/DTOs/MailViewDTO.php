<?php

namespace App\DTOs;

use App\Models\Mail;
use Illuminate\Support\Carbon;

class MailViewDTO {
    public int $id;    
    public array $sender;    
    public array $toUsers;
    public array $ccUsers;
    public array $toCustomers;
    public array $ccCustomers;
    public string $subject;
    public string $body;
    public string $sentAt;

    public function __construct(
        Mail $mail,
    ) {
        $this->id = $mail->id;
        $this->sender = $mail->getSenderDetails();
        $this->toUsers = $mail->to_users ?? [];
        $this->ccUsers = $mail->cc_users ?? [];
        $this->toCustomers = $mail->to_customers ?? [];
        $this->ccCustomers = $mail->cc_customers ?? [];
        $this->subject = $mail->mailBody->subject;
        $this->body = $mail->mailBody->body;
        $this->sentAt = Carbon::parse($mail->created_at)->toISOString();
    }
}
