<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use App\Services\ArticleService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\ArticleResources;
use App\Http\Requests\Article\ShowArticlesRequest;
use App\Http\Requests\Article\CreateArticleRequest;
use App\Http\Requests\Article\ShowArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Requests\Article\DeleteArticleRequest;
use App\Http\Requests\Article\DeleteArticlesRequest;

class ArticleController extends BaseController
{
    /**
     * @var ArticleService
     */
    protected $service;

    /**
     * ArticleController constructor.
     *
     * @param ArticleService $service
     */
    public function __construct(ArticleService $service)
    {
        $this->service = $service;
    }

    /**
     * Show articles.
     *
     * @param ShowArticlesRequest $request
     * @return ArticleResources|JsonResponse
     */
    public function showArticles(ShowArticlesRequest $request): ArticleResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showArticles(request('organization_id')));
    }

    /**
     * Create article.
     *
     * @param CreateArticleRequest $request
     * @return JsonResponse
     */
    public function createArticle(CreateArticleRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createArticle(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Show single article.
     *
     * @param ShowArticleRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function showArticle(ShowArticleRequest $request, Article $article): JsonResponse
    {
        return $this->prepareOutput($this->service->showArticle($article->id));
    }

    /**
     * Update article.
     *
     * @param UpdateArticleRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function updateArticle(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        return $this->prepareOutput($this->service->updateArticle($article->id, $request->validated()));
    }

    /**
     * Delete article.
     *
     * @param DeleteArticleRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function deleteArticle(DeleteArticleRequest $request, Article $article): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteArticle($article->id));
    }

    /**
     * Delete multiple articles.
     *
     * @param DeleteArticlesRequest $request
     * @return JsonResponse
     */
    public function deleteArticles(DeleteArticlesRequest $request): JsonResponse
    {
        $articleIds = request()->input('article_ids', []);
        return $this->prepareOutput($this->service->deleteArticles(request('organization_id'), $articleIds));
    }
}
