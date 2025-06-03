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

    public function getPersonalTickets(Request $request): JsonResponse {
        $userId = Auth::id();
        if (!$userId) {
            return $this->sendError('Unauthorized', [], 401);
        }

        if ($request->input('user_id') != $userId) {
            return $this->sendError(
                'Unauthorised',
                ["error" => "Unauthorised"],
                403
            );
            Log::error('Unauthorized access attempt', [
                'user_id' => $userId,
                'requested_user_id' => $request->input('user_id'),
                'requested_user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'date' => now()->toDateTimeString(),
                'user_agent' => $request->header('User-Agent', 'unknown'),
            ]);
        }

        $paginatedDTOs = $this->ticketService->getPersonalTickets($userId);

        return response()->json($paginatedDTOs);
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
