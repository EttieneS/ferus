<?php

namespace App\DTOs;

use App\Models\Ticket;

class TicketViewDTO {
    public array $ticket;
    public array $incomingMail;
    public ?array $assignedTo;
    public ?array $assignedBy;
    public ?array $customer;
    public ?array $queue;

    public function __construct(Ticket $ticket) {
        $this->ticket = $ticket->only([
            'id',
            'incoming_mail_id',
            'queue_id',
            'assigned_by',
            'assigned_to',
            'status',
            'priority',
            'split'
        ]);

        $this->incomingMail = $ticket->incomingMail ? $ticket->incomingMail->only(['id', 'subject', 'body']) : null;
        $this->assignedTo = optional($ticket->assignedTo)?->only(['id', 'name', 'surname', 'email']);
        $this->assignedBy = optional($ticket->assignedBy)?->only(['id', 'name', 'surname', 'email']);
        $this->customer = $ticket->incomingMail->customer->only(['id', 'full_name', 'email']);
        $this->queue = $ticket->queue->only(['id', 'name']);
    }

    public static function fromCollection($tickets) {
        return $tickets->map(fn($ticket) => new self($ticket))->toArray();
    }
}
