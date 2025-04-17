<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\QueueResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Queue\CreateQueueRequest;
use App\Http\Requests\Queue\UpdateQueueRequest;

class QueueController extends BaseController
{
    /**
     * @var QueueService
     */
    protected $service;

    /**
     * QueueController constructor.
     *
     * @param QueueService $service
     */
    public function __construct(QueueService $service)
    {
        $this->service = $service;
    }

    /**
     * Show queues.
     *
     * @return QueueResources|JsonResponse
     */
    public function showQueues(): QueueResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showQueues(request('organization_id')));
    }

    /**
     * Create queue.
     *
     * @param CreateQueueRequest $request
     * @return JsonResponse
     */
    public function createQueue(CreateQueueRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createQueue(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Show single queue.
     *
     * @param Queue $queue
     * @return JsonResponse
     */
    public function showQueue(Queue $queue): JsonResponse
    {
        return $this->prepareOutput($this->service->showQueue($queue->id));
    }

    /**
     * Update queue.
     *
     * @param UpdateQueueRequest $request
     * @param Queue $queue
     * @return JsonResponse
     */
    public function updateQueue(UpdateQueueRequest $request, Queue $queue): JsonResponse
    {
        return $this->prepareOutput($this->service->updateQueue($queue->id, $request->validated()));
    }

    /**
     * Delete queue.
     *
     * @param Queue $queue
     * @return JsonResponse
     */
    public function deleteQueue(Queue $queue): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteQueue($queue->id));
    }

    /**
     * Delete multiple queues.
     *
     * @return JsonResponse
     */
    public function deleteQueues(): JsonResponse
    {
        $queueIds = request()->input('queue_ids', []);
        return $this->prepareOutput($this->service->deleteQueues(request('organization_id'), $queueIds));
    }
}
