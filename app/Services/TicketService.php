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
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $tickets = Ticket::with(['incomingMail', 'assignedTo', 'assignedBy', 'queue'])
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

    public function assignUser(Ticket $ticket): JsonResponse {
        DB::beginTransaction();

        try {
            $existingTicket = Ticket::find($ticket->id);

            Log::error($existingTicket);

            if (!$existingTicket) {
                $existingTicket = new Ticket();
            }

            $existingTicket->id = $ticket->id;
            $existingTicket->assigned_to = $ticket->assigned_to;
            $existingTicket->assigned_by = $ticket->assigned_by;
            $existingTicket->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Ticket successfully assigned to user.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception("Failed to assign ticket: " . $e->getMessage());
        }
    }

    // public function getTicketsByQueue(int $queueId): LengthAwarePaginator {

    //     $tickets = Ticket::where('queue_id', $queueId)
    //         ->with(['assignedTo', 'assignedBy', 'customer', 'queue'])
    //         ->paginate(10);

    //     return new LengthAwarePaginator(
    //         TicketViewDTO::fromCollection($tickets->getCollection()),
    //         $tickets->total(),
    //         $tickets->perPage(),
    //         $tickets->currentPage(),
    //         ['path' => request()->url()]
    //     );
    // }

    public function forwardTicketToQueue(Ticket $ticket) {
        try {
            DB::beginTransaction();

            $existingTicket = Ticket::find($ticket->id);

            if (!$existingTicket) {
                throw new ModelNotFoundException('Ticket not found');
                if (!$ticket) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid ticket.',
                    ], 400);
                }
            }

            $existingTicket->queue_id = $ticket->queue_id;
            $existingTicket->save();

            DB::commit();

            return response()->json([
                'status' => 'error',
                'message' => 'Ticket sucessfully forwarded.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to forward ticket to queue', [
                'error' => $e->getMessage(),
                'ticket_id' => $ticket->id ?? 'N/A'
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while processing your request.',
            ], 500);
        }
    }

    public function getTicketsByQueue(int $queueId): LengthAwarePaginator {
        $tickets = Ticket::where('queue_id', $queueId)
            ->with(['incomingMail', 'assignedTo', 'assignedBy', 'queue'])
            ->paginate(10);

        $transformedTickets = $tickets->getCollection()->transform(function ($ticket) {
            return new TicketViewDTO($ticket);
        });

        return new LengthAwarePaginator(
            $transformedTickets,
            $tickets->total(),
            $tickets->perPage(),
            $tickets->currentPage(),
            ['path' => request()->url()]
        );
    }
}
