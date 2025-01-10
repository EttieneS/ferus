<?php

namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
   
class AuthController extends BaseController {
    
    public function login(Request $request): JsonResponse {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] = $user->createToken('squaredbarn')->plainTextToken; 
            $success['user'] = $user;

            header('Content-Type', 'html');
            header('Access-Control-Allow-Origin', 'localhost:4200');
            header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization');

            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
}
