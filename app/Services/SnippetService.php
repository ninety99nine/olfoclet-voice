<?php

namespace App\Services;

use App\Models\Snippet;
use App\Http\Resources\SnippetResource;
use App\Http\Resources\SnippetResources;

class SnippetService extends BaseService
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
     * SnippetService constructor.
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
     * Show snippets.
     *
     * @param string|null $organizationId
     * @return SnippetResources|array
     */
    public function showSnippets(?string $organizationId = null): SnippetResources|array
    {
        $query = Snippet::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create snippet.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createSnippet(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;
        $snippet = Snippet::create($data);

        // If the snippet is AI searchable, generate embedding and upsert to Pinecone
        if ($snippet->ai_searchable) {
            $embedding = $this->openAiService->generateEmbedding($snippet->content);
            $metadata = [
                'knowledge_base_id' => $snippet->knowledge_base_id,
                'type' => 'snippet',
                'content' => $snippet->content,
                'title' => $snippet->title,
            ];
            $this->pineconeService->upsertVector($snippet->id, $embedding, $organizationId, $metadata);
        }

        return $this->showCreatedResource($snippet);
    }

    /**
     * Show snippet.
     *
     * @param string $snippetId
     * @return SnippetResource
     */
    public function showSnippet(string $snippetId): SnippetResource
    {
        $snippet = Snippet::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($snippetId);
        return $this->showResource($snippet);
    }

    /**
     * Update snippet.
     *
     * @param string $snippetId
     * @param array $data
     * @return array
     */
    public function updateSnippet(string $snippetId, array $data): array
    {
        $snippet = Snippet::findOrFail($snippetId);
        $oldAiSearchable = $snippet->ai_searchable;
        $snippet->update($data);

        // Handle Pinecone updates for RAG
        if (isset($data['ai_searchable']) || isset($data['content'])) {
            if ($snippet->ai_searchable) {
                $embedding = $this->openAiService->generateEmbedding($snippet->content);
                $metadata = [
                    'knowledge_base_id' => $snippet->knowledge_base_id,
                    'type' => 'snippet',
                    'content' => $snippet->content,
                    'title' => $snippet->title,
                ];
                $this->pineconeService->upsertVector($snippet->id, $embedding, $snippet->organization_id, $metadata);
            } elseif ($oldAiSearchable) {
                $this->pineconeService->deleteVector($snippet->id, $snippet->organization_id);
            }
        }

        return $this->showUpdatedResource($snippet);
    }

    /**
     * Delete snippet.
     *
     * @param string $snippetId
     * @return array
     */
    public function deleteSnippet(string $snippetId): array
    {
        $snippet = Snippet::findOrFail($snippetId);

        // Delete from Pinecone if AI searchable
        if ($snippet->ai_searchable) {
            $this->pineconeService->deleteVector($snippet->id, $snippet->organization_id);
        }

        $deleted = $snippet->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Snippet deleted'];
        }

        return ['deleted' => false, 'message' => 'Snippet delete unsuccessful'];
    }

    /**
     * Delete snippets.
     *
     * @param string|null $organizationId
     * @param array $snippetIds
     * @return array
     */
    public function deleteSnippets(?string $organizationId, array $snippetIds): array
    {
        $query = Snippet::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $snippetIds);

        $snippets = $query->get();

        if ($totalSnippets = $snippets->count()) {
            // Delete from Pinecone for AI searchable snippets
            $snippets->where('ai_searchable', true)->each(function ($snippet) {
                $this->pineconeService->deleteVector($snippet->id, $snippet->organization_id);
            });

            $snippets->each->delete();
            return ['deleted' => true, 'message' => "$totalSnippets snippet(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No snippets deleted'];
    }
}
