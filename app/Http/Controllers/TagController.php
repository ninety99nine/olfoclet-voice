<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\TagResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Tag\ShowTagRequest;
use App\Http\Requests\Tag\ShowTagsRequest;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Http\Requests\Tag\DeleteTagRequest;
use App\Http\Requests\Tag\DeleteTagsRequest;

class TagController extends BaseController
{
    /**
     * @var TagService
     */
    protected $service;

    /**
     * TagController constructor.
     *
     * @param TagService $service
     */
    public function __construct(TagService $service)
    {
        $this->service = $service;
    }

    /**
     * Show tags.
     *
     * @param ShowTagsRequest $request
     * @return TagResources|JsonResponse
     */
    public function showTags(ShowTagsRequest $request): TagResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showTags(request('organization_id')));
    }

    /**
     * Create tag.
     *
     * @param CreateTagRequest $request
     * @return JsonResponse
     */
    public function createTag(CreateTagRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createTag(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple tags.
     *
     * @param DeleteTagsRequest $request
     * @return JsonResponse
     */
    public function deleteTags(DeleteTagsRequest $request): JsonResponse
    {
        $tagIds = request()->input('tag_ids', []);
        return $this->prepareOutput($this->service->deleteTags(request('organization_id'), $tagIds));
    }

    /**
     * Show single tag.
     *
     * @param ShowTagRequest $request
     * @param Tag $tag
     * @return JsonResponse
     */
    public function showTag(ShowTagRequest $request, Tag $tag): JsonResponse
    {
        return $this->prepareOutput($this->service->showTag($tag->id));
    }

    /**
     * Update tag.
     *
     * @param UpdateTagRequest $request
     * @param Tag $tag
     * @return JsonResponse
     */
    public function updateTag(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        return $this->prepareOutput($this->service->updateTag($tag->id, $request->validated()));
    }

    /**
     * Delete tag.
     *
     * @param DeleteTagRequest $request
     * @param Tag $tag
     * @return JsonResponse
     */
    public function deleteTag(DeleteTagRequest $request, Tag $tag): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteTag($tag->id));
    }
}
