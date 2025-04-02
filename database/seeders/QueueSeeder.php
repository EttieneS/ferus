<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QueueSeeder extends Seeder {
    public function run(): void {
        DB::table('queues')->insert([
            [
                'name' => 'Dev',
                'mailer' => 'dev',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT',
                'mailer' => 'dev',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Infrastructure',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
