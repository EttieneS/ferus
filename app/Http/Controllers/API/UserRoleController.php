<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use App\Services\UserRoleService;
use App\DTOs\UserRoleDTO; // Make sure this file exists at app/DTOs/UserRoleDTO.php
use App\Models\User;

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


    public function assignRole(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $roleDTO = array();
        //the request will be an array of userroleDTO
        foreach ($request->all() as $item) {
            $roleDTO[] = UserRoleDTO::fromRequest($item);
        }

        $this->roleService->assignRole($roleDTO);

        return response()->json(['message' => 'Role assigned successfully']);
    }
}
