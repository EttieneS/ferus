<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\OutgoingMailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\DTOs\OutgoingMailDTO;
use App\Models\OutgoingMail;
use Illuminate\Http\JsonResponse;
use App\Models\Ticket;
use App\Mail\GenericMail;
use App\Models\Customer;


class OutgoingMailController extends BaseController {
    protected OutgoingMailService $outgoingMailService;

    public function __construct(OutgoingMailService $outgoingMailService) {
        $this->outgoingMailService = $outgoingMailService;
    }

    public function getRepliesByTicketId(Request $request): JsonResponse {
        try {
            $ticketId = $request->input('ticket_id');

            if (!$ticketId) {
                return $this->sendError('Missing ticket_id in request.', 422);
            }

            $replies = $this->outgoingMailService->getByTicketId($ticketId);

            return $this->sendResponse($replies, 'Replies retrieved successfully');
        } catch (\Throwable $e) {
            Log::error('❌ Failed to fetch replies', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('Failed to fetch replies');
        }
    }



    // public function send(Request $request): JsonResponse {
    //     try {
    //         Log::debug('incoming request data', $request->all());

    //         $dto = OutgoingMailDTO::fromRequest($request);
    //         $ticket = Ticket::with(['queue', 'incomingMail.customer'])->findOrFail($dto->outgoingMail->ticket_id);

    //         $mailer = $ticket->queue->mailer;

    //         $toEmail = app()->environment('local')
    //             ? 'smithettiene@yahoo.com'
    //             : $ticket->incomingMail->customer->email;

    //         if (app()->environment('local')) {
    //             Log::info('📨 Dev mode: overriding recipient to smithettiene@yahoo.com');
    //         }

    //         Log::debug('Prepared to send mail', [
    //             'recipient' => $toEmail,
    //             'subject' => $dto->subject,
    //             'mailer' => $mailer,
    //         ]);

    //         if (!$toEmail) {
    //             Log::warning('No customer email found for ticket ID ' . $ticket->id);
    //             return $this->sendError('No recipient email found.');
    //         }

    //         if (!array_key_exists($mailer, config('mail.mailers'))) {
    //             Log::error("Invalid mailer '{$mailer}' defined in queue ID {$ticket->queue->id}");
    //             return $this->sendError("Mailer '{$mailer}' is not configured.");
    //         }

    //         $sentEmails = $this->outgoingMailService->send($dto);

    //         Log::info("✅ Mail sent via service to recipients:", $sentEmails);

    //         return $this->sendResponse(['sent' => true, 'recipients' => $sentEmails], 'Mail sent successfully');
    //     } catch (\Throwable $e) {
    //         Log::error('❌ Mail send failed', [
    //             'exception' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return $this->sendError('Mail send failed');
    //     }

    //     return response()->json(['message' => 'Unreachable'], 500);
    // }

    // public function send(Request $request): JsonResponse {
    //     try {
    //         Log::debug('📥 incoming request data', $request->all());

    //         $dto = OutgoingMailDTO::fromRequest($request);
    //         $sentTo = $this->outgoingMailService->send($dto);

    //         Log::info("✅ Mail sent via service to recipients:", $sentTo);

    //         return $this->sendResponse(['sent' => true, 'recipients' => $sentTo], 'Mail sent successfully');
    //     } catch (\Throwable $e) {
    //         Log::error('❌ Mail send failed', [
    //             'exception' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return $this->sendError('Mail send failed');
    //     }
    // }

    // public function send(Request $request): JsonResponse {
    //     try {
    //         $dto = OutgoingMailDTO::fromRequest($request);
    //         $result = $this->outgoingMailService->send($dto);

    //         return $this->sendResponse([
    //             'sent' => true,
    //             'recipients' => $result['recipients'],
    //             'mail_id' => $result['mail_id'],
    //         ], 'Mail sent successfully');
    //     } catch (\Throwable $e) {
    //         Log::error('❌ Mail send failed', [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);

    //         return $this->sendError('Mail send failed');
    //     }
    // }

    // public function getById(Request $request): JsonResponse {
    //     $request->validate([
    //         'id' => 'required|integer|exists:outgoing_mails,id',
    //     ]);

    //     try {
    //         $mail = OutgoingMail::with(['recipients', 'ticket.queue', 'ticket.incomingMail.customer'])->findOrFail($request->id);

    //         $groupedRecipients = $mail->recipients->groupBy('recipient_type');

    //         return $this->sendResponse([
    //             'id' => $mail->id,
    //             'ticket_id' => $mail->ticket_id,
    //             'user_id' => $mail->user_id,
    //             'subject' => $mail->subject,
    //             'body' => $mail->body,
    //             'created_at' => $mail->created_at->toDateTimeString(),
    //             'to' => $groupedRecipients->get('to') ?? [],
    //             'cc' => $groupedRecipients->get('cc') ?? [],
    //             'ticket' => $mail->ticket,
    //         ], 'Mail fetched successfully');
    //     } catch (\Throwable $e) {
    //         Log::error('❌ Failed to fetch mail by ID', [
    //             'id' => $request->id,
    //             'error' => $e->getMessage(),
    //         ]);

    //         return $this->sendError('Mail not found or error occurred.');
    //     }
    // }

    // public function send(Request $request): JsonResponse {
    //     try {
    //         $dto = OutgoingMailDTO::fromRequest($request);

    //         $dto->to = array_map(function ($recipient) {
    //             if (is_string($recipient) && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    //                 $customer = Customer::firstOrCreate(
    //                     ['email' => $recipient],
    //                     ['full_name' => 'Unknown', 'phone' => null, 'address' => null]
    //                 );
    //                 return $customer;
    //             }
    //             return $recipient;
    //         }, $dto->to);

    //         if ($dto->cc) {
    //             $dto->cc = array_map(function ($recipient) {
    //                 if (is_string($recipient) && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    //                     $customer = Customer::firstOrCreate(
    //                         ['email' => $recipient],
    //                         ['full_name' => 'Unknown', 'phone' => null, 'address' => null]
    //                     );
    //                     return $customer;
    //                 }
    //                 return $recipient;
    //             }, $dto->cc);
    //         }

    //         $result = $this->outgoingMailService->send($dto);

    //         Log::info("✅ Mail sent via service to recipients: ", $result);
    //         return $this->sendResponse($result, 'Mail sent successfully');
    //     } catch (\Throwable $e) {
    //         Log::error('❌ Mail send failed', [
    //             'exception' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);

    //         return $this->sendError('Mail send failed');
    //     }
    // }

    public function send(Request $request): JsonResponse {
        try {
            $dto = OutgoingMailDTO::fromRequest($request);

            $normalizeRecipients = function (array $recipients): array {
                return array_map(function ($recipient) {
                    if (is_string($recipient) && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                        return Customer::firstOrCreate(
                            ['email' => $recipient],
                            ['full_name' => 'Unknown', 'phone' => null, 'address' => null]
                        );
                    }
                    return $recipient;
                }, $recipients);
            };

            $dto->to = $normalizeRecipients($dto->to);
            if ($dto->cc) $dto->cc = $normalizeRecipients($dto->cc);

            $result = $this->outgoingMailService->send($dto);

            Log::info("✅ Mail sent via service to recipients: ", $result);
            return $this->sendResponse($result, 'Mail sent successfully');
        } catch (\Throwable $e) {
            Log::error('❌ Mail send failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->sendError('Mail send failed');
        }
    }


    // public function getByTicketId(Request $request): JsonResponse {
    //     $ticketId = $request->input('ticket_id');

    //     if (!$ticketId) {
    //         return $this->sendError('Ticket ID is required');
    //     }

    //     try {
    //         $replies = $this->outgoingMailService->getByTicketId($ticketId);
    //         return $this->sendResponse($replies, 'Replies fetched successfully');
    //     } catch (\Throwable $e) {
    //         Log::error('❌ Failed to fetch replies', [
    //             'exception' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return $this->sendError('Failed to fetch replies');
    //     }
    // }

    public function getByTicketId(Request $request): JsonResponse {
        $ticketId = (int) $request->input('ticket_id');

        if (!$ticketId) {
            return $this->sendError('Ticket ID is required');
        }

        try {
            $replies = $this->outgoingMailService->getByTicketId($ticketId);
            return $this->sendResponse($replies, 'Replies fetched successfully');
        } catch (\Throwable $e) {
            Log::error('❌ Failed to fetch replies', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->sendError('Failed to fetch replies');
        }
    }



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
