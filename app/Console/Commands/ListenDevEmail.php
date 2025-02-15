<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Ticket;
use App\Models\Customer;
use Throwable;

class ListenDevEmail extends Command
{

    protected $signature = 'app:listen-dev-email';
    protected $description = 'Command description';

    public function handle()
    {
        $client = Client::account("default");
        $client->connect();

        $folder = $client->getFolderByName('INBOX');
        $timeout = 1200;

        $folder->idle(function ($message) {
            try {
                $this->info("New message received: " . $message->subject);
                $this->info("from " . $message->getFrom()[0]->mail);

                $ticket = [
                    //TODO from and full name is duplicate data in customer
                    'from' => $message->getFrom()[0]->mail,
                    'title' => $message->subject,
                    'full_name' => $message->getFrom()[0]->personal,
                    'subject' => $message->getTextBody(),
                    'message_id' => $message->message_id,
                    'queue_id' => 1
                ];

                $customer = [
                    'full_name' => $message->getFrom()[0]->personal,
                    'email' => $message->getFrom()[0]->mail,
                ];


                $customer = Customer::where('email', $customer['email'])->first();
                if (!$customer) {
                    Customer::create($customer);            
                }

                Ticket::create($ticket);

            } catch (Throwable $e) {
                error_log(json_encode($message));
                error_log($e->getMessage());
            }

            $message->delete($expunge = true);
        }, $timeout);
    }
}
