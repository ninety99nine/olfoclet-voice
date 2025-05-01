<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\ConversationMessage;
use App\Http\Controllers\BaseController;
use App\Services\ConversationMessageService;
use App\Http\Resources\ConversationMessageResources;
use App\Http\Requests\ConversationMessage\ShowConversationMessageRequest;
use App\Http\Requests\ConversationMessage\ShowConversationMessagesRequest;
use App\Http\Requests\ConversationMessage\CreateConversationMessageRequest;
use App\Http\Requests\ConversationMessage\UpdateConversationMessageRequest;
use App\Http\Requests\ConversationMessage\DeleteConversationMessageRequest;
use App\Http\Requests\ConversationMessage\DeleteConversationMessagesRequest;

class ConversationMessageController extends BaseController
{
    /**
     * @var ConversationMessageService
     */
    protected $service;

    /**
     * ConversationMessageController constructor.
     *
     * @param ConversationMessageService $service
     */
    public function __construct(ConversationMessageService $service)
    {
        $this->service = $service;
    }

    /**
     * Show conversation messages.
     *
     * @param ShowConversationMessagesRequest $request
     * @return ConversationMessageResources|JsonResponse
     */
    public function showConversationMessages(ShowConversationMessagesRequest $request): ConversationMessageResources|JsonResponse
    {
        $threadId = $request->query('thread_id');
        return $this->prepareOutput($this->service->showConversationMessages($threadId));
    }

    /**
     * Create conversation message.
     *
     * @param CreateConversationMessageRequest $request
     * @return JsonResponse
     */
    public function createConversationMessage(CreateConversationMessageRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createConversationMessage($request->validated()), 201);
    }

    /**
     * Show single conversation message.
     *
     * @param ShowConversationMessageRequest $request
     * @param ConversationMessage $conversationMessage
     * @return JsonResponse
     */
    public function showConversationMessage(ShowConversationMessageRequest $request, ConversationMessage $conversationMessage): JsonResponse
    {
        return $this->prepareOutput($this->service->showConversationMessage($conversationMessage->id));
    }

    /**
     * Update conversation message.
     *
     * @param UpdateConversationMessageRequest $request
     * @param ConversationMessage $conversationMessage
     * @return JsonResponse
     */
    public function updateConversationMessage(UpdateConversationMessageRequest $request, ConversationMessage $conversationMessage): JsonResponse
    {
        return $this->prepareOutput($this->service->updateConversationMessage($conversationMessage->id, $request->validated()));
    }

    /**
     * Delete conversation message.
     *
     * @param DeleteConversationMessageRequest $request
     * @param ConversationMessage $conversationMessage
     * @return JsonResponse
     */
    public function deleteConversationMessage(DeleteConversationMessageRequest $request, ConversationMessage $conversationMessage): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteConversationMessage($conversationMessage->id));
    }

    /**
     * Delete multiple conversation messages.
     *
     * @param DeleteConversationMessagesRequest $request
     * @return JsonResponse
     */
    public function deleteConversationMessages(DeleteConversationMessagesRequest $request): JsonResponse
    {
        $messageIds = request()->input('message_ids', []);
        $threadId = $request->query('thread_id');
        return $this->prepareOutput($this->service->deleteConversationMessages($threadId, $messageIds));
    }
}
