<?php

namespace App\DTOs;

use App\Models\Ticket;

class TicketViewDTO {
    public array $ticket;
    public MailViewDTO $mailViewDTO;
    public ?array $assignedTo;
    public ?array $assignedBy;
    public ?array $queue;

    public function __construct(Ticket $ticket) {
        $this->ticket = [
            'id' => $ticket->id,
            'ref_number' => $this->generateRefNumber($ticket->id),
            'mail_id' => $ticket->mail_id,
            'queue_id' => $ticket->queue_id,
            'assigned_by' => $ticket->assigned_by,
            'assigned_to' => $ticket->assigned_to,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'split' => $ticket->split,
            'due_date' => $ticket->due_date,
        ];

        $this->mailViewDTO = new MailViewDTO($ticket->mail);

        $this->assignedTo = optional($ticket->assignedTo)?->only(['id', 'name', 'surname', 'email']);
        $this->assignedBy = optional($ticket->assignedBy)?->only(['id', 'name', 'surname', 'email']);

        $this->queue = $ticket->queue->only(['id', 'name']);
    }

    public static function fromCollection($tickets) {
        return $tickets->map(fn($ticket) => new self($ticket))->toArray();
    }

    private function generateRefNumber(int $id): string {
        return 'TKT-' . str_pad($id, 8, '0', STR_PAD_LEFT);
    }
}
