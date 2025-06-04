<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class UserResource extends JsonResource {
    public function toArray($request): array {
        $roleMap = [];

        foreach ($this->queueRoles as $entry) {
            if (!isset($roleMap[$entry->queue_id])) {
                $roleMap[$entry->queue_id] = [];
            }

            if ($entry->role_id) {
                $roleMap[$entry->queue_id][] = $entry->role_id;
            }
        }

        Log::info('User RoleMap:', ['user_id' => $this->id, 'roles' => $roleMap]);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'roles' => json_decode(json_encode($roleMap, JSON_FORCE_OBJECT), true),
        ];
    }
}
