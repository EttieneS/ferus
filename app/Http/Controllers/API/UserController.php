<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Validator;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController {
    protected UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function register(Request $request): JsonResponse {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('squaredbarn')->plainTextToken;
        $success['name'] =  $user->name;

        header('Content-Type', 'text/plain');
        header('Access-Control-Allow-Origin', 'http://localhost:4200');
        header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization');

        return $this->sendResponse($success, 'User register successfully.');
    }

    public function index(): JsonResponse {
        $users = User::all();

        return $this->sendResponse(UserResource::collection($users), 'Users retrieved successfully.');
    }

    public function create(Request $request): JsonResponse {
        try {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['token'] =  $user->createToken('squaredbarn')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User register successfully.');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function searchEmail(Request $request): JsonResponse {
        $input = $request->all();
        $partialEmail = $input['email'];

        $users = User::where('email', 'like', '%' . $partialEmail . '%')->get();

        return $this->sendResponse(UserResource::collection($users), 'e-mail addresses like $request');
    }

    public function updateRights(Request $request): JsonResponse {
        Log::info('🔧 Updating rights for user ID: ' . $request->input('id'));
        Log::info('📦 Payload:', $request->all());

        $user = $this->userService->getUserById($request->input('id'));

        if (!$user) {
            Log::warning('🚫 User not found.');
            return $this->sendError('No user found', [], 404);
        }

        $this->userService->updateUserQueuesAndRoles($user, $request->input('queues'));

        Log::info('✅ Update process complete.');

        return $this->sendResponse([], 'User rights updated');
    }
}
