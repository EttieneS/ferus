<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class QueueSeeder extends Seeder {
    public function run(): void {
        DB::table('queues')->insert([
            [
                'name' => 'IT Google',
                'mailer' => 'itgoogle',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'itsempermeliora@gmail.com',
                'password' => Crypt::encrypt('mnqbaronzncmcccl'),
                'from_name' => 'IT Department',
                'from_email' => 'itsempermeliora@gmail.com',
                'sla_id' => DB::table('slas')->where('minutes', 30)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dev Google',
                'mailer' => 'devgoogle',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'semperadmelioratest@gmail.com',
                'password' => Crypt::encrypt('ffafgvyinxwabah'),
                'from_name' => 'Dev Department',
                'from_email' => 'semperadmelioratest@gmail.com',
                'sla_id' => DB::table('slas')->where('minutes', 60)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dev Melio',
                'mailer' => 'devmelio',
                'host' => 'cloudmail.synaq.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'dev.melio@affinitylifeltd.co.za',
                'password' => Crypt::encrypt('NTqN7ujpT*Lq'),
                'from_name' => 'Dev Department',
                'from_email' => 'dev.melio@affinitylifeltd.co.za',
                'sla_id' => DB::table('slas')->where('minutes', 60)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
