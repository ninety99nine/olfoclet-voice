<?php

namespace App\Http\Controllers;

use App\Models\Snippet;
use Illuminate\Http\JsonResponse;
use App\Services\SnippetService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\SnippetResources;
use App\Http\Requests\Snippet\ShowSnippetsRequest;
use App\Http\Requests\Snippet\CreateSnippetRequest;
use App\Http\Requests\Snippet\ShowSnippetRequest;
use App\Http\Requests\Snippet\UpdateSnippetRequest;
use App\Http\Requests\Snippet\DeleteSnippetRequest;
use App\Http\Requests\Snippet\DeleteSnippetsRequest;

class SnippetController extends BaseController
{
    /**
     * @var SnippetService
     */
    protected $service;

    /**
     * SnippetController constructor.
     *
     * @param SnippetService $service
     */
    public function __construct(SnippetService $service)
    {
        $this->service = $service;
    }

    /**
     * Show snippets.
     *
     * @param ShowSnippetsRequest $request
     * @return SnippetResources|JsonResponse
     */
    public function showSnippets(ShowSnippetsRequest $request): SnippetResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showSnippets(request('organization_id')));
    }

    /**
     * Create snippet.
     *
     * @param CreateSnippetRequest $request
     * @return JsonResponse
     */
    public function createSnippet(CreateSnippetRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createSnippet(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Show single snippet.
     *
     * @param ShowSnippetRequest $request
     * @param Snippet $snippet
     * @return JsonResponse
     */
    public function showSnippet(ShowSnippetRequest $request, Snippet $snippet): JsonResponse
    {
        return $this->prepareOutput($this->service->showSnippet($snippet->id));
    }

    /**
     * Update snippet.
     *
     * @param UpdateSnippetRequest $request
     * @param Snippet $snippet
     * @return JsonResponse
     */
    public function updateSnippet(UpdateSnippetRequest $request, Snippet $snippet): JsonResponse
    {
        return $this->prepareOutput($this->service->updateSnippet($snippet->id, $request->validated()));
    }

    /**
     * Delete snippet.
     *
     * @param DeleteSnippetRequest $request
     * @param Snippet $snippet
     * @return JsonResponse
     */
    public function deleteSnippet(DeleteSnippetRequest $request, Snippet $snippet): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteSnippet($snippet->id));
    }

    /**
     * Delete multiple snippets.
     *
     * @param DeleteSnippetsRequest $request
     * @return JsonResponse
     */
    public function deleteSnippets(DeleteSnippetsRequest $request): JsonResponse
    {
        $snippetIds = request()->input('snippet_ids', []);
        return $this->prepareOutput($this->service->deleteSnippets(request('organization_id'), $snippetIds));
    }
}
