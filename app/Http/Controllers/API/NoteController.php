<?php

namespace App\Http\Controllers\API;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Services\NoteService;
use Illuminate\Support\Facades\Log;

class NoteController extends BaseController {
    public function __construct(
        private NoteService $noteService,
    ) {
        
    }
    public function store(Request $request) {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'body' => 'required|string',
        ]);

        $note = Note::fromRequest($request);
        $response = $this->noteService->create($note);

        if ($response['status'] == "success") {
            return $this->sendResponse("", "Note created");
        } elseif ($response['status'] == "error") {            
            Log::error(json_encode($response));            
            return $this->sendError("Failed to create note", [], 500, $response['message']);
        }

    }

    public function getAllByTicketId(Request $request) {
        // $request->validate([
            //     'ticket_id' => 'required|exists:tickets,id',
            // ]);
            
        $ticketId = $request->input('ticket_id');
        $notes = $this->noteService->getAllByTicketId($ticketId);
        Log::error("lOG GET ALL NOTES BY TICKET ID IN NOT E CONTROLER:" . json_encode($notes));
            
        return $this->sendResponse($notes, "Notes retrieved successfully.");
    }
}
