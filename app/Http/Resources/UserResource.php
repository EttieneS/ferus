<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class UserResource extends JsonResource {
    public function toArray($request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'queue_roles' => $this->queueRoles
                ->groupBy('queue_id')
                ->map(function ($items, $queueId) {
                    return [
                        'queue_id' => (int) $queueId,
                        'role_ids' => $items->pluck('role_id')->map(fn($id) => (int) $id)->values()->all(),
                    ];
                })
                ->values()
                ->all(),
        ];
    }
}
