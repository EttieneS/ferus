<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Queue;
class MailDTO {    
    public ?int $ticketId;
    public ?int $mailId;
    public int $senderId;    
    public int $senderType;

    public ?Queue $queue = null;

    public ?array $toUsers = [];
    public ?array $ccUsers = [];
    public ?array $toCustomers = [];
    public ?array $ccCustomers = [];

    public string $subject;
    public string $body;
    public ?int $inReplyTo;

    public static function fromRequest(Request $request): self {
        Log::info(json_encode($request) . " :request");
        
        $dto = new self;
        $dto->ticketId = $request->has('ticket_id')
            ? (int) $request->input('ticket_id')
            : null;
        $dto->mailId = $request->has('mail_id')
            ? (int) $request->input('mail_id')
            : null;
        $dto->senderId = $request->input('sender_id');
        $dto->senderType = $request->input('sender_type');
        $dto->queue = Queue::fromArray($request->input('queue'));

        $dto->toUsers = $request->input('to_users', []);
        $dto->ccUsers = $request->input('cc_users', []);
        $dto->toCustomers = $request->input('to_customers', []);
        $dto->ccCustomers = $request->input('cc_customers', []);
        
        $dto->subject = $request->input('subject', '');
        $dto->body = $request->input('body', '');
        $dto->inReplyTo = $request->has('in_reply_to')
            ? (int) $request->input('in_reply_to')
            : null;

        return $dto;
    }
}
