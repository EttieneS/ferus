<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;
use Webklex\PHPIMAP\Message;
use App\Models\Ticket;

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
}
