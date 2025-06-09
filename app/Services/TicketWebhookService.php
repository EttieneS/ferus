<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Collection;

class TicketWebhookService {
    
    public function getOpenTicketCount(): int {
        return Ticket::with(['queue', 'assignedTo', 'customer'])
            ->count();
    }

    public function getPersonalTicketCount($userId): int {
        return Ticket::with(['assignedTo'])
            ->where('assigned_to', $userId)
            ->count();
    }
}
