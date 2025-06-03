<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource {    
    public function toArray($request): array {
        $roleMap = [];

        foreach ($this->queueRoles as $entry) {
            $qid = $entry->queue_id;
            $rid = $entry->role_id;

            if (!isset($roleMap[$qid])) {
                $roleMap[$qid] = [];
            }

            $roleMap[$qid][] = $rid;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'roles' => $roleMap,
        ];
    }
}
