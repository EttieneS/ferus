<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class OutgoingMailRecipientSeeder extends Seeder {
    public function run(): void {
        $now = Carbon::now();

        DB::table('outgoing_mail_recipients')->insert([
            [
                'outgoing_mail_id' => 1,
                'recipient_id' => 5,
                'recipient_type' => 0, // 0 = User
                'mail_type' => 'to',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'outgoing_mail_id' => 1,
                'recipient_id' => 2,
                'recipient_type' => 1, // 1 = Customer
                'mail_type' => 'to',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'outgoing_mail_id' => 1,
                'recipient_id' => 3,
                'recipient_type' => 1, // 1 = Customer
                'mail_type' => 'cc',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
