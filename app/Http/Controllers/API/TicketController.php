<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;
use Webklex\PHPIMAP\Message;
use App\Models\Ticket;

class TicketController extends BaseController {
    public function createTicketFromMessage(Message $message) {
        $ticket = [
            'title' => $message->title,
            'subject' => $message->subject,
        ];
        
        Ticket::create($ticket);
    }

    public function create(Request $request): JsonResponse {
        $ticket = $request->all();
                
        Ticket::create($ticket);

        return $this->sendResponse('new UsersResource($user)', 'User created successfully.');
    }

    public function test () {
        $test = [
            'title' => "pick",
            "description" => "legend"
        ];
        Ticket::create($test);      
        
        return $this->sendResponse('success', 'User created successfully.');
    }
}
