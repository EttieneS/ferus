<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\TicketWebhookService;

class TicketWebhookController extends Controller {

    protected $ticketWebhookService;
    
    public function __construct(TicketWebhookService $ticketWebhookService) {
        $this->ticketWebhookService = $ticketWebhookService;
    }

    public function getOpenTicketCount(Request $request) {
        $secret = $request->header('X-Melio-Webhook-Secret');
        if ($secret !== config('webhook.secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $count = $this->ticketWebhookService->getOpenTicketCount();

        return response()->json(['open_tickets' => $count]);
    }

    public function getPersonalTicketCount(Request $request) {
        $userId = auth('api')->id();
        
        $secret = $request->header('X-Melio-Webhook-Secret');
        if ($secret !== config('webhook.secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $count = $this->ticketWebhookService->getPersonalTicketCount($userId);

        return response()->json(['personal_tickets' => $count]);
    }

}
