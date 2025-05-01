<?php

namespace App\Services;

use App\Models\Article;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleResources;

class ArticleService extends BaseService
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
     * ArticleService constructor.
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
     * Show articles.
     *
     * @param string|null $organizationId
     * @return ArticleResources|array
     */
    public function showArticles(?string $organizationId = null): ArticleResources|array
    {
        $query = Article::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create article.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createArticle(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;
        $article = Article::create($data);

        // If the article is AI searchable, generate embedding and upsert to Pinecone
        if ($article->ai_searchable) {
            $embedding = $this->openAiService->generateEmbedding($article->content);
            $metadata = [
                'knowledge_base_id' => $article->knowledge_base_id,
                'content' => $article->content,
                'title' => $article->title,
                'type' => 'article'
            ];
            $this->pineconeService->upsertVector($article->id, $embedding, $organizationId, $metadata);
        }

        return $this->showCreatedResource($article);
    }

    /**
     * Show article.
     *
     * @param string $articleId
     * @return ArticleResource
     */
    public function showArticle(string $articleId): ArticleResource
    {
        $article = Article::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($articleId);
        return $this->showResource($article);
    }

    /**
     * Update article.
     *
     * @param string $articleId
     * @param array $data
     * @return array
     */
    public function updateArticle(string $articleId, array $data): array
    {
        $article = Article::findOrFail($articleId);
        $oldAiSearchable = $article->ai_searchable;
        $article->update($data);

        // Handle Pinecone updates for RAG
        if (isset($data['ai_searchable']) || isset($data['content'])) {
            if ($article->ai_searchable) {
                $embedding = $this->openAiService->generateEmbedding($article->content);
                $metadata = [
                    'knowledge_base_id' => $article->knowledge_base_id,
                    'type' => 'article',
                    'content' => $article->content,
                    'title' => $article->title,
                ];
                $this->pineconeService->upsertVector($article->id, $embedding, $article->organization_id, $metadata);
            } elseif ($oldAiSearchable) {
                $this->pineconeService->deleteVector($article->id, $article->organization_id);
            }
        }

        return $this->showUpdatedResource($article);
    }

    /**
     * Delete article.
     *
     * @param string $articleId
     * @return array
     */
    public function deleteArticle(string $articleId): array
    {
        $article = Article::findOrFail($articleId);

        // Delete from Pinecone if AI searchable
        if ($article->ai_searchable) {
            $this->pineconeService->deleteVector($article->id, $article->organization_id);
        }

        $deleted = $article->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Article deleted'];
        }

        return ['deleted' => false, 'message' => 'Article delete unsuccessful'];
    }

    /**
     * Delete articles.
     *
     * @param string|null $organizationId
     * @param array $articleIds
     * @return array
     */
    public function deleteArticles(?string $organizationId, array $articleIds): array
    {
        $query = Article::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $articleIds);

        $articles = $query->get();

        if ($totalArticles = $articles->count()) {
            // Delete from Pinecone for AI searchable articles
            $articles->where('ai_searchable', true)->each(function ($article) {
                $this->pineconeService->deleteVector($article->id, $article->organization_id);
            });

            $articles->each->delete();
            return ['deleted' => true, 'message' => "$totalArticles article(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No articles deleted'];
    }
}
