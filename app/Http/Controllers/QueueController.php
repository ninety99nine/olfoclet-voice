<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\QueueResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Queue\ShowQueuesRequest;
use App\Http\Requests\Queue\CreateQueueRequest;
use App\Http\Requests\Queue\ShowQueueRequest;
use App\Http\Requests\Queue\UpdateQueueRequest;
use App\Http\Requests\Queue\DeleteQueueRequest;
use App\Http\Requests\Queue\DeleteQueuesRequest;

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
     * @param ShowQueuesRequest $request
     * @return QueueResources|JsonResponse
     */
    public function showQueues(ShowQueuesRequest $request): QueueResources|JsonResponse
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
     * @param ShowQueueRequest $request
     * @param Queue $queue
     * @return JsonResponse
     */
    public function showQueue(ShowQueueRequest $request, Queue $queue): JsonResponse
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
     * @param DeleteQueueRequest $request
     * @param Queue $queue
     * @return JsonResponse
     */
    public function deleteQueue(DeleteQueueRequest $request, Queue $queue): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteQueue($queue->id));
    }

    /**
     * Delete multiple queues.
     *
     * @param DeleteQueuesRequest $request
     * @return JsonResponse
     */
    public function deleteQueues(DeleteQueuesRequest $request): JsonResponse
    {
        $queueIds = request()->input('queue_ids', []);
        return $this->prepareOutput($this->service->deleteQueues(request('organization_id'), $queueIds));
    }
}
