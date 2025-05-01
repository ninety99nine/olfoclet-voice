<?php

namespace App\Services;

use App\Models\Copilot;
use App\Models\ConversationThread;
use App\Models\ConversationMessage;
use Illuminate\Support\Str;
use App\Http\Resources\CopilotResource;
use App\Http\Resources\CopilotResources;

class CopilotService extends BaseService
{
    /**
     * @var OpenAiService
     */
    protected $openAiService;

    /**
     * @var PineconeService
     */
    protected $pineconeService;

    /**
     * CopilotService constructor.
     *
     * @param OpenAiService $openAiService
     * @param PineconeService $pineconeService
     */
    public function __construct(OpenAiService $openAiService, PineconeService $pineconeService)
    {
        $this->openAiService = $openAiService;
        $this->pineconeService = $pineconeService;
    }

    /**
     * Show Copilots.
     *
     * @param string|null $organizationId
     * @return CopilotResources|array
     */
    public function showCopilots(?string $organizationId = null): CopilotResources|array
    {
        $query = Copilot::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create Copilot.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createCopilot(string $organizationId, array $data): array
    {
        $userIds = $data['user_ids'] ?? [];
        $knowledgeBaseIds = $data['knowledge_base_ids'] ?? [];

        $data['organization_id'] = $organizationId;
        unset($data['knowledge_base_ids'], $data['user_ids']);

        $copilot = Copilot::create($data);

        // Attach knowledge bases
        if (!empty($knowledgeBaseIds)) {
            $copilot->knowledgeBases()->sync($knowledgeBaseIds);
        }

        // Attach users
        if (!empty($userIds)) {
            $copilot->users()->sync($userIds);
        }

        return $this->showCreatedResource($copilot);
    }

    /**
     * Delete Copilot.
     *
     * @param string $copilotId
     * @return array
     */
    public function deleteCopilot(string $copilotId): array
    {
        $copilot = Copilot::findOrFail($copilotId);

        if($copilot) {

            $deleted = $copilot->delete();

            if ($deleted) {
                return ['deleted' => true, 'message' => 'Copilot deleted'];
            }else{
                return ['deleted' => false, 'message' => 'Copilot delete unsuccessful'];
            }

        }else{
            return ['deleted' => false, 'message' => 'This Copilot does not exist'];
        }
    }

    /**
     * Delete Copilots.
     *
     * @param string|null $organizationId
     * @param array $copilotIds
     * @return array
     */
    public function deleteCopilots(?string $organizationId, array $copilotIds): array
    {
        $query = Copilot::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $copilotIds);

        $copilots = $query->get();

        if($totalCopilots = $copilots->count()) {

            foreach($copilots as $copilot) {
                $copilot->delete();
            }

            return ['deleted' => true, 'message' => $totalCopilots  .($totalCopilots  == 1 ? ' Copilot': ' Copilots') . ' deleted'];

        }else{
            return ['deleted' => false, 'message' => 'No Copilots deleted'];
        }
    }

    /**
     * Show Copilot.
     *
     * @param string $copilotId
     * @return CopilotResource
     */
    public function showCopilot(string $copilotId): CopilotResource
    {
        $copilot = Copilot::query()
            ->with($this->getRequestRelationships())
            ->findOrFail($copilotId);
        return $this->showResource($copilot);
    }

    /**
     * Update Copilot.
     *
     * @param string $copilotId
     * @param array $data
     * @return array
     */
    public function updateCopilot(string $copilotId, array $data): array
    {
        $copilot = Copilot::findOrFail($copilotId);

        if($copilot) {

            // Handle knowledge bases separately
            if (isset($data['knowledge_base_ids'])) {
                $knowledgeBaseIds = $data['knowledge_base_ids'];
                unset($data['knowledge_base_ids']);
                $copilot->knowledgeBases()->sync($knowledgeBaseIds);
            }

            // Handle users separately
            if (isset($data['user_ids'])) {
                $userIds = $data['user_ids'];
                unset($data['user_ids']);
                $copilot->users()->sync($userIds);
            }

            $copilot->update($data);
            return $this->showUpdatedResource($copilot);

        }else{

            return ['updated' => false, 'message' => 'This Copilot does not exist'];

        }
    }

    /**
     * Query the Copilot using RAG.
     *
     * @param Copilot $copilot
     * @param string $query
     * @param string|null $conversationThreadId
     * @return array
     */
    public function queryCopilot(Copilot $copilot, string $query, ?string $conversationThreadId = null): array
    {
        if (!$copilot->is_active) {
            return ['success' => false, 'message' => 'Copilot is not active'];
        }

        // Check if the current user is assigned to the Copilot
        $user = auth()->user();
        if (!$copilot->users->contains($user->id)) {
            return ['success' => false, 'message' => 'You do not have access to this Copilot'];
        }

        $isNewThread = false;

        // If conversationThreadId is not provided, create a new thread
        if (!$conversationThreadId) {
            $thread = ConversationThread::create([
                'id' => (string) Str::uuid(),
                'title' => 'New Conversation',
                'user_id' => $user->id,
                'copilot_id' => $copilot->id,
            ]);

            $conversationThreadId = $thread->id;
            $isNewThread = true;
        } else {
            // Verify the thread belongs to the user and copilot
            $thread = ConversationThread::where('id', $conversationThreadId)
                ->where('user_id', $user->id)
                ->where('copilot_id', $copilot->id)
                ->first();

            if (!$thread) {
                return ['success' => false, 'message' => 'Thread not found or access denied'];
            }
        }

        // Save the user's message
        ConversationMessage::create([
            'role' => 'user',
            'content' => $query,
            'timestamp' => now(),
            'id' => (string) Str::uuid(),
            'thread_id' => $conversationThreadId,
        ]);

        // Step 1: Classify the query to determine if RAG is needed
        $context = [];
        $knowledgeBases = $copilot->knowledgeBases;

        // First call to OpenAI to classify the query
        try {
            $intentResult = $this->openAiService->classifyQueryIntent($query);
            $needsSources = $intentResult['needsSources'] ?? false;
        } catch (\Exception $e) {
            // Fallback to skipping RAG if classification fails
            $needsSources = false;
        }

        // Step 2: Perform RAG retrieval only if needed
        if ($needsSources && !$knowledgeBases->isEmpty()) {
            $knowledgeBaseIds = $knowledgeBases->pluck('id')->toArray();

            // Generate query embedding
            $queryEmbedding = $this->openAiService->generateEmbedding($query);

            // Search Pinecone for relevant vectors with re-ranking
            $results = $this->pineconeService->queryVectors(
                $queryEmbedding,
                $query, // Pass the original query text for hybrid search
                $copilot->organization_id,
                50, // Initial top-k to retrieve more candidates for re-ranking
                ['knowledge_base_id' => ['$in' => $knowledgeBaseIds]],
                config('services.pinecone.reranker_id') // Specify the reranker ID from config
            );

            // Aggregate content from search results if any
            if (!empty($results)) {
                // Take the top 3 results after Pinecone re-ranking
                $results = array_slice($results, 0, 3);

                foreach ($results as $result) {
                    $metadata = $result['metadata'];
                    $context[] = [
                        'type' => $metadata['type'],
                        'content' => $metadata['content'],
                        'title' => $metadata['title'] ?? ($metadata['page_url'] ?? $metadata['website_url'] ?? 'Untitled'),
                    ];
                }
            }
        }

        // Step 3: Use OpenAI to generate the final response
        try {
            $response = $this->openAiService->promptWithContext($query, json_encode($context));
            $result = [
                'success' => true,
                'response' => $response,
                'context' => $context
            ];

            // Save the Copilot's response
            ConversationMessage::create([
                'id' => (string) Str::uuid(),
                'thread_id' => $conversationThreadId,
                'role' => 'assistant',
                'content' => $result['response'],
                'context' => $result['context'],
                'timestamp' => now(),
            ]);

            // Update thread title if it's the first message
            if ($thread->messages()->count() === 2) { // First user message + first assistant message
                $thread->title = substr($query, 0, 50) . (strlen($query) > 50 ? '...' : '');
                $thread->save();
            }

            // Include thread ID and title in the response
            $result['thread'] = [
                'id' => $thread->id,
                'title' => $thread->title
            ];

            return $result;
        } catch (\Exception $e) {
            $result = [
                'success' => false,
                'message' => 'Failed to generate response: ' . $e->getMessage(),
            ];

            // Save error message
            ConversationMessage::create([
                'id' => (string) Str::uuid(),
                'thread_id' => $conversationThreadId,
                'role' => 'error',
                'content' => $result['message'],
                'timestamp' => now(),
            ]);

            // Include thread ID and title even in case of error
            $result['thread'] = [
                'id' => $thread->id,
                'title' => $thread->title
            ];

            return $result;
        }
    }
}
