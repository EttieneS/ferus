<?php

namespace App\Services;

use App\Models\Queue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Crypt;

class QueueService {
    public function getAllQueues(): Collection {
        return Queue::with('sla')->get();
    }

    public function getQueueById(int $queueId): Queue {
        return Queue::findOrFail($queueId);
    }

    public function createQueue(Queue $queue): void {
        if ($queue->password) {
            $queue->password = Crypt::encryptString($queue->password);
        }

        $result = $queue->save();

        \Log::debug('Save result:', ['result' => $result]);
    }

    public function updateQueue(int $queueId, array $data): Queue {
        $queue = Queue::findOrFail($queueId);
        $queue->update($data);
        return $queue;
    }

    public function deleteQueue(int $queueId): bool {
        $queue = Queue::findOrFail($queueId);
        return $queue->delete();
    }
}
