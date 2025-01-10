<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TicketController;

Route::controller(AuthController::class)->group(function() {
   Route::post('auth/login', 'login');
});

Route::controller(UserController::class)->group(function() {
   Route::post('users/index', 'index');
   Route::post('users/create', 'create');
   Route::post('users/mail', 'mail');

});

Route::controller(TicketController::class)->group(function() {
   Route::post('tickets/create', 'create');
   Route::post('tickets/test', 'test');      
});


