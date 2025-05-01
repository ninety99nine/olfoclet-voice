<?php

namespace App\Services;

use App\Models\ConversationMessage;
use App\Http\Resources\ConversationMessageResource;
use App\Http\Resources\ConversationMessageResources;

class ConversationMessageService extends BaseService
{
    /**
     * Show conversation messages.
     *
     * @param string|null $threadId
     * @return ConversationMessageResources|array
     */
    public function showConversationMessages(?string $threadId = null): ConversationMessageResources|array
    {
        $query = ConversationMessage::query()
            ->whereHas('thread', fn($q) => $q->where('user_id', auth()->id()))
            ->when($threadId, fn($query) => $query->where('thread_id', $threadId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create conversation message.
     *
     * @param array $data
     * @return array
     */
    public function createConversationMessage(array $data): array
    {
        $message = ConversationMessage::create($data);

        return $this->showCreatedResource($message);
    }

    /**
     * Show conversation message.
     *
     * @param string $messageId
     * @return ConversationMessageResource
     */
    public function showConversationMessage(string $messageId): ConversationMessageResource
    {
        $message = ConversationMessage::query()
            ->whereHas('thread', fn($q) => $q->where('user_id', auth()->id()))
            ->with($this->getRequestRelationships())
            ->findOrFail($messageId);
        return $this->showResource($message);
    }

    /**
     * Update conversation message.
     *
     * @param string $messageId
     * @param array $data
     * @return array
     */
    public function updateConversationMessage(string $messageId, array $data): array
    {
        $message = ConversationMessage::query()
            ->whereHas('thread', fn($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($messageId);
        $message->update($data);

        return $this->showUpdatedResource($message);
    }

    /**
     * Delete conversation message.
     *
     * @param string $messageId
     * @return array
     */
    public function deleteConversationMessage(string $messageId): array
    {
        $message = ConversationMessage::query()
            ->whereHas('thread', fn($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($messageId);

        $deleted = $message->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Conversation message deleted'];
        }

        return ['deleted' => false, 'message' => 'Conversation message delete unsuccessful'];
    }

    /**
     * Delete conversation messages.
     *
     * @param string|null $threadId
     * @param array $messageIds
     * @return array
     */
    public function deleteConversationMessages(?string $threadId, array $messageIds): array
    {
        $query = ConversationMessage::query()
            ->whereHas('thread', fn($q) => $q->where('user_id', auth()->id()))
            ->when($threadId, fn($query) => $query->where('thread_id', $threadId))
            ->whereIn('id', $messageIds);

        $messages = $query->get();

        if ($totalMessages = $messages->count()) {
            $messages->each->delete();
            return ['deleted' => true, 'message' => "$totalMessages conversation message(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No conversation messages deleted'];
    }
}
