<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserController;

Route::controller(UserController::class)->group(function() {
   Route::post('users/index', 'index');
   Route::post('users/create', 'create');
});

