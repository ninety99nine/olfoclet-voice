<?php

namespace App\Http\Controllers;

use App\Models\HelpCenterCollection;
use App\Services\HelpCenterCollectionService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\HelpCenterCollectionResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\HelpCenterCollection\ShowHelpCenterCollectionRequest;
use App\Http\Requests\HelpCenterCollection\ShowHelpCenterCollectionsRequest;
use App\Http\Requests\HelpCenterCollection\CreateHelpCenterCollectionRequest;
use App\Http\Requests\HelpCenterCollection\UpdateHelpCenterCollectionRequest;
use App\Http\Requests\HelpCenterCollection\DeleteHelpCenterCollectionRequest;
use App\Http\Requests\HelpCenterCollection\DeleteHelpCenterCollectionsRequest;

class HelpCenterCollectionController extends BaseController
{
    /**
     * @var HelpCenterCollectionService
     */
    protected $service;

    /**
     * HelpCenterCollectionController constructor.
     *
     * @param HelpCenterCollectionService $service
     */
    public function __construct(HelpCenterCollectionService $service)
    {
        $this->service = $service;
    }

    /**
     * Show help center collections.
     *
     * @param ShowHelpCenterCollectionsRequest $request
     * @return HelpCenterCollectionResources|JsonResponse
     */
    public function showHelpCenterCollections(ShowHelpCenterCollectionsRequest $request): HelpCenterCollectionResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showHelpCenterCollections(request('organization_id')));
    }

    /**
     * Create help center collection.
     *
     * @param CreateHelpCenterCollectionRequest $request
     * @return JsonResponse
     */
    public function createHelpCenterCollection(CreateHelpCenterCollectionRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createHelpCenterCollection(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple help center collections.
     *
     * @param DeleteHelpCenterCollectionsRequest $request
     * @return JsonResponse
     */
    public function deleteHelpCenterCollections(DeleteHelpCenterCollectionsRequest $request): JsonResponse
    {
        $helpCenterCollectionIds = request()->input('help_center_collection_ids', []);
        return $this->prepareOutput($this->service->deleteHelpCenterCollections(request('organization_id'), $helpCenterCollectionIds));
    }

    /**
     * Show single help center collection.
     *
     * @param ShowHelpCenterCollectionRequest $request
     * @param HelpCenterCollection $helpCenterCollection
     * @return JsonResponse
     */
    public function showHelpCenterCollection(ShowHelpCenterCollectionRequest $request, HelpCenterCollection $helpCenterCollection): JsonResponse
    {
        return $this->prepareOutput($this->service->showHelpCenterCollection($helpCenterCollection->id));
    }

    /**
     * Update help center collection.
     *
     * @param UpdateHelpCenterCollectionRequest $request
     * @param HelpCenterCollection $helpCenterCollection
     * @return JsonResponse
     */
    public function updateHelpCenterCollection(UpdateHelpCenterCollectionRequest $request, HelpCenterCollection $helpCenterCollection): JsonResponse
    {
        return $this->prepareOutput($this->service->updateHelpCenterCollection($helpCenterCollection->id, $request->validated()));
    }

    /**
     * Delete help center collection.
     *
     * @param DeleteHelpCenterCollectionRequest $request
     * @param HelpCenterCollection $helpCenterCollection
     * @return JsonResponse
     */
    public function deleteHelpCenterCollection(DeleteHelpCenterCollectionRequest $request, HelpCenterCollection $helpCenterCollection): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteHelpCenterCollection($helpCenterCollection->id));
    }
}
