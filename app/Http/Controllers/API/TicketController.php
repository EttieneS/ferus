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
        return response()->json(
            $this->ticketService->getAllTickets()
        );
    }

    public function assignUser(Request $request): JsonResponse {
        $ticket = Ticket::fromRequest($request);
        $response = $this->ticketService->assignUser($ticket);
        return response()->json($response);
    }

    public function getTicketsByQueue(int $queueId): JsonResponse {
        $tickets = $this->ticketService->getTicketsByQueue($queueId);
        return response()->json($tickets);
    }
}
