<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use App\Services\ContentItemService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ContentItemResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\ContentItem\ShowContentItemRequest;
use App\Http\Requests\ContentItem\ShowContentItemsRequest;
use App\Http\Requests\ContentItem\CreateContentItemRequest;
use App\Http\Requests\ContentItem\UpdateContentItemRequest;
use App\Http\Requests\ContentItem\DeleteContentItemRequest;
use App\Http\Requests\ContentItem\DeleteContentItemsRequest;

class ContentItemController extends BaseController
{
    /**
     * @var ContentItemService
     */
    protected $service;

    /**
     * ContentItemController constructor.
     *
     * @param ContentItemService $service
     */
    public function __construct(ContentItemService $service)
    {
        $this->service = $service;
    }

    /**
     * Show content items.
     *
     * @param ShowContentItemsRequest $request
     * @return ContentItemResources|JsonResponse
     */
    public function showContentItems(ShowContentItemsRequest $request): ContentItemResources|JsonResponse
    {
        if ($request->input('type') === 'folder') {
            return $this->prepareOutput($this->service->getFolderTree($request->input('knowledge_base_id')));
        }else{
            return $this->prepareOutput($this->service->showContentItems($request->input('knowledge_base_id')));
        }
    }

    /**
     * Create content item.
     *
     * @param CreateContentItemRequest $request
     * @return JsonResponse
     */
    public function createContentItem(CreateContentItemRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createContentItem($request->input('knowledge_base_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple content items.
     *
     * @param DeleteContentItemsRequest $request
     * @return JsonResponse
     */
    public function deleteContentItems(DeleteContentItemsRequest $request): JsonResponse
    {
        $contentItemIds = $request->input('content_item_ids', []);
        return $this->prepareOutput($this->service->deleteContentItems($request->input('knowledge_base_id'), $contentItemIds));
    }

    /**
     * Show single content item.
     *
     * @param ShowContentItemRequest $request
     * @param ContentItem $contentItem
     * @return JsonResponse
     */
    public function showContentItem(ShowContentItemRequest $request, ContentItem $contentItem): JsonResponse
    {
        return $this->prepareOutput($this->service->showContentItem($contentItem->id));
    }

    /**
     * Update content item.
     *
     * @param UpdateContentItemRequest $request
     * @param ContentItem $contentItem
     * @return JsonResponse
     */
    public function updateContentItem(UpdateContentItemRequest $request, ContentItem $contentItem): JsonResponse
    {
        return $this->prepareOutput($this->service->updateContentItem($contentItem->id, $request->validated()));
    }

    /**
     * Delete content item.
     *
     * @param DeleteContentItemRequest $request
     * @param ContentItem $contentItem
     * @return JsonResponse
     */
    public function deleteContentItem(DeleteContentItemRequest $request, ContentItem $contentItem): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteContentItem($contentItem->id));
    }
}
