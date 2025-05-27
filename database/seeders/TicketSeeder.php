<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mail;
use App\Models\MailBody;
use App\Models\Ticket;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TicketSeeder extends Seeder {
    public function run(): void {
        $queues = Queue::all();
        $users = User::all();

        foreach ($queues as $queue) {
            for ($i = 0; $i < 3; $i++) {
                $subject = Str::title(fake()->words(rand(3, 6), true));
                $bodyText = fake()->paragraphs(rand(2, 4), true);

                $mail = Mail::create([
                    'sender_id' => 1,
                    'sender_type' => Mail::CUSTOMER,
                    'to_users' => null,
                    'cc_users' => null,
                    'to_customers' => null,
                    'cc_customers' => null,                                        
                    'is_internal' => false,
                    'in_reply_to' => null,
                ]);

                MailBody::create([
                    'mail_id' => $mail->id,
                    'body' => "Subject: $subject\n\n$bodyText"
                ]);

                Ticket::create([
                    'mail_id' => $mail->id,
                    'queue_id' => $queue->id,
                    'assigned_by' => $users->random()->id,
                    'assigned_to' => $users->random()->id,
                    'status' => rand(0, 2),
                    'priority' => rand(0, 2),
                    'due_date' => Carbon::now()->addDays(rand(1, 5)),
                ]);
            }
        }
    }
}
