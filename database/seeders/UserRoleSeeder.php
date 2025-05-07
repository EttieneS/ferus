<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRole;
use Illuminate\Support\Facades\Log;

class UserRoleSeeder extends Seeder {
    public function run(): void {
        $roles = [
            'Admin',
            'Manager',
            'Agent',
            'Viewer',
            'Reporter',
        ];

        foreach ($roles as $role) {
            try {
                $model = UserRole::firstOrCreate(['name' => $role]);

                if (!$model || !$model->id) {
                    throw new \Exception("❌ Failed to create or retrieve role: {$role}");
                }

                echo "✔ Role created or found: {$model->name} (ID: {$model->id})\n";
            } catch (\Throwable $e) {
                Log::error("Seeder error for role '{$role}': " . $e->getMessage());
                echo "❌ Error creating role '{$role}': " . $e->getMessage() . "\n";
            }
        }
    }
}
