<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\IncomingMail;
use App\Models\Ticket;
use App\Models\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class IncomingMailSeeder extends Seeder {
    public function run(): void {
        DB::beginTransaction();

        try {
            $customer = Customer::firstOrCreate([
                'email' => 'smithettiene@yahoo.com',
                'full_name' => 'Ettiene Smith',
                'phone' => '123456789',
                'address' => '123 Demo Street'
            ]);

            if (!$customer || !$customer->id) {
                throw new \Exception("Customer creation failed or invalid ID.");
            }

            $queues = Queue::with('sla')->get();

            if ($queues->isEmpty()) {
                throw new \Exception("No queues found in database.");
            }

            foreach ($queues as $queue) {
                for ($i = 1; $i <= 3; $i++) {
                    $messageId = uniqid("msg_");

                    $incomingMail = IncomingMail::create([
                        'customer_id' => $customer->id,
                        'message_id' => $messageId,
                        'subject' => "{$queue->name} SLA Test Mail {$i}",
                        'body' => "SLA test mail {$i} for queue '{$queue->name}'.",
                    ]);

                    if (!$incomingMail || !$incomingMail->id) {
                        throw new \Exception("IncomingMail creation failed for queue '{$queue->name}' #{$i}");
                    }

                    $dueDate = null;
                    if ($queue->sla && $queue->sla->minutes) {
                        $shiftMinutes = match ($i) {
                            1 => -30,
                            2 => 0,
                            3 => 30,
                        };
                        $dueDate = now()->addMinutes($queue->sla->minutes + $shiftMinutes);
                    }

                    $ticket = Ticket::create([
                        'incoming_mail_id' => $incomingMail->id,
                        'status' => 0,
                        'priority' => 0,
                        'queue_id' => $queue->id,
                        'due_date' => $dueDate,
                    ]);

                    if (!$ticket || !$ticket->id) {
                        throw new \Exception("Ticket creation failed for queue '{$queue->name}' #{$i}");
                    }

                    echo "✔ SLA test ticket {$ticket->id} for '{$queue->name}' created (due in {$shiftMinutes} min)\n";
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('IncomingMailSeeder error: ' . $e->getMessage());
            echo "❌ Seeder failed: " . $e->getMessage() . "\n";
        }
    }
}
