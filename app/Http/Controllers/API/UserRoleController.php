<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use App\Services\UserRoleService;
use App\DTOs\UserRoleDTO;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Log;
class UserRoleController extends BaseController {
    protected $roleService;

    public function __construct(UserRoleService $roleService) {
        $this->roleService = $roleService;
    }

    public function index() {
        return response()->json($this->roleService->getAll());
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
        ]);

        $role = $this->roleService->createRole($request->all());

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }

    public function update(Request $request) {
        Log::error('Update User Role Request', [
            'request' => $request->all()
        ]);
        
        // $request->validate([
        //     'user_id' => 'required|exists:users,id',
        //     'role_id' => 'required|exists:roles,id',
        // ]);

        $roleDTO = UserRoleDTO::fromRequest($request);                
        $response = $this->roleService->update($roleDTO);        
        
        if (isset($response['success']) && !$response['success']) {
            return $this->sendResponse($response['message'], $response['code']);

        }

        return $this->sendResponse(["status" => 200], $response['message']);
    }
}
