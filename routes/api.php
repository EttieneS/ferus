<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SlaController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserRoleController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\IncomingMailController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\QueueController;
use App\Http\Controllers\API\OutgoingMailController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Http\Controllers\API\MailController;
use App\Http\Controllers\API\FileController;

Route::middleware(['auth:api'])->get('/me', function () {
    return response()->json([
        'id' => Auth::id(),
        'user' => Auth::user(),
    ]);
});

Route::controller(AuthController::class)->group(function () {
    Route::post('auth/login', 'login');
    Route::post('auth/logout', 'logout')->middleware('auth:api');
    Route::post('auth/refresh', 'refresh')->middleware('auth:api');
});

Route::controller(UserController::class)->group(function () {
    Route::post('users/index', 'index');
    Route::post('users/create', 'create');
    Route::post('users/mail', 'mail');
    Route::post('users/searchemail', 'searchEmail');
    Route::post('users/update-rights', 'updateRights');
    Route::post('users/get-by-id', 'getById');
    Route::post('users/update', 'update');
});

Route::controller(UserRoleController::class)->group(function () {
    Route::post('/user-roles/index', [UserRoleController::class, 'index']);
    Route::post('/user-roles/store', [UserRoleController::class, 'store']);
    Route::post('/user-roles/update', [UserRoleController::class, 'update']);
});

Route::controller(CustomerController::class)->group(function () {
    Route::post('/customers/index', [CustomerController::class, 'index']);
    Route::post('/customers/create', [CustomerController::class, 'create']);
});

Route::controller(TicketController::class)->group(function () {
    Route::post('tickets/index', [TicketController::class, 'index']);
    Route::post('/tickets/create', [TicketController::class, 'create']);
    Route::post('/tickets/assign-user', [TicketController::class, 'assignUser']);
    Route::post('/tickets/assign-users', [TicketController::class, 'assignUsers']);
    Route::post('/tickets/forward-queue', [TicketController::class, 'forwardTicketToQueue']);
    Route::post('/tickets/reply', [TicketController::class, 'reply']);
    Route::post('/tickets/get-by-queue', [TicketController::class, 'getTicketsByQueue']);
    Route::post('/tickets/update-priority', [TicketController::class, 'updatePriority']);
    Route::post('/tickets/update-status', [TicketController::class, 'updateStatus']);
    Route::post('/tickets/get-personal-tickets', [TicketController::class, 'getPersonalTickets']);
});

Route::controller(QueueController::class)->group(function () {
    Route::post('/queues/index', [QueueController::class, 'index']);
    Route::post('/queues/create', [QueueController::class, 'store']);
});

Route::controller(IncomingMailController::class)->group(function () {
    Route::post('/incoming-mail/store', [IncomingMailController::class, 'store']);
});

Route::controller(OutgoingMailController::class)->group(function () {
    Route::post('/outgoing-mails/send', 'send');
    Route::post('/outgoing-mails/get-by-id', 'getByTicketId');

    Route::get('/outgoing-mails/test-send', 'testSend');
});

Route::middleware(['auth:api'])->group(function () {
    Route::post('/mails/send', [MailController::class, 'send']);
    Route::post('/mails/get-by-ticket-id', [MailController::class, 'getByTicketId']);
    Route::post('/mails/get-replies', [MailController::class, 'getReplies']);
});

Route::post('/auth/refresh', function (): JsonResponse {
    /** @var \Tymon\JWTAuth\JWTGuard $guard */
    $guard = Auth::guard('api');

    try {
        $newToken = $guard->refresh();

        return response()->json([
            'success' => true,
            'access_token' => $newToken,
            'token_type' => 'bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
        ]);
    } catch (TokenInvalidException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid refresh token',
        ], 401);
    }
});

Route::get('/slas/index', [SlaController::class, 'index']);

Route::post('/files/upload-avatar', [FileController::class, 'uploadAvatar']);
