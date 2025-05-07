<?php

namespace App\Services;

use App\Models\User;
use App\Models\QueueUserRole;

class UserService {
    public function getAllUsers() {
        return User::all();
    }

    public function getUserById($id): ?User {
        return User::find($id);
    }

    public function updateUserQueuesAndRoles(User $user, array $queueRoles): bool {
        foreach ($queueRoles as $item) {
            $queueId = $item['id'];
            $roles = $item['roles'] ?? [];

            QueueUserRole::where('user_id', $user->id)
                ->where('queue_id', $queueId)
                ->delete();

            foreach ($roles as $roleId) {
                QueueUserRole::create([
                    'user_id' => $user->id,
                    'queue_id' => $queueId,
                    'role_id' => $roleId
                ]);
            }
        }

        return true;
    }

    public function createUser(array $data): User {
        return User::create($data);
    }
}
