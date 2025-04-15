<?php

namespace App\Http\Controllers;

use App\Enums\SortResourceType;
use App\Services\SortingService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Sorting\ShowSortingRequest;

class SortingController extends BaseController
{
    /**
     * @var SortingService
     */
    protected $service;

    /**
     * OrganizationController constructor.
     *
     * @param SortingService $service
     */
    public function __construct(SortingService $service)
    {
        $this->service = $service;
    }

    /**
     * Show sorting.
     *
     * @param ShowSortingRequest $request
     * @return JsonResponse
     */
    public function showSorting(ShowSortingRequest $request): JsonResponse
    {
        $type = SortResourceType::tryFrom($request->input('type'));
        return $this->prepareOutput($this->service->getSortingOptionsByResourceType($type));
    }
}
