<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Ticket;
use App\Models\Customer;
use App\Models\IncomingMail;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListenDevEmail extends Command {
    protected $signature = 'app:listen-dev-email';
    protected $description = 'Command to listen for new emails and create tickets';

    public function handle() {
        $client = Client::account("default");
        $client->connect();

        $folder = $client->getFolderByName('INBOX');
        $timeout = 1200;

        $folder->idle(function ($message) {
            try {
                $this->info("New message received: " . ($message->subject ?? "No Subject"));
                $this->info("message body" . $message->getTextBody() ?? '');
                // $message->getFrom()[0]->mail);

                $from = $message->getFrom()[0]->mail;
                $this->info("From: " . ($from ?? "Unknown"));

                $customerData = [
                    'full_name' => $message->getFrom()[0]->personal ?? "Unknown",
                    'email'     => $message->getFrom()[0]->mail ?? null,
                ];

                if ($customerData['email']) {
                    $customer = Customer::where('email', $customerData['email'])->first();
                    if ($customer) {
                        $this->info($customer ?? "didn't find customer");
                    }

                    if (!$customer) {
                        $customer = Customer::create($customerData);
                        $this->info($customerData ?? " customerdata");
                    }
                }

                if (!$customer || !$customer->id) {
                    throw new \Exception("Failed to find or create customer.");
                }

                $mailData = [
                    'customer_id' => $customer->id,
                    'subject' => $message->subject,
                    'body' => $message->getTextBody() ?? '',
                    'message_id'  => $message->message_id ?? ''
                ];

                $mail = IncomingMail::create($mailData);
                if (!$mail || !$mail->id) {
                    throw new \Exception("Failed to find or create mail.");
                }
                $message->delete($expunge = true);

                $ticketData = [
                    'incoming_mail_id' => $mail->id,
                    'status' => 0,
                    'priority' => 0,
                    'queue_id' => 1
                ];

                // $ticket = Ticket::create($ticketData);
                // if (!$ticket || !$ticket->id) {
                //     throw new \Exception("Failed to create ticket.");
                // }

                Log::error("ticket data" . json_encode($ticketData));
                try {
                    $ticket = Ticket::create($ticketData);
                    if (!$ticket || !$ticket->id) {
                        // throw new \Exception("Failed to create ticket.");
                    }

                    DB::commit();  // Commit the transaction after successfully creating the ticket.
                } catch (\Exception $e) {
                    DB::rollBack();  // Rollback in case of failure
                    Log::error("Error creating ticket: " . $e->getMessage());
                    $this->info($e->getMessage());
                    throw $e;  // Re-throw exception to be caught by the caller
                }
            } catch (Throwable $e) {
                // error_log(json_encode($message));
                Log::error("Error processing message: " . $e->getMessage());
            }
        }, $timeout);
    }
}
