<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\QueueController;

Route::controller(AuthController::class)->group(function() {
   Route::post('auth/login', 'login');
});

Route::controller(UserController::class)->group(function() {
   Route::post('users/index', 'index');
   Route::post('users/create', 'create');
   Route::post('users/mail', 'mail');
   Route::post('users/searchemail', 'searchEmail');
});

// Route::controller(RoleController::class)->group(function () {
//     Route::post('/roles/index', [RoleController::class, 'index']);
//     Route::post('/roles/store', [RoleController::class, 'store']);
// });

// Route::controller(CustomerController::class)->group(function () {
//     Route::post('/customers/index', [CustomerController::class, 'index']);
//     Route::post('/customers/create', [CustomerController::class, 'create']);
// });

Route::controller(TicketController::class)->group(function () {
    Route::post('tickets/index', [TicketController::class, 'index']);
    // Route::post('/tickets/create', [TicketController::class, 'create']);
    Route::post('/tickets/assign-user', [TicketController::class, 'assignUser']);
});

Route::controller(QueueController::class)->group(function () {
    Route::post('/queues/index', [QueueController::class, 'index']);
});



