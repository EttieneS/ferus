<?php

namespace App\DTOs;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class AssignUsersDTO {
    public int $id;
    public array $assignedTo;
    public int $assignedBy;

    public function __construct(int $id, array $assignedTo, int $assignedBy) {
        $this->id = $id;
        $this->assignedTo = $assignedTo;
        $this->assignedBy = $assignedBy;
    }

    public static function fromRequest(Request $request): self {
        return new self(
            $request->input('id'),
            $request->input('assigned_to', []),
            Auth::id()
        );
    }
}
