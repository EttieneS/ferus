<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Ticket;
use Throwable;

class ImapTest extends Command
{    
    protected $signature = 'app:imap-test';
    
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
                $this->info("full_name " . json_encode($message->getFrom()[0]->personal));
                $this->info("subject" . json_encode($message->getTextBody()));

                $ticket = [
                    'from' => $message->getFrom()[0]->mail,
                    'title' => $message->subject,
                    'full_name' => $message->getFrom()[0]->personal,
                    'subject' => $message->getTextBody(),
                    'message_id' => $message->message_id,
                    'queue_id' => 1
                ];

                Ticket::create($ticket);
            } catch (Throwable $e) {
                error_log(json_encode($message));
                error_log($e->getMessage());
            }

            $message->delete($expunge = true);
        }, $timeout);
    }
}
