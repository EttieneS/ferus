<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\AssignedTicket;
use App\DTOs\TicketViewDTO;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;


use Exception;

class TicketService {
    private int $paginationLimit = 10;

    public function assignTicketToQueues(array $data): array {
        return DB::transaction(function () use ($data) {
            $assignedTickets = [];
            foreach ($data['queue_ids'] as $queueId) {
                $assignedTickets[] = Ticket::create([
                    'ticket_id' => $data['ticketId'],
                    'customer_id' => $data['customerId'],
                    'queue_id' => $queueId,
                    'assigned_by' => $data['assignedBy'],
                    'assigned_to' => $data['assignedBy'],
                    'status' => $data['status'] ?? 0,
                    'priority' => $data['priority'] ?? 0,
                ]);
            }
            return $assignedTickets;
        });
    }

    // public function getAllTickets(): array {
    //     $tickets = Ticket::with(['assignedTo', 'assignedBy', 'customer', 'queue'])->get();
    //     return TicketViewDTO::fromCollection($tickets);
    // }

    public function getAllTickets(): LengthAwarePaginator {
        // public function getAllTickets(): JsonResponse {
        // return response()->json(['message' => 'here']);

        $tickets = Ticket::with(['assignedTo', 'assignedBy', 'customer', 'queue'])
            ->paginate($this->paginationLimit);

        $transformedTickets = TicketViewDTO::fromCollection($tickets);

        return new LengthAwarePaginator(
            $transformedTickets,
            $tickets->total(),
            $tickets->perPage(),
            $tickets->currentPage(),
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }



    public function assignUser(Ticket $ticket): Ticket {

        DB::beginTransaction();

        try {
            $existingTicket = Ticket::find($ticket->id);

            if (!$existingTicket) {
                $existingTicket = new Ticket();
            }

            $existingTicket->id = $ticket->id;
            $existingTicket->assigned_to = $ticket->assigned_to;
            $existingTicket->assigned_by = $ticket->assigned_by;
            $existingTicket->save();

            DB::commit();

            return $existingTicket;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Failed to assign ticket: " . $e->getMessage());
        }
    }

    public function getTicketsByQueue(int $queueId): LengthAwarePaginator {

        $tickets = Ticket::where('queue_id', $queueId)
            ->with(['assignedTo', 'assignedBy', 'customer', 'queue'])
            ->paginate(10);

        return new LengthAwarePaginator(
            TicketViewDTO::fromCollection($tickets->getCollection()),
            $tickets->total(),
            $tickets->perPage(),
            $tickets->currentPage(),
            ['path' => request()->url()]
        );
    }
}
