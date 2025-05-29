<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class MailDTO {    
    public int $ticketId;
    public int $mailId;
    public ?int $senderId = null;    
    public ?int $senderType = null;

    public ?int $queue = null;

    public array $toUsers = [];
    public array $ccUsers = [];
    public array $toCustomers = [];
    public array $ccCustomers = [];

    public string $subject;
    public string $body;
    public ?int $inReplyTo;

    public static function fromRequest(Request $request): self {
        $dto = new self;

        $dto->mailId = $request->input('mail_id');
        $dto->ticketId = $request->input('ticket_id');        
        $dto->senderId = $request->input('sender_id');
        $dto->senderType = $request->input['sender_type'];
        $dto->queue = $request->input['queue'];

        $dto->toUsers = $request->input('to_users', []);
        $dto->ccUsers = $request->input('cc_users', []);
        $dto->toCustomers = $request->input('to_customers', []);
        $dto->ccCustomers = $request->input('cc_customers', []);
        
        $dto->subject = $request->input('subject', '');
        $dto->body = $request->input('body', '');
        $dto->inReplyTo = $request->input('in_reply_to', '');

        return $dto;
    }
}
