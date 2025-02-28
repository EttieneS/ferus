<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Validator;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\ClientManager;
use Illuminate\Console\Command;


class UserController extends BaseController
{
    public function register(Request $request): JsonResponse
    {
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'email' => 'required|email',
        //     'password' => 'required',
        //     'c_password' => 'required|same:password',
        // ]);

        // if($validator->fails()){
        //     return $this->sendError('Validation Error.', $validator->errors());
        // }

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

    public function index(): JsonResponse
    {
        $users = User::all();

        return $this->sendResponse(UserResource::collection($users), 'Users retrieved successfully.');
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['token'] =  $user->createToken('squaredbarn')->plainTextToken;
            $success['name'] =  $user->name;

            // header('Content-Type', 'text/plain');
            // header('Access-Control-Allow-Origin', 'http://localhost:4200');
            // header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization');

            return $this->sendResponse($success, 'User register successfully.');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function searchEmail(Request $request): JsonResponse
    {
        $input = $request->all();
        $partialEmail = $input['email'];

        $users = User::where('email', 'like', '%' . $partialEmail . '%')->get();

        return $this->sendResponse(UserResource::collection($users), 'e-mail addresses like $request');
    }
}
