<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Ticket;
use App\Models\Customer;
use App\Models\Mail;
use Throwable;

class ListenDevEmail extends Command
{
    protected $signature = 'app:listen-dev-email';
    protected $description = 'Command to listen for new emails and create tickets';

    public function handle()
    {
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
                    
                    if (!$customer) {
                        $customer = Customer::create($customerData);
                    }
                }

                if (!$customer || !$customer->id) {
                    throw new \Exception("Failed to find or create customer.");
                }
                
                $mailData = [
                    'customer_id' => $customer->id,                    
                    'subject'       => $message->subject,
                    'message'     => $message->getTextBody() ?? '',
                    'message_id'  => $message->message_id ?? ''                    
                ];

                $mail = Mail::create($mailData);
                if (!$mail || !$mail->id) {
                    throw new \Exception("Failed to find or create mail.");
                }
                $message->delete($expunge = true);

                $ticketData = [
                    'mail_id' => $mail->id,                                     
                    'status' => 0,
                    'priority' => 0,
                    'queue_id' => 1
                ];

                $ticket = Ticket::create($ticketData);
                if (!$ticket || !$ticket->id) {
                    throw new \Exception("Failed to create ticket.");
                }

            } catch (Throwable $e) {
                // error_log(json_encode($message));
                error_log("Error processing message: " . $e->getMessage());
            }
        }, $timeout);
    }
}
