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
   public function index(): JsonResponse {
      $users = User::all();
   
      return $this->sendResponse(UserResource::collection($users), 'Users retrieved successfully.');      
   }

   public function create(Request $request): JsonResponse {
        $input = $request->all();                        
        $user = User::create($input);
   
        return $this->sendResponse('success', 'User created successfully.');
    }                   	
}
