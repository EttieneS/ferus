<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;
use App\Models\Ticket;

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
            $this->info("New message received: " . $message->subject);
            $ticket = [
                'title' => $message->subject,
                'subject' => $message->getTextBody(),
                'message_id' => $message->message_id,
            ];

            Ticket::create($ticket);

            $message->delete($expunge = true);
        }, $timeout);
    }
}
