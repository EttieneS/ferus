<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use App\Services\TicketService;

class TicketController extends BaseController {
    protected TicketService $ticketService;

    public function __construct(TicketService $ticketService) {
        $this->ticketService = $ticketService;
    }

    public function index(): JsonResponse {
        $tickets = Ticket::all();

        return $this->sendResponse(new TicketResource($tickets), 'All tickets returned.');
    }
        
    public function create(Request $request): JsonResponse {
        $ticket = $request->all();

        Ticket::create($ticket);

        return $this->sendResponse('success', 'Ticket created successfully.');
    }    

    public function assignTicket(Request $request): JsonResponse {
        // $validatedData = $request->validate([
        //     'ticket_id' => 'required|integer|exists:tickets,id',
        //     'queue_ids' => 'required|array',
        //     'queue_ids.*' => 'integer|exists:queues,id',
        //     'assigned_by' => 'required|integer|exists:users,id',
        //     'status' => 'nullable|string|max:255',
        //     'priority' => 'nullable|string|max:255'
        // ]);

        $assignedTickets = $this->ticketService->assignTicketToQueue($request);

        return response()->json([
            'message' => 'Ticket assigned successfully!',
            'data' => $assignedTickets
        ], 201);
    }

    public function assignTo(Request $request): JsonResponse {
        return $this->sendResponse('success', 'Ticket created successfully.');
    }

    public function getByQueue(Request $request): JsonResponse {
        $queueId = $request['queue_id'];
        
        $tickets = DB::table('tickets')
            ->where('queue_id', '=', $queueId)
            ->get();


        return $this->sendResponse(new TicketResource($tickets), 'All tickets returned.');        
    }
}
