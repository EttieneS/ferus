<?php
namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RoleService {
    public function createRole(array $data) {
                
        return Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name']
        ]);
    }

    public function getAll() {
        return DB::table('roles')            
            ->select('id', 'name')
            ->get();
    }
}


