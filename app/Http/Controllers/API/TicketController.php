<?php

namespace App\Http\Controllers\API;

use App\DTOs\AssignUsersDTO;
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
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\AssignRef;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\DTOs\TicketViewDTO;

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
        $dto = AssignUsersDTO::fromRequest($request);

        $authenticatedId = Auth::id();

        if ($dto->assignedBy !== $authenticatedId) {
            Log::warning('⚠️ assigned_by mismatch', [
                'expected' => $authenticatedId,
                'received' => $dto->assignedBy,
            ]);

            return $this->sendError('Token does not match assignedBy.', [], 403);
        }

        return $this->sendResponse(
            $this->ticketService->assignUsers($dto),
            'Users assigned successfully.'
        );
    }

    public function assignUsers(Request $request): JsonResponse {
        Log::info('Raw incoming request: assign users component bonobo', $request->all());

        /** @var \Tymon\JWTAuth\JWTGuard $jwtGuard */
        $jwtGuard = auth('api');

        $assignedBy = $jwtGuard->id();
        $currentToken = JWTAuth::getToken()?->get();

        if (!$assignedBy || !$currentToken) {
            $refreshedToken = JWTAuth::setToken($currentToken)->refresh();
        }

        $assignDTO = AssignUsersDTO::fromRequest($request, $assignedBy);

        try {
            $result = $this->ticketService->assignUsers($assignDTO);

            $refreshedToken = JWTAuth::setToken($currentToken)->refresh();

            $response = $this->sendResponse($result, 'Ticket assigned to selected users.');

            if ((string) $refreshedToken !== (string) $currentToken) {
                $response->headers->set('Authorization', 'Bearer ' . $refreshedToken);
            }

            return $response;
        } catch (\Exception $e) {
            DB::enableQueryLog();
            Log::error('🔥 Exception in assignUsers controller', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Log::info('Eloquent queries', DB::getQueryLog());

            return $this->sendError('Assignment failed.', [], 500, $e->getMessage());
        }
    }


    public function getTicketsByQueue(Request $request): JsonResponse {
        $queueId = $request->queue_id;
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

    public function personal(): JsonResponse {
        $userId = 1;
        $tickets = $this->ticketService->getPersonalTickets($userId);

        return $this->sendResponse(
            TicketViewDTO::fromCollection($tickets),
            'Personal tickets fetched successfully'
        );
    }

    public function updatePriority(Request $request) {
        $ticketId = (int) $request->input('ticket_id');
        $priority = (int) $request->input('priority');

        $ticket = $this->ticketService->updatePriority($ticketId, $priority);

        return response()->json([
            'message' => 'Priority updated',
            'ticket' => $ticket,
        ]);
    }

    public function updateStatus(Request $request) {
        $validated = $request->validate([
            'ticket_id' => 'required|integer|exists:tickets,id',
            'status' => 'required|numeric|min:0|max:3',
        ]);

        $ticket = Ticket::findOrFail($validated['ticket_id']);
        $ticket->status = $validated['status'];
        $ticket->save();

        return response()->json([
            'message' => 'Status updated successfully.',
            'ticket' => $ticket,
        ]);
    }
}
