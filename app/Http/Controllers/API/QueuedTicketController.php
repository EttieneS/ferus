<?php

namespace App\Http\Controllers\API;

use App\Services\QueuedTicketService;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

class QueuedTicketController extends BaseController {
    protected $queuedTicketService;

    public function __construct(QueuedTicketService $queuedTicketService) {
        $this->queuedTicketService = $queuedTicketService;
    }
    
    public function index() {        
        return response()->json($this->queuedTicketService->getAllQueuedTickets());
    }

    public function show($id) {
        return response()->json($this->queuedTicketService->getAssignedTicketById($id));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'queue_id' => 'required|exists:queues,id',
            'priority' => 'integer',
            'status' => 'integer'
        ]);
        return response()->json($this->queuedTicketService->createAssignedTicket($data));
    }
}
