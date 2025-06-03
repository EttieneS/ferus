<?php

namespace App\DTOs;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class UserRoleDTO {
    public int $userId;    
    public array $roles = [
        'queueId' => 0,
        'roles' => [],
    ];

    public function __construct() {}

    // public static function fromRequest(Request $request): self {        
    //     $userId = $request->input('user_id');
    //     $roles = [];         
        
    //     foreach ($request->input('roles') as $role) {
    //         $roles[] = [
    //             'queueId' => $role['queue_id'] ?? 0,
    //             'roles' => $role['roles'] ?? [],
    //         ];
    //     }
    //     $roles = $roles ?? [];

    //     return (new self(
    //         $userId = $userId,
    //         $roles = $roles
    //     ));  
    // }

    public static function fromRequest(Request $request): self {
        $dto = new self();
        $dto->userId = $request->input('user_id');
        $dto->roles = array_map(function ($item) {
            return (object) [
                'queueId' => $item['queue_id'],
                'roleIds' => $item['role_ids'],
            ];
        }, $request->input('roles', []));

        return $dto;
    }
}
