<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Queue;
use App\Models\Sla;

class QueueSeeder extends Seeder {
    public function run(): void {
        $demo = DB::table('slas')->where('minutes', 1)->value('id');
        $urgent = DB::table('slas')->where('minutes', 30)->value('id');
        $critical = DB::table('slas')->where('minutes', 15)->value('id');

        DB::table('queues')->insert([
            [
                'name' => 'Dev',
                'mailer' => 'dev',
                'sla_id' => $demo,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT',
                'mailer' => 'it',
                'sla_id' => $urgent,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Infrastructure',
                'mailer' => 'infra',
                'sla_id' => $critical,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
