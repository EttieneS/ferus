<?php

namespace App\DTOs;

use App\Models\Ticket;

class TicketViewDTO {
    public array $ticket;
    public array $mailDTO;
    public ?array $assignedTo;
    public ?array $assignedBy;
    public ?array $customer;
    public ?array $queue;

    public function __construct(Ticket $ticket) {
        $this->ticket = [
            'id' => $ticket->id,
            'ref_number' => $ticket->ref_number,
            'mail_id' => $ticket->mail_id,
            'queue_id' => $ticket->queue_id,
            'assigned_by' => $ticket->assigned_by,
            'assigned_to' => $ticket->assigned_to,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'split' => $ticket->split,
            'due_date' => $ticket->due_date,
        ];

        $this->mailDTO = $ticket->mail_id ? $ticket->mail->only(['id', 'subject', 'body']) : null;
        $this->assignedTo = optional($ticket->assignedTo)?->only(['id', 'name', 'surname', 'email']);
        $this->assignedBy = optional($ticket->assignedBy)?->only(['id', 'name', 'surname', 'email']);
        $this->customer = $ticket->mail?->customer?->only(['id', 'full_name', 'email']);

        $this->queue = $ticket->queue->only(['id', 'name']);
    }

    public static function fromCollection($tickets) {
        return $tickets->map(fn($ticket) => new self($ticket))->toArray();
    }
}
