<?php

namespace App\DTOs;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class UserRoleDTO {
    public int $queue;
    public array $roles;

    public function __construct(int $queue, array $roles) {
        $this->queue = $queue;
        $this->roles = $roles;
    }

    public static function fromRequest(Request $request): self {
        return new self(
            $request->input('queue'),
            $request->input('roles', []),
        );
    }
}
