<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class QueueSeeder extends Seeder {
    public function run(): void {
        DB::table('queues')->insert([
            [
                'name' => 'GoogleIT',
                'mailer' => 'it',
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
                'name' => 'GoogleIT',
                'mailer' => 'it',
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
                'name' => 'Melio Dev',
                'mailer' => 'meliodev',
                'host' => 'cloudmail.synaq.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'dev.melio@affinitylifeltd.co.za',
                'password' => Crypt::encrypt('mnqbaronzncmcccl'),
                'from_name' => 'IT Department',
                'from_email' => 'itsempermeliora@gmail.com',
                'sla_id' => DB::table('slas')->where('minutes', 30)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
