<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\TicketResource;
use Illuminate\Http\JsonResponse;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use App\Services\TicketService;
use App\Services\TicketReplyService;
use App\Services\EmailService;
use App\Models\TicketReply;

class TicketController extends BaseController {
    protected TicketService $ticketService;
    protected TicketReplyService $ticketReplyService;
    protected EmailService $emailService;

    public function __construct(
        TicketService $ticketService,
        TicketReplyService $ticketReplyService,
        EmailService $emailService
    ) {
        $this->ticketService = $ticketService;
        $this->ticketReplyService = $ticketReplyService;
        $this->emailService = $emailService;
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

    public function forwardTicketToQueue(Request $request): JsonResponse {
        $ticket = Ticket::fromRequest($request);

        return $this->ticketService->forwardTicketToQueue($ticket);
    }

    public function reply(Request $request): JsonResponse {
        // return response()->json(["message" => "here"]);
        // $validated = $request->validate([
        //     'ticket_id' => 'required|exists:tickets,id',
        //     'user_id' => 'required|exists:users,id',
        //     'message' => 'required|string'
        // ]);

        // $reply = $this->ticketReplyService->createReply($validated);

        // return $reply
        //     ? response()->json(['status' => 'success', 'message' => 'Reply sent successfully'])
        //     : response()->json(['status' => 'error', 'message' => 'Failed to send reply'], 500);

        // $reply = new TicketReply([
        //     'ticket_id' => $request->input('ticket_id'),
        //     'user_id' => $request->input('user_id'),
        //     'subject' => $request->input('subject'),
        //     'body' => $request->input('body'),
        //     'mailer' => $request->input('mailer'), // Default mailer
        // ]);

        // $response = $this->emailService->sendEmail($reply);
        $response = "here";

        return response()->json($response);
    }
}
