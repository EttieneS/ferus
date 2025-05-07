<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mail;
use App\Models\Ticket;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;

class TicketSeeder extends Seeder {
    public function run(): void {
        $queues = Queue::all();
        $users = User::all();

        foreach ($queues as $queue) {
            for ($i = 0; $i < 3; $i++) {
                $mail = Mail::create([
                    'user_id' => 1,
                    'user_type' => 1,
                    'subject' => 'Mail subject ' . $i,
                    'body' => 'Generated body content ' . $i,
                    'mail_type' => rand(0, 1), // 0 = outgoing, 1 = incoming
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
