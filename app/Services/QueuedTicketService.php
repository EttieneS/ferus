<?php

namespace App\Services;

use App\Models\AssignedTicket;
use App\Models\Ticket;
use App\Models\QueuedTicket;
use Illuminate\Support\Facades\DB;
use App\DTOs\QueuedTicketDTO;

class QueuedTicketService {

    public function getAllQueuedTickets() {        
        $queuedTickets = QueuedTicket::with(['ticket', 'customer'])->get();
        return QueuedTicketDTO::fromCollection($queuedTickets);
    }

    public function getAssignedTicketById($id)
    {
        return QueuedTicket::with('ticket', 'customer')->findOrFail($id);
    }

    public function createAssignedTicket($data)
    {
        return QueuedTicket::create($data);
    }
}
