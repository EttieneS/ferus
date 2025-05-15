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
                'host' => env('MAIL_IT_HOST'),
                'port' => env('MAIL_IT_PORT'),
                'encryption' => env('MAIL_IT_ENCRYPTION'),
                'username' => env('MAIL_IT_USERNAME'),
                'password' => Crypt::encryptString(env('MAIL_IT_PASSWORD')),
                'from_name' => 'IT Department',
                'from_email' => env('MAIL_IT_FROM_ADDRESS'),
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
                'password' => Crypt::encryptString('ffafgvyinxwabah'),
                'from_name' => 'Dev Department',
                'from_email' => 'semperadmelioratest@gmail.com',
                'sla_id' => DB::table('slas')->where('minutes', 60)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dev Melio',
                'mailer' => env('MAILER_DEV_MELIO_MAILER'),
                'host' => env('MAILER_DEV_MELIO_HOST'),
                'port' => env('MAILER_DEV_MELIO_PORT'),
                'encryption' => env('MAILER_DEV_MELIO_ENCRYPTION'),
                'username' => env('MAILER_DEV_MELIO_USERNAME'),
                'password' => Crypt::encryptString(env('MAILER_DEV_MELIO_PASSWORD')),
                'from_name' => env('MAILER_DEV_MELIO_FROM_NAME'),
                'from_email' => env('MAILER_DEV_MELIO_FROM_EMAIL'),
                'sla_id' => DB::table('slas')->where('minutes', 60)->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
