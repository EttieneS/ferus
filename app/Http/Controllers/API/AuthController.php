<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController {

    public function __construct(
        private AuthService $authService
    ) {
    }

    public function login(Request $request): JsonResponse {
        $credentials = $request->only('email', 'password');
        Log::info("Login");
        
        if (!$token = auth('api')->attempt($credentials)) {
            return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
        }

        // $guard = Auth::guard('api');

        // if (!$token = $guard->attempt($credentials)) {
        //     return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        // }

        // $user = $guard->user();

        // return $this->sendResponse([
        //     'token' => $token,
        //     'user' => $user
        // ], 'User login successfully.');

        $user = auth('api')->user();

        if (!$user) {
            return $this->sendError('Unauthorised.', ['error' => 'User not found after login']);
        }

        return $this->sendResponse([
            'token' => $token,
            'user' => $user
        ], 'User login successful.');
    }

    public function logout(): JsonResponse {
        $guard = Auth::guard('api');

        try {
            $guard->logout();

            return $this->sendResponse([], 'User logged out successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Logout failed.', ['error' => $e->getMessage()]);
        }
    }
}
