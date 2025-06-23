<?php
namespace App\Http\Controllers\API;

use App\Models\Mail;
use App\Models\Note;
use App\Services\ThreadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketThreadController extends BaseController {
    
    public function __construct(
        private ThreadService $threadService
    ) {}

    public function index(Request $request): JsonResponse {
        $threadItemDTO = [
            'ticketId' => $request->input('ticket_id'),
            'mailId' => $request->input('mail_id')
        ];

        $threadItemsObject = $this->threadService->getMailsAndNotesByTicketId($threadItemDTO);
        $threadItems = (array) $threadItemsObject;

        $mails = $threadItems['mails'] ?? collect();
        $notes = $threadItems['notes'] ?? collect();

        $threads = $mails->concat($notes)->sortBy('created_at')->values();

        return $this->sendResponse($threadItemDTO, $threads);
    }
}
