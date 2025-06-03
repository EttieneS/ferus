<?php

namespace App\Services;

use App\DTOs\AssignUsersDTO;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\DTOs\TicketViewDTO;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Illuminate\Support\Facades\Auth;

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
            Log::info($assignedTickets);
            return $assignedTickets;
        });
    }

    // public function getAllTickets(): array {
    //     $tickets = Ticket::with(['assignedTo', 'assignedBy', 'customer', 'queue'])->get();
    //     return TicketViewDTO::fromCollection($tickets);
    // }

    public function getAllTickets(): LengthAwarePaginator {
        try {
            $tickets = Ticket::with([
                'mail',
                'mail.mailBody',
                'assignedTo',
                'assignedBy',
                'queue'
            ])->paginate($this->paginationLimit);

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
        } catch (Throwable $e) {
            Log::error('Error fetching tickets: ' . $e->getMessage());
            // Optionally rethrow or return empty paginator
            throw $e;
        }
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

    public function assignUsers(AssignUsersDTO $data): array {
        DB::enableQueryLog();

        Log::info('✅ assignUsers SERVICE START', [
            'id' => $data->id,
            'assigned_to' => $data->assignedTo,
        ]);

        return DB::transaction(function () use ($data) {
            $baseTicket = Ticket::findOrFail($data->id);
            $results = [];

            foreach ($data->assignedTo as $index => $userId) {
                if ($index === 0) {
                    // ✅ Update the base ticket
                    Log::info('✏️ Updating base ticket ID ' . $baseTicket->id . ' to user ID ' . $userId);

                    $baseTicket->assigned_to = $userId;
                    $baseTicket->assigned_by = $data->assignedBy;
                    $baseTicket->save();

                    $results[] = $baseTicket->toArray();
                } else {
                    // ✅ Clone for additional users
                    Log::info('🆕 Creating ticket for user ID ' . $userId);

                    $new = Ticket::create([
                        'incoming_mail_id' => $baseTicket->mail_id,
                        'assigned_to' => $userId,
                        'assigned_by' => $data->assignedBy,
                        'queue_id' => $baseTicket->queue_id,
                        'status' => $baseTicket->status,
                        'priority' => $baseTicket->priority,
                        'due_date' => $baseTicket->due_date,
                    ]);

                    $results[] = $new->toArray();
                }
            }

            Log::info('Eloquent queries', DB::getQueryLog());

            return $results;
        });
    }



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
            ->with(['mail', 'assignedTo', 'assignedBy', 'queue'])
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

    public function getPersonalTickets($userId): LengthAwarePaginator {
        Log::info('Fetching personal tickets for user ID: ' . $userId);
        if (!$userId) {
            throw new Exception('User ID is required to fetch personal tickets.');
        }
        $tickets = Ticket::where('assigned_to', $userId)
            ->with(['mail', 'assignedTo', 'assignedBy', 'queue'])
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

    public function updatePriority(int $ticketId, int $priority): Ticket {
        $ticket = Ticket::findOrFail($ticketId);
        $ticket->priority = $priority;
        $ticket->save();

        return $ticket;
    }
}
