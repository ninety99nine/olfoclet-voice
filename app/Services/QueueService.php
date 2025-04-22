<?php

namespace App\Services;

use App\Models\Queue;
use App\Http\Resources\QueueResource;
use App\Http\Resources\QueueResources;

class QueueService extends BaseService
{
    /**
     * Show queues.
     *
     * @param string|null $organizationId
     * @return QueueResources|array
     */
    public function showQueues(?string $organizationId = null): QueueResources|array
    {
        $query = Queue::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create queue.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createQueue(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;
        $queue = Queue::create($data);

        return $this->showCreatedResource($queue);
    }

    /**
     * Show queue.
     *
     * @param string $queueId
     * @return QueueResource
     */
    public function showQueue(string $queueId): QueueResource
    {
        $queue = Queue::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($queueId);
        return $this->showResource($queue);
    }

    /**
     * Update queue.
     *
     * @param string $queueId
     * @param array $data
     * @return array
     */
    public function updateQueue(string $queueId, array $data): array
    {
        $queue = Queue::findOrFail($queueId);
        $queue->update($data);

        return $this->showUpdatedResource($queue);
    }

    /**
     * Delete queue.
     *
     * @param string $queueId
     * @return array
     */
    public function deleteQueue(string $queueId): array
    {
        $queue = Queue::findOrFail($queueId);

        $deleted = $queue->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Queue deleted'];
        }

        return ['deleted' => false, 'message' => 'Queue delete unsuccessful'];
    }

    /**
     * Delete queues.
     *
     * @param string|null $organizationId
     * @param array $queueIds
     * @return array
     */
    public function deleteQueues(?string $organizationId, array $queueIds): array
    {
        $query = Queue::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $queueIds);

        $queues = $query->get();

        if ($totalQueues = $queues->count()) {
            $queues->each->delete();
            return ['deleted' => true, 'message' => "$totalQueues queue(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No queues deleted'];
    }
}
