<?php

namespace App\DTOs;

use App\Models\OutgoingMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutgoingMailDTO {
    public OutgoingMail $outgoingMail;
    public array $to;
    public ?array $cc;
    public int $from;
    public string $subject;
    public string $body;
    public ?int $userId;

    public function __construct(
        OutgoingMail $outgoingMail,
        array $to,
        ?array $cc,
        string $subject,
        string $body,
        int $userId
    ) {
        $this->outgoingMail = $outgoingMail;
        $this->to = $to;
        $this->cc = $cc;
        $this->subject = $subject;
        $this->body = $body;
        $this->userId = $userId;
    }

    public static function fromRequest(Request $request): self {
        $outgoingMail = new OutgoingMail();
        $outgoingMail->fromDtoRequest($request);

        $user = $request->user();
        $userId = $user?->id ?? 1;

        $outgoingMail->ticket_id = $request->input('ticket_id');
        $outgoingMail->user_id = $userId;
        $outgoingMail->user_type = 0;

        $to = $request->input('to', []);
        $cc = $request->input('cc', []);

        return new self(
            outgoingMail: $outgoingMail,
            to: is_array($to) ? $to : [$to],
            cc: is_array($cc) ? $cc : (is_string($cc) ? explode(',', $cc) : null),
            subject: $request->input('subject', 'No Subject'),
            body: $request->input('body', ''),
            userId: $userId
        );
    }
}
