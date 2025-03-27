<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
// use Webklex\PHPIMAP\Client;
use App\Mail\GenericMail;
use App\Models\TicketReply;
use Exception;
use Illuminate\Support\Facades\Log;

class EmailService {
    // private $client;

    // public function __construct() {
    //     $this->client = new Client([
    //         'host'          => config('imap.accounts.default.host'),
    //         'port'          => config('imap.accounts.default.port'),
    //         'encryption'    => config('imap.accounts.default.encryption'),
    //         'validate_cert' => config('imap.accounts.default.validate_cert'),
    //         'username'      => config('imap.accounts.default.username'),
    //         'password'      => config('imap.accounts.default.password'),
    //     ]);
    //     $this->client->connect();
    // }

    // public function getInboxMessages() {
    //     $folder = $this->client->getFolder('INBOX');
    //     return $folder->messages()->all()->get();
    // }

    // public function sendEmail(TicketReply $reply): array {
    //     // try {
    //     //     // Store the reply in the database
    //     //     // $reply->save();

    //     //     // Send the email
    //     //     $email = new GenericMail($reply->subject, $reply->body);
    //     //     Mail::mailer($reply->mailer)->to($reply->ticket->customer->email)->send($email);

    //     //     Log::info("Ticket reply sent for ticket ID {$reply->ticket_id} using {$reply->mailer}.");
    //     //     return ['status' => 'success', 'message' => 'Reply sent and saved successfully.'];
    //     // } catch (Exception $e) {
    //     //     Log::error("Failed to send ticket reply: " . $e->getMessage());
    //     //     return ['status' => 'error', 'message' => 'Failed to send reply.'];
    //     // }

    //     try {
    //         // Retrieve related ticket and customer email
    //         $ticket = $reply->ticket;

    //         if (!$ticket) {
    //             throw new Exception("Ticket not found for reply ID: {$reply->id}");
    //         }

    //         $customerEmail = $ticket->customer->email ?? null;

    //         if (!$customerEmail) {
    //             throw new Exception("Customer email not found for ticket ID: {$ticket->id}");
    //         }

    //         // Send email
    //         $email = new GenericMail($reply->subject, $reply->body);
    //         Mail::mailer($reply->mailer)->to($customerEmail)->send($email);

    //         Log::info("Ticket reply sent for ticket ID {$reply->ticket_id} using {$reply->mailer}.");
    //         return ['status' => 'success', 'message' => 'Reply sent and saved successfully.'];
    //     } catch (Exception $e) {
    //         Log::error("Failed to send ticket reply: " . $e->getMessage());
    //         return ['status' => 'error', 'message' => 'Failed to send reply.', 'error' => $e->getMessage()];
    //     }
    // }
}
