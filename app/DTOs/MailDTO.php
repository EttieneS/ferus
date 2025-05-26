<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class MailDTO {
    public int $ticketId;
    public ?int $fromUser = null;
    public ?int $fromCustomer = null;
    public ?int $sendType = null; //0 incoming, 1 outgoing

    public array $toUsers = [];
    public array $ccUsers = [];
    public array $toCustomers = [];
    public array $ccCustomers = [];

    public string $subject;
    public string $body;
    public ?int $inReplyTo;

    public static function fromRequest(Request $request): self {
        $dto = new self;

        $dto->ticketId = $request->input('ticket_id');
        $dto->fromUser = $request->input('from_user');
        $dto->fromCustomer = $request->input('from_customer');

        $dto->toUsers = $request->input('to_users', []);
        $dto->ccUsers = $request->input('cc_users', []);
        $dto->toCustomers = $request->input('to_customers', []);
        $dto->ccCustomers = $request->input('cc_customers', []);

        $dto->sendType = $request->input('send_type');
        $dto->subject = $request->input('subject', '');
        $dto->body = $request->input('body', '');
        $dto->inReplyTo = $request->input('in_reply_to', '');

        return $dto;
    }
}
