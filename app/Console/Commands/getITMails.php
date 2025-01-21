<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
// use Webklex\IMAP\Facades\Client as ClientFacade;
use Webklex\IMAP\Facades\Client;
use App\Models\Ticket;
use Webklex\PHPIMAP\Exceptions\ConnectionFailedException;
use Webklex\PHPIMAP\Exceptions\FolderFetchingException;
use Throwable;
use Illuminate\Support\Facades\Log;

class getITMails extends Command {

    protected $signature = 'app:listen-it-mails';    
    protected $description = 'Listen to IT mail inbox';
    protected $account = "it";
    
    public function handle() {
        $client = Client::account("it");
        // if (is_array($this->account)) {
        //     $client = ClientFacade::make($this->account);
        // }else{
        //     $client = ClientFacade::account($this->account);
        // }

        // $client->connect();

        try {
            $client->connect();
        } catch (ConnectionFailedException $e) {
            Log::error($e->getMessage());
            return 1;
        }

        $folder = $client->getFolderByName('INBOX');
        $timeout = 1200;
                        
        $folder->idle(function ($message) {
            try {                
                $this->info("New message received: " . $message->subject);
                $this->info("from ". $message->getFrom()[0]->mail);                
                $this->info("full_name ". json_encode($message->getFrom()[0]->personal));
                $this->info("subject". json_encode($message->getTextBody()));

                $ticket = [
                    'from' => $message->getFrom()[0]->mail,
                    'title' => $message->subject,
                    'full_name' => $message->getFrom()[0]->personal,
                    'subject' => $message->getTextBody(),
                    'message_id' => $message->message_id,
                    'queue_id' => 2
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
