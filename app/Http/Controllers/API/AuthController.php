<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Services\AuthService;
use App\Services\UserRoleService;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController {

    public function __construct(
        private AuthService $authService,
        private UserRoleService $userRoleService,
    ) {
    }
    
    public function login(Request $request): JsonResponse {
        $credentials = $request->only('email', 'password');
        Log::info("Login attempt", $credentials);

        if (!$token = auth('api')->attempt($credentials)) {
            return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
        }
                        
        $user = auth('api')->user();

        if (!$user) {
            return $this->sendError('Unauthorised.', ['error' => 'User not found after login']);
        } else {
            $rawRoles = $this->userRoleService->getUserRoles($user->id);

            $roles = $rawRoles->reduce(function ($carry, $item) {
                $queueId = $item['queue_id'];
                $roleId = $item['role_id'];

                if (!isset($carry[$queueId])) {
                    $carry[$queueId] = [];
                }

                $carry[$queueId][] = $roleId;

                return $carry;
            }, []);
        }
        
        $cookie = cookie(
            'token',
            $token,
            60 * 24,
            '/',
            null,
            true,
            true,
            false,
            'Strict'
        );

        $data = [
            'user' => $user,
            'roles' => $roles,
        ];

        $message = 'User login successful.';

        return $this->sendResponse($data, $message)
            ->withCookie($cookie);     
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
