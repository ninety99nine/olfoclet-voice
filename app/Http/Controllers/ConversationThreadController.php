<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\ConversationThread;
use App\Http\Controllers\BaseController;
use App\Services\ConversationThreadService;
use App\Http\Resources\ConversationThreadResources;
use App\Http\Requests\ConversationThread\ShowConversationThreadRequest;
use App\Http\Requests\ConversationThread\ShowConversationThreadsRequest;
use App\Http\Requests\ConversationThread\CreateConversationThreadRequest;
use App\Http\Requests\ConversationThread\UpdateConversationThreadRequest;
use App\Http\Requests\ConversationThread\DeleteConversationThreadRequest;
use App\Http\Requests\ConversationThread\DeleteConversationThreadsRequest;

class ConversationThreadController extends BaseController
{
    /**
     * @var ConversationThreadService
     */
    protected $service;

    /**
     * ConversationThreadController constructor.
     *
     * @param ConversationThreadService $service
     */
    public function __construct(ConversationThreadService $service)
    {
        $this->service = $service;
    }

    /**
     * Show conversation threads.
     *
     * @param ShowConversationThreadsRequest $request
     * @return ConversationThreadResources|JsonResponse
     */
    public function showConversationThreads(ShowConversationThreadsRequest $request): ConversationThreadResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showConversationThreads());
    }

    /**
     * Create conversation thread.
     *
     * @param CreateConversationThreadRequest $request
     * @return JsonResponse
     */
    public function createConversationThread(CreateConversationThreadRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createConversationThread($request->validated()), 201);
    }

    /**
     * Show single conversation thread.
     *
     * @param ShowConversationThreadRequest $request
     * @param ConversationThread $conversationThread
     * @return JsonResponse
     */
    public function showConversationThread(ShowConversationThreadRequest $request, ConversationThread $conversationThread): JsonResponse
    {
        return $this->prepareOutput($this->service->showConversationThread($conversationThread->id));
    }

    /**
     * Update conversation thread.
     *
     * @param UpdateConversationThreadRequest $request
     * @param ConversationThread $conversationThread
     * @return JsonResponse
     */
    public function updateConversationThread(UpdateConversationThreadRequest $request, ConversationThread $conversationThread): JsonResponse
    {
        return $this->prepareOutput($this->service->updateConversationThread($conversationThread->id, $request->validated()));
    }

    /**
     * Delete conversation thread.
     *
     * @param DeleteConversationThreadRequest $request
     * @param ConversationThread $conversationThread
     * @return JsonResponse
     */
    public function deleteConversationThread(DeleteConversationThreadRequest $request, ConversationThread $conversationThread): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteConversationThread($conversationThread->id));
    }

    /**
     * Delete multiple conversation threads.
     *
     * @param DeleteConversationThreadsRequest $request
     * @return JsonResponse
     */
    public function deleteConversationThreads(DeleteConversationThreadsRequest $request): JsonResponse
    {
        $threadIds = request()->input('thread_ids', []);
        return $this->prepareOutput($this->service->deleteConversationThreads($threadIds));
    }
}
