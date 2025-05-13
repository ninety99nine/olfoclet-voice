<?php

namespace App\Http\Controllers;

use App\Models\ContentSource;
use Illuminate\Http\JsonResponse;
use App\Services\ContentSourceService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\ContentSourceResources;
use App\Http\Requests\ContentSource\ShowContentSourceRequest;
use App\Http\Requests\ContentSource\ShowContentSourcesRequest;
use App\Http\Requests\ContentSource\CreateContentSourceRequest;
use App\Http\Requests\ContentSource\UpdateContentSourceRequest;
use App\Http\Requests\ContentSource\DeleteContentSourceRequest;
use App\Http\Requests\ContentSource\DeleteContentSourcesRequest;

class ContentSourceController extends BaseController
{
    /**
     * @var ContentSourceService
     */
    protected $service;

    /**
     * ContentSourceController constructor.
     *
     * @param ContentSourceService $service
     */
    public function __construct(ContentSourceService $service)
    {
        $this->service = $service;
    }

    /**
     * Show content sources.
     *
     * @param ShowContentSourcesRequest $request
     * @return ContentSourceResources|JsonResponse
     */
    public function showContentSources(ShowContentSourcesRequest $request): ContentSourceResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showContentSources(request('knowledge_base_id')));
    }

    /**
     * Create content source.
     *
     * @param CreateContentSourceRequest $request
     * @return JsonResponse
     */
    public function createContentSource(CreateContentSourceRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createContentSource(request('knowledge_base_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple content sources.
     *
     * @param DeleteContentSourcesRequest $request
     * @return JsonResponse
     */
    public function deleteContentSources(DeleteContentSourcesRequest $request): JsonResponse
    {
        $contentSourceIds = request()->input('content_source_ids', []);
        return $this->prepareOutput($this->service->deleteContentSources(request('knowledge_base_id'), $contentSourceIds));
    }

    /**
     * Show single content source.
     *
     * @param ShowContentSourceRequest $request
     * @param ContentSource $contentSource
     * @return JsonResponse
     */
    public function showContentSource(ShowContentSourceRequest $request, ContentSource $contentSource): JsonResponse
    {
        return $this->prepareOutput($this->service->showContentSource($contentSource->id));
    }

    /**
     * Update content source.
     *
     * @param UpdateContentSourceRequest $request
     * @param ContentSource $contentSource
     * @return JsonResponse
     */
    public function updateContentSource(UpdateContentSourceRequest $request, ContentSource $contentSource): JsonResponse
    {
        return $this->prepareOutput($this->service->updateContentSource($contentSource->id, $request->validated()));
    }

    /**
     * Delete content source.
     *
     * @param DeleteContentSourceRequest $request
     * @param ContentSource $contentSource
     * @return JsonResponse
     */
    public function deleteContentSource(DeleteContentSourceRequest $request, ContentSource $contentSource): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteContentSource($contentSource->id));
    }
}
