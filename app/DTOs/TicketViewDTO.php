<?php

namespace App\DTOs;

use App\Models\Ticket;

class TicketViewDTO {
    public ?array $ticket;
    public ?array $mail;
    public ?array $assignedTo;
    public ?array $assignedBy;
    public ?array $customer;
    public ?array $queue;

    public function __construct(Ticket $ticket) {
        $this->ticket = $ticket->only([
            'id',
            'mail_id',
            'queue_id',
            'assigned_by',
            'assigned_to',
            'status',
            'priority',
            'split'
        ]);
        $this->mail = optional($ticket->mail)?->only(['id', 'subject', 'message']);
        $this->assignedTo = optional($ticket->assignedTo)?->only(['id', 'name', 'surname', 'email']);
        $this->assignedBy = optional($ticket->assignedBy)?->only(['id', 'name', 'surname', 'email']);
        $this->customer = optional($ticket->customer)?->only(['id', 'full_name', 'email']);
        $this->queue = optional($ticket->queue)?->only(['id', 'name']);
    }

    public static function fromCollection($tickets) {
        return $tickets->map(fn($ticket) => new self($ticket))->toArray();
    }
}
