<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OutgoingMail;
use App\Models\OutgoingMailRecipient;
use App\Models\User;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class OutgoingMailSeeder extends Seeder {
    public function run(): void {
        DB::beginTransaction();

        try {
            $user = User::firstOrCreate([
                'email' => 'internal@example.com'
            ], [
                'name' => 'Internal',
                'surname' => 'User',
                'password' => bcrypt('password')
            ]);

            $customer = Customer::firstOrCreate([
                'email' => 'client@example.com'
            ], [
                'full_name' => 'Client A',
                'phone' => '123456789',
                'address' => 'Client Street'
            ]);

            $cc = Customer::firstOrCreate([
                'email' => 'accountant@example.com'
            ], [
                'full_name' => 'Accountant',
                'phone' => '987654321',
                'address' => 'Finance Street'
            ]);

            $ticket = Ticket::first();
            if (!$ticket) throw new \Exception("No ticket found.");

            $mail = OutgoingMail::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'user_type' => 0, // 0 = User, 1 = Customer
                'subject' => 'Test Subject',
                'body' => 'Test body for seeded mail.'
            ]);

            OutgoingMailRecipient::insert([
                [
                    'outgoing_mail_id' => $mail->id,
                    'recipient_id' => $user->id,
                    'recipient_type' => 0, // User
                    'mail_type' => 'to',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'outgoing_mail_id' => $mail->id,
                    'recipient_id' => $customer->id,
                    'recipient_type' => 1, // Customer
                    'mail_type' => 'to',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'outgoing_mail_id' => $mail->id,
                    'recipient_id' => $cc->id,
                    'recipient_type' => 1, // Customer
                    'mail_type' => 'cc',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);

            DB::commit();
            echo "✔ Outgoing mail seeded with recipients.\n";
        } catch (\Throwable $e) {
            DB::rollBack();
            echo "❌ Seeder failed: {$e->getMessage()}\n";
        }
    }
}
