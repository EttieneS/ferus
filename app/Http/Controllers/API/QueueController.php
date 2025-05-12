<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;
use App\DTOs\QueueDTO;
use Illuminate\Support\Facades\Log;
use App\Models\Queue;

class QueueController extends BaseController {
    protected $queueService;

    public function __construct(QueueService $queueService) {
        $this->queueService = $queueService;
    }

    public function index(): JsonResponse {
        return response()->json($this->queueService->getAllQueues());
    }

    public function show(int $queueId): JsonResponse {
        return response()->json($this->queueService->getQueueById($queueId));
    }

    public function store(Request $request): JsonResponse {
        Log::info($request . " request store");

        try {
            $dto = QueueDTO::fromRequest($request);

            $queue = new Queue([
                'name' => $dto->name,
                'mailer' => $dto->mailer,
                'host' => $dto->host,
                'port' => $dto->port,
                'encryption' => $dto->encryption,
                'username' => $dto->username,
                'password' => $dto->password,
                'from_name' => $dto->fromName,
                'from_email' => $dto->fromEmail,
                'sla_id' => $dto->slaId,
            ]);

            Log::info('Passing built Queue to service', $queue->toArray());

            $this->queueService->createQueue($queue);

            return $this->sendResponse(null, 'Queue created successfully.');
        } catch (\Throwable $e) {
            Log::error('Queue creation failed:', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return $this->sendError('Queue creation failed.', 500);
        }
    }


    public function update(Request $request, int $queueId): JsonResponse {
        $validated = $request->validate([
            'name' => 'required|string|unique:queues,name,' . $queueId . '|max:255'
        ]);

        return response()->json($this->queueService->updateQueue($queueId, $validated));
    }

    public function destroy(int $queueId): JsonResponse {
        $this->queueService->deleteQueue($queueId);
        return response()->json(['message' => 'Queue deleted successfully']);
    }
}
