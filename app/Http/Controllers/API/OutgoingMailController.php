<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\OutgoingMailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OutgoingMailController extends BaseController {
    protected $outgoingMailService;

    public function __construct(OutgoingMailService $outgoingMailService) {
        $this->outgoingMailService = $outgoingMailService;
    }

    public function send(Request $request) {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'subject' => 'required|string',
            'body' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $outgoingMail = $this->outgoingMailService->send($validated);

        return response()->json([
            'message' => 'Mail sent successfully.',
            'data' => $outgoingMail,
        ], 201);
    }

    // public function testSend() {
    //     try {
    //         $data = [
    //             'ticket_id' => 2,
    //             'subject' => 'Test Reply from Controller',
    //             'body' => '<p>This is a <strong>test reply</strong> to a ticket via test method.</p>',
    //             'user_id' => 1,
    //         ];

    //         $outgoingMail = $this->outgoingMailService->send($data);

    //         return $this->sendResponse($outgoingMail, 'Test mail sent successfully.');
    //     } catch (\Exception $e) {
    //         return $this->sendError('Failed to send test mail.', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ], 500);
    //     }
    // }

    public function testSend() {
        try {
            $data = [
                'ticket_id' => 3,
                'subject' => 'Test Reply from Controller',
                'body' => '<p>This is a <strong>test reply</strong> to a ticket via test method.</p>',
                'user_id' => 1,
            ];

            $ticket = \App\Models\Ticket::with(['queue', 'incomingMail.customer'])->findOrFail($data['ticket_id']);
            $mailer = $ticket->queue->mailer ?? config('mail.default');
            $toEmail = $ticket->incomingMail->customer->email;

            \Illuminate\Support\Facades\Mail::mailer($mailer)->send([], [], function ($message) use ($data, $toEmail) {
                $message->to($toEmail)
                    ->subject($data['subject'])
                    ->html($data['body']);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Mail sent successfully',
                'data' => [
                    'ticket_id' => $ticket->id,
                    'to' => $toEmail,
                    'subject' => $data['subject'],
                    'mailer' => $mailer,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mail failed to send',
                'error' => $e->getMessage(),
            ], 500);
        }
        //     try {
        //         $data = [
        //             'ticket_id' => 2,
        //             'subject' => 'Test Reply from Controller',
        //             'body' => '<p>This is a <strong>test reply</strong> to a ticket via test method.</p>',
        //             'user_id' => 1,
        //         ];

        //         $ticket = \App\Models\Ticket::with(['queue', 'incomingMail.customer'])->findOrFail($data['ticket_id']);
        //         $mailer = $ticket->queue->mailer ?? config('mail.default');
        //         $toEmail = $ticket->incomingMail->customer->email;

        //         Log::info("Using mailer: {$mailer} for ticket ID {$ticket->id}");


        //         Mail::mailer($mailer)->send([], [], function ($message) use ($data, $toEmail) {
        //             $message->to($toEmail)
        //                 ->bcc('smithettiene@yahoo.com')
        //                 ->subject($data['subject'])
        //                 ->html($data['body']);
        //         });

        //         return $this->sendResponse([
        //             'to' => $toEmail,
        //             'mailer' => $mailer,
        //             'ticket_id' => $ticket->id,
        //             'subject' => $data['subject'],
        //         ], 'Test mail sent successfully.');
        //     } catch (\Exception $e) {
        //         return $this->sendError('Failed to send test mail.', [
        //             'message' => $e->getMessage(),
        //             'trace' => $e->getTraceAsString()
        //         ], 500);
        //     }
        // }
    }
}
