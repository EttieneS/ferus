<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\API\BaseController as BaseController;

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
        $validated = $request->validate([
            'name' => 'required|string|unique:queues|max:255'
        ]);

        return response()->json($this->queueService->createQueue($validated), 201);
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
