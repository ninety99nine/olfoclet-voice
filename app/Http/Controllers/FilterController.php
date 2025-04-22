<?php

namespace App\Http\Controllers;

use App\Services\FilterService;
use App\Enums\FilterResourceType;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Filtering\ShowFilterOptionsRequest;

class FilterController extends BaseController
{
    /**
     * @var FilterService
     */
    protected $service;

    /**
     * FilterController constructor.
     *
     * @param FilterService $service
     */
    public function __construct(FilterService $service)
    {
        $this->service = $service;
    }

    /**
     * Show filter options.
     *
     * @param ShowFilterOptionsRequest $request
     * @return JsonResponse
     */
    public function showFilterOptions(ShowFilterOptionsRequest $request): JsonResponse
    {
        $type = FilterResourceType::tryFrom($request->input('type'));
        return $this->prepareOutput($this->service->getFiltersByResourceType($type));
    }
}
