<?php

namespace App\Services;

use App\Models\TicketReply;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

class TicketReplyService {
    protected EmailService $emailService;

    public function __construct(EmailService $emailService) {
        $this->emailService = $emailService;
    }

    public function createReply(Request $request) {
        $reply = new TicketReply([
            'ticket_id' => $request->input('ticket_id'),
            'user_id' => $request->input('user_id'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
            'mailer' => $request->input('mailer', 'dev'), // Default mailer
        ]);

        // Pass it to the EmailService
        $response = $this->emailService->sendEmail($reply);
        // DB::beginTransaction();
        // try {
        //     // Ensure ticket exists
        //     $ticket = Ticket::findOrFail($data['ticket_id']);

        //     // Create reply in DB
        //     $reply = TicketReply::create([
        //         'ticket_id' => $ticket->id,
        //         'user_id' => $data['user_id'],
        //         'message' => $data['message'],
        //     ]);

        //     // Send Email via EmailService
        //     $this->emailService->sendMail([
        //         'mail_account' => 'it', // Change dynamically if needed
        //         'to' => $ticket->customer->email,
        //         'from' => 'it@example.com',
        //         'subject' => "Reply to Ticket #{$ticket->id}",
        //         'message' => $data['message']
        //     ]);

        //     DB::commit();
        //     return $reply;
        // } catch (Exception $e) {
        //     DB::rollBack();
        //     Log::error("Failed to create ticket reply", ['error' => $e->getMessage()]);
        //     return null;
        // }
    }
}
