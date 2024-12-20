<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Validator;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

   
class UserController extends BaseController
{
   public function index(): JsonResponse {
      $users = User::all();
   
      return $this->sendResponse(UserResource::collection($users), 'Users retrieved successfully.');
      // return $users;
   }

   public function create(Request $request): JsonResponse
    {
        $input = $request->all();
        
        // echo json_encode($input);

   
        // $validator = Validator::make($input, [
        //     'name' => 'required',            
        // ]);
   
        // if($validator->fails()){
        //     return $this->sendError('Validation Error.', $validator->errors());
        // }
        
        $user = User::create($input);
   
        return $this->sendResponse('success', 'User created successfully.');
    }   

}
