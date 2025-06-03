<?php

namespace App\Services;

use App\DTOs\UserRoleDTO;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        try {
            foreach ($roleDTO as $item) {
                DB::table('queue_user_roles')->insert([
                    'queue_id' => $item['queue'],
                    'user_id' => $item['user_id'],
                    'role_id' => $item['role_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $response = [
                'message' => 'Roles assigned successfully.',
                'success' => true,
                'code' => 200
            ];
        } catch (\Exception $e) {        
            Log::error('Role assignment failed', [
                'error' => $e->getMessage(),
                'data' => $roleDTO
            ]);

            return $response = [
                'message' => 'Failed to assign roles.',
                'code' => 500,
                'success' => false
            ];
        }
    }

    public function update(UserRoleDTO $userRoleDTO) {
        try {
            $userId = $userRoleDTO->userId;
            $newAssignments = collect($userRoleDTO->roles)
                ->flatMap(function ($queueRole) use ($userId) {
                    return collect($queueRole->roleIds)->map(function ($roleId) use ($queueRole, $userId) {
                        return [
                            'queue_id' => $queueRole->queueId,
                            'user_id' => $userId,
                            'role_id' => $roleId,
                        ];
                    });
                });
            
            // $userId must not be used before initialisation??
            $existingAssignments = DB::table('queue_user_roles')
                ->where('user_id', $userId)
                ->get();

            foreach ($existingAssignments as $existing) {
                $stillAssigned = $newAssignments->contains(function ($new) use ($existing) {
                    return $new['queue_id'] === $existing->queue_id &&
                        $new['role_id'] === $existing->role_id;
                });

                if (!$stillAssigned && is_null($existing->deleted_at)) {
                    DB::table('queue_user_roles')
                        ->where('id', $existing->id)
                        ->update(['deleted_at' => now()]);
                }
            }

            foreach ($newAssignments as $assignment) {
                $exists = DB::table('queue_user_roles')
                    ->where([
                        ['queue_id', '=', $assignment['queue_id']],
                        ['user_id', '=', $assignment['user_id']],
                        ['role_id', '=', $assignment['role_id']],
                    ])
                    ->first();

                if ($exists && $exists->deleted_at) {
                    DB::table('queue_user_roles')
                        ->where('id', $exists->id)
                        ->update(['deleted_at' => null, 'updated_at' => now()]);
                } elseif (!$exists) {
                    DB::table('queue_user_roles')->insert([
                        ...$assignment,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return $response = [
                'message' => 'Roles updated successfully'
            ];
        } catch (\Throwable $e) {
            return $response = [
                'error' => 'Failed to update roles',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getUserRoles(int $userId) {
        return DB::table('queue_user_roles')
            ->where('user_id', $userId)
            ->get()
            ->map(function ($item) {
                return [
                    'queue_id' => $item->queue_id,
                    'role_id' => $item->role_id,
                ];
            });
    }
}
