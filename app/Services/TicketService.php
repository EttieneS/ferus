<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\AssignedTicket;
use Exception;

class TicketService {
    public function assignTicketToQueues(array $data): array {
        return DB::transaction(function () use ($data) {
            $assignedTickets = [];
            foreach ($data['queue_ids'] as $queueId) {
                $assignedTickets[] = AssignedTicket::create([
                    'ticket_id' => $data['ticket_id'],
                    'queue_id' => $queueId,
                    'assigned_by' => $data['assigned_by'],
                    'assigned_to' => $data['assigned_to'],
                    'status' => $data['status'] ?? 'pending',
                    'priority' => $data['priority'] ?? 'normal',
                ]);
            }
            return $assignedTickets;
        });
    }
}
