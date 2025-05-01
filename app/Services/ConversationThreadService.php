<?php

namespace App\Services;

use App\Models\ConversationThread;
use App\Http\Resources\ConversationThreadResource;
use App\Http\Resources\ConversationThreadResources;

class ConversationThreadService extends BaseService
{
    /**
     * Show conversation threads.
     *
     * @param string|null $copilotId
     * @return ConversationThreadResources|array
     */
    public function showConversationThreads(?string $copilotId = null): ConversationThreadResources|array
    {
        $query = ConversationThread::query()
            ->where('user_id', auth()->id())
            ->when($copilotId, fn($query) => $query->where('copilot_id', $copilotId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create conversation thread.
     *
     * @param array $data
     * @return array
     */
    public function createConversationThread(array $data): array
    {
        $data['user_id'] = auth()->id();
        $thread = ConversationThread::create($data);

        return $this->showCreatedResource($thread);
    }

    /**
     * Show conversation thread.
     *
     * @param string $threadId
     * @return ConversationThreadResource
     */
    public function showConversationThread(string $threadId): ConversationThreadResource
    {
        $thread = ConversationThread::query()
            ->where('user_id', auth()->id())
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($threadId);
        return $this->showResource($thread);
    }

    /**
     * Update conversation thread.
     *
     * @param string $threadId
     * @param array $data
     * @return array
     */
    public function updateConversationThread(string $threadId, array $data): array
    {
        $thread = ConversationThread::where('user_id', auth()->id())
            ->findOrFail($threadId);
        $thread->update($data);

        return $this->showUpdatedResource($thread);
    }

    /**
     * Delete conversation thread.
     *
     * @param string $threadId
     * @return array
     */
    public function deleteConversationThread(string $threadId): array
    {
        $thread = ConversationThread::where('user_id', auth()->id())
            ->findOrFail($threadId);

        $deleted = $thread->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Conversation thread deleted'];
        }

        return ['deleted' => false, 'message' => 'Conversation thread delete unsuccessful'];
    }

    /**
     * Delete conversation threads.
     *
     * @param array $threadIds
     * @return array
     */
    public function deleteConversationThreads(array $threadIds): array
    {
        $query = ConversationThread::query()
            ->where('user_id', auth()->id())
            ->whereIn('id', $threadIds);

        $threads = $query->get();

        if ($totalThreads = $threads->count()) {
            $threads->each->delete();
            return ['deleted' => true, 'message' => "$totalThreads conversation thread(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No conversation threads deleted'];
    }
}
