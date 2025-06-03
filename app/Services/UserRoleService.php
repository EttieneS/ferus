<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserRoleService {
    public function createRole(array $data) {

        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name']
        ]);
    }

    public function getAll() {
        return DB::table('user_roles')
            ->select('id', 'name')
            ->get();
    }

    public function assignRole(array $roleDTO) {
        foreach ($roleDTO as $item) {
            DB::table('queue_user_roles')->insert([
                'queue_id' => $item['queue'],
                'user_id' => $item['user_id'],
                'role_id' => $item['role_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // get error from database if the insert fails
        // If the insert fails, you can throw an exception or return an error response





    }
}
