<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use App\Services\UserRoleService;

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
}
