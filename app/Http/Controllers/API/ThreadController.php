<?php
namespace App\Http\Controllers\API;

use App\Models\Mail;
use App\Models\Note;
use App\Services\ThreadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ThreadController extends BaseController {
    
    public function __construct(
        private ThreadService $threadService
    ) {}

    public function getByTicketId(Request $request): JsonResponse {        
        $threadItemDTO = $request->input('data');
        
        $threads = $this->threadService->getByTicketId($threadItemDTO);
        $message = "Successfully retrieved thread items";
        
        return $this->sendResponse($threads, $message);
    }
}
