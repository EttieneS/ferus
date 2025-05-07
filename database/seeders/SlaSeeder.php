<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlaSeeder extends Seeder {
    public function run(): void {
        DB::table('slas')->insert([
            [
                'name' => 'Demo SLA (1 min)',
                'minutes' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard (60 min)',
                'minutes' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Urgent (30 min)',
                'minutes' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Critical (15 min)',
                'minutes' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'One Day (24h)',
                'minutes' => 60 * 24,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Two Day (48h)',
                'minutes' => 60 * 48,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
