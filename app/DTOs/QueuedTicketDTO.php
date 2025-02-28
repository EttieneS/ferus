<?php

namespace App\DTOs;

class QueuedTicketDTO {
    public $id;
    public $queueId;
    public $status;
    public $priority;
    public $createdAt;
    public $updatedAt;

    public $ticketId;
    public $subject;
    public $body;

    public $customerId;
    public $customerName;
    public $customerEmail;

    public function __construct($queuedTicket) {
        $this->id = $queuedTicket->id;
        $this->queueId = $queuedTicket->queue_id;
        $this->status = $queuedTicket->status;
        $this->priority = $queuedTicket->priority;
        $this->createdAt = $queuedTicket->created_at;
        $this->updatedAt = $queuedTicket->updated_at;

        // Ticket details
        $this->ticketId = $queuedTicket->ticket->id ?? null;
        $this->subject = $queuedTicket->ticket->subject ?? null;
        $this->body = $queuedTicket->ticket->body ?? null;

        // Customer details
        $this->customerId = $queuedTicket->customer->id ?? null;
        $this->customerName = $queuedTicket->customer->full_name ?? null;
        $this->customerEmail = $queuedTicket->customer->email ?? null;
    }

    public static function fromCollection($queuedTickets) {
        return $queuedTickets->map(fn($queuedTicket) => new self($queuedTicket));
    }
}
