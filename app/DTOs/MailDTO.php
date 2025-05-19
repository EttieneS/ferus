<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class MailDTO {
    public int $ticketId;
    public ?int $fromUser = null;
    public ?int $fromCustomer = null;

    public array $toUsers = [];
    public array $ccUsers = [];
    public array $toCustomers = [];
    public array $ccCustomers = [];

    public string $subject;
    public string $body;

    public static function fromRequest(Request $request): self {
        $dto = new self;

        $dto->ticketId = $request->input('ticketId');
        $dto->fromUser = $request->input('fromUser'); // optional
        $dto->fromCustomer = $request->input('fromCustomer'); // optional

        $dto->toUsers = $request->input('toUsers', []);
        $dto->ccUsers = $request->input('ccUsers', []);
        $dto->toCustomers = $request->input('toCustomers', []);
        $dto->ccCustomers = $request->input('ccCustomers', []);

        $dto->subject = $request->input('subject', '');
        $dto->body = $request->input('body', '');

        return $dto;
    }
}
