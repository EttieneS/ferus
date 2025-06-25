<?php

namespace App\Services;

use App\Models\Queue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Exception;
use Illuminate\Support\Facades\Log;
class QueueService {
    public function getAllQueues(): Collection {
        return Queue::with('sla')->get();
    }

    public function getQueueById(int $queueId): Queue
    {
        return Queue::findOrFail($queueId);
    }

    public function createQueue(Queue $queue): void
    {
        if ($queue->password) {
            $queue->password = Crypt::encryptString($queue->password);
        }

        $result = $queue->save();

        \Log::debug('Save result:', ['result' => $result]);
    }

    public function updateQueue(Queue $queue): array {
        try {
            Log::info($queue->id . " queue id ");
            $existing = Queue::findOrFail($queue->id);
            $existing->fill($queue->getAttributes());
            $existing->save();

            return [
                'success' => true,
                'message' => $queue->namme . ' updated successfully.'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => [$e->getMessage()],
                'code' => $e->getCode()
            ];
        }
    }

    public function deleteQueue(int $queueId): bool
    {
        $queue = Queue::findOrFail($queueId);
        return $queue->delete();
    }
}
