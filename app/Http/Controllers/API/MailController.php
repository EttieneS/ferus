<?php

namespace App\Http\Controllers\API;

use App\DTOs\MailDTO;
use App\Http\Controllers\API\BaseController;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MailController extends BaseController {
    protected MailService $mailService;

    public function __construct(MailService $mailService) {
        $this->mailService = $mailService;
    }

    public function send(Request $request) {
        Log::info("mail controller");
        $dto = MailDTO::fromRequest($request);
        $result = $this->mailService->send($dto);

        return $this->sendResponse($result, 'Mail sent successfully.');
    }

    public function getByTicketId(int $ticketId) {
        $replies = $this->mailService->getByTicketId($ticketId);

        return $this->sendResponse($replies, 'Mail fetched successfully.');
    }
}
