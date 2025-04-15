<?php

namespace App\Http\Controllers;

use App\Services\FilterService;
use App\Enums\FilterResourceType;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Filter\ShowFiltersRequest;

class FilterController extends BaseController
{
    /**
     * @var FilterService
     */
    protected $service;

    /**
     * OrganizationController constructor.
     *
     * @param FilterService $service
     */
    public function __construct(FilterService $service)
    {
        $this->service = $service;
    }

    /**
     * Show filters.
     *
     * @param ShowFiltersRequest $request
     * @return JsonResponse
     */
    public function showFilters(ShowFiltersRequest $request): JsonResponse
    {
        $type = FilterResourceType::tryFrom($request->input('type'));
        return $this->prepareOutput($this->service->getFiltersByResourceType($type));
    }
}
