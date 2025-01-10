<?php
   namespace App\Services;

   use Webklex\PHPIMAP\Client;

   class EmailService {
      private $client;

      public function __construct() {
         $this->client = new Client([
               'host'          => config('imap.accounts.default.host'),
               'port'          => config('imap.accounts.default.port'),
               'encryption'    => config('imap.accounts.default.encryption'),
               'validate_cert' => config('imap.accounts.default.validate_cert'),
               'username'      => config('imap.accounts.default.username'),
               'password'      => config('imap.accounts.default.password'),
         ]);
         $this->client->connect();
      }

      public function getInboxMessages() {
         $folder = $this->client->getFolder('INBOX');
         return $folder->messages()->all()->get();
      }
   }
?>