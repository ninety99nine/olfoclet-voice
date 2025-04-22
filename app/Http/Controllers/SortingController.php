<?php

namespace App\Http\Controllers;

use App\Enums\SortResourceType;
use App\Services\SortingService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Sorting\ShowSortOptionsRequest;

class SortingController extends BaseController
{
    /**
     * @var SortingService
     */
    protected $service;

    /**
     * SortingController constructor.
     *
     * @param SortingService $service
     */
    public function __construct(SortingService $service)
    {
        $this->service = $service;
    }

    /**
     * Show sort options.
     *
     * @param ShowSortOptionsRequest $request
     * @return JsonResponse
     */
    public function showSortOptions(ShowSortOptionsRequest $request): JsonResponse
    {
        $type = SortResourceType::tryFrom($request->input('type'));
        return $this->prepareOutput($this->service->getSortingOptionsByResourceType($type));
    }
}
