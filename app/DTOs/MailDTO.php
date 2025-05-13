<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class MailDTO {
    public object $mail;
    public array $to;
    public ?array $cc;
    public ?int $userId;

    public static function fromRequest(Request $request): self {
        $dto = new self();
        $dto->mail = (object) [
            'ticket_id' => $request->input('ticket_id'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
        ];
        $dto->to = $request->input('to', []);
        $dto->cc = $request->input('cc', []);
        $dto->userId = $request->input('user_id');

        return $dto;
    }
}
