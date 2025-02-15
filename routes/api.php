<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\CustomerController;

Route::controller(AuthController::class)->group(function() {
   Route::post('auth/login', 'login');
});

Route::controller(UserController::class)->group(function() {
   Route::post('users/index', 'index');
   Route::post('users/create', 'create');
   Route::post('users/mail', 'mail');
   Route::post('users/searchemail', 'searchEmail');
});

Route::controller(TicketController::class)->group(function() {
   Route::post('tickets/index', 'index');     
   Route::post('tickets/create', 'create');
   Route::post('tickets/getbyqueue', 'getByQueue');

});

Route::controller(TeamController::class)->group(function () {
    Route::post('/teams/index', 'index');
    Route::post('/teams/create', 'create');
    Route::post('/teams/getbyid', 'getbyid');
    Route::delete('/teams/deletebyid', 'deletebyid');

    Route::post('/teams/{id}/add-member', 'addMember');
    Route::get('/teams/{id}/members', 'getMembers');
});

Route::controller(RoleController::class)->group(function () {
    Route::post('/roles/index', [RoleController::class, 'index']);
    Route::post('/roles/store', [RoleController::class, 'store']);
});

Route::controller(CustomerController::class)->group(function () {
    Route::post('/customers/index', [CustomerController::class, 'index']);
    Route::post('/customers/create', [CustomerController::class, 'create']);
});


