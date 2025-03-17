<?php

namespace App\Services;

use App\Models\Queue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class QueueService {
    public function getAllQueues(): Collection {
        return Queue::all();
    }

    public function getQueueById(int $queueId): Queue {
        return Queue::findOrFail($queueId);
    }

    public function createQueue(array $data): Queue {
        return Queue::create($data);
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
