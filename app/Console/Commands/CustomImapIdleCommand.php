<?php

namespace App\Console\Commands;

use Webklex\PHPIMAP\Message;
use Webklex\IMAP\Commands\ImapIdleCommand;
use App\Http\Controllers\API\TicketController;
use App\Models\Ticket;

class CustomImapIdleCommand extends ImapIdleCommand {
    
    protected $signature = 'app:custom-imap-idle-command';    
    protected $description = 'Listen for new commands';
    protected $account = "default"; 
    
    public function onNewMessage(Message $message) {
        // while(true) {
            $this->info("New message received: " . $message->subject); 
            $this->info("Message id: " . $message->getTextBody());                

            if (!(Ticket::where('message_id', '=', $message->message_id))) {
                $ticket = [
                    'title' => $message->subject,
                    'subject' => $message->getTextBody(),
                    'message_id' => $message->message_id,
                ];

                Ticket::create($ticket);

                $message->delete($expunge = true);
            }
        //     // $ticketsController = new TicketController();
        //     // $`ticketsController->createTicketFromMessage($message);
        // }        
    }
}
