<?php

namespace App\Services;

use App\Models\User;
use App\Models\QueueUserRole;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class UserService {
    public function getAllUsersWithRoles(): LengthAwarePaginator {
        return User::with('queueRoles')->paginate(15);         
    }

    public function getUserById($id): ?User {
        return User::find($id);
    }

    public function updateAvatar(int $userId, string $path): bool {
        $user = User::find($userId);
        if (!$user) {
            Log::error('User not found for avatar update', ['user_id' => $userId]);
            return false;
        }
        
        $user->avatar = $path;
        return $user->save();
    }

    public function getAvatar(int $userId): ?string {
        $user = User::find($userId);
        if ($user && $user->avatar) {
            return $user->avatar;
        }
        return null;
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

    public function update(User $user): array {     
        $userId = $this->getUserById($user->id);
        if (!$user) {
            Log::error('User not found for update', ['user_id' => $userId]);
            return [
                'status' => 'error',
                'message' => 'User not found',
                'data' => null
            ];
        }
        
        $user->name = request('name', $user->name);
        $user->surname = request('surname', $user->surname);
        $user->email = request('email', $user->email);                        
        
        // Save the user with the updated values
        if (!$user->isDirty()) {
            return [
                'status' => 'success',
                'message' => 'No changes made to the user',
                'data' => $user
            ];
        }
                
        $user->updated_at = now(); // Update the timestamp        
        $user->saveQuietly(); // Use saveQuietly to avoid triggering events if not needed
                
        return [
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $user
        ];
    }
}
