<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\IncomingMailService;
use Illuminate\Http\JsonResponse;

class IncomingMailController extends Controller {
    protected $incomingMailService;

    public function __construct(IncomingMailService $incomingMailService) {
        $this->incomingMailService = $incomingMailService;
    }

    public function store(Request $request): JsonResponse {
        // $validated = $request->validate([
        //     'customer_id' => 'required|exists:customers,id',
        //     'subject'     => 'required|string|max:255',
        //     'message'     => 'required|string',
        //     'message_id'  => 'required|string|unique:incoming_mails,message_id'
        // ]);

        $mailData = $request->all();

        $mail = $this->incomingMailService->createMail($mailData);
        return response()->json($mail, 201);
    }
}
