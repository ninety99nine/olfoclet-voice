<?php

namespace App\Http\Controllers;

use App\Models\Number;
use App\Services\NumberService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\NumberResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Number\ShowNumberRequest;
use App\Http\Requests\Number\ShowNumbersRequest;
use App\Http\Requests\Number\CreateNumberRequest;
use App\Http\Requests\Number\UpdateNumberRequest;
use App\Http\Requests\Number\DeleteNumberRequest;
use App\Http\Requests\Number\DeleteNumbersRequest;

class NumberController extends BaseController
{
    /**
     * @var NumberService
     */
    protected $service;

    /**
     * NumberController constructor.
     *
     * @param NumberService $service
     */
    public function __construct(NumberService $service)
    {
        $this->service = $service;
    }

    /**
     * Show numbers.
     *
     * @param ShowNumbersRequest $request
     * @return NumberResources|JsonResponse
     */
    public function showNumbers(ShowNumbersRequest $request): NumberResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showNumbers(request('organization_id')));
    }

    /**
     * Create number.
     *
     * @param CreateNumberRequest $request
     * @return JsonResponse
     */
    public function createNumber(CreateNumberRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createNumber(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple numbers.
     *
     * @param DeleteNumbersRequest $request
     * @return JsonResponse
     */
    public function deleteNumbers(DeleteNumbersRequest $request): JsonResponse
    {
        $numberIds = request()->input('number_ids', []);
        return $this->prepareOutput($this->service->deleteNumbers(request('organization_id'), $numberIds));
    }

    /**
     * Show single number.
     *
     * @param ShowNumberRequest $request
     * @param Number $number
     * @return JsonResponse
     */
    public function showNumber(ShowNumberRequest $request, Number $number): JsonResponse
    {
        return $this->prepareOutput($this->service->showNumber($number->id));
    }

    /**
     * Update number.
     *
     * @param UpdateNumberRequest $request
     * @param Number $number
     * @return JsonResponse
     */
    public function updateNumber(UpdateNumberRequest $request, Number $number): JsonResponse
    {
        return $this->prepareOutput($this->service->updateNumber($number->id, $request->validated()));
    }

    /**
     * Delete number.
     *
     * @param DeleteNumberRequest $request
     * @param Number $number
     * @return JsonResponse
     */
    public function deleteNumber(DeleteNumberRequest $request, Number $number): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteNumber($number->id));
    }
}
