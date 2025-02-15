<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketController extends BaseController {
    
    public function index(): JsonResponse {
        $tickets = Ticket::all();

        return $this->sendResponse(new TicketResource($tickets), 'All tickets returned.');
    }
        
    public function create(Request $request): JsonResponse {
        $ticket = $request->all();

        Ticket::create($ticket);

        return $this->sendResponse('success', 'Ticket created successfully.');
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
