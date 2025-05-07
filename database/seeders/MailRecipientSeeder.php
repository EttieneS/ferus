<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mail;
use App\Models\MailRecipient;
use App\Models\User;
use App\Models\Customer;

class MailRecipientSeeder extends Seeder {
    public function run(): void {
        $mails = Mail::all();
        $users = User::all();
        $customers = Customer::all();

        foreach ($mails as $mail) {
            if (rand(0, 1)) {
                $user = $users->random();
                MailRecipient::create([
                    'mail_id' => $mail->id,
                    'recipient_id' => $user->id,
                    'recipient_type' => 0,
                    'send_type' => 0,
                ]);
            } else {
                $customer = $customers->random();
                MailRecipient::create([
                    'mail_id' => $mail->id,
                    'recipient_id' => $customer->id,
                    'recipient_type' => 1,
                    'send_type' => 0,
                ]);
            }

            if (rand(0, 1)) {
                $user = $users->random();
                MailRecipient::create([
                    'mail_id' => $mail->id,
                    'recipient_id' => $user->id,
                    'recipient_type' => 0,
                    'send_type' => 1,
                ]);
            } else {
                $customer = $customers->random();
                MailRecipient::create([
                    'mail_id' => $mail->id,
                    'recipient_id' => $customer->id,
                    'recipient_type' => 1,
                    'send_type' => 1,
                ]);
            }
        }
    }
}
