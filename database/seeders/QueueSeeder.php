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
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'semperadmelioratest@gmail.com',
                'password' => 'pffafgvyinxwabah',
                'from_name' => 'Development Department',
                'from_email' => 'semperadmelioratest@gmail.com',
                'sla_id' => DB::table('slas')->where('minutes', 1)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT',
                'mailer' => 'it',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'itsempermeliora@gmail.com',
                'password' => 'mnqbaronzncmcccl',
                'from_name' => 'IT Department',
                'from_email' => 'itsempermeliora@gmail.com',
                'sla_id' => DB::table('slas')->where('minutes', 30)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
