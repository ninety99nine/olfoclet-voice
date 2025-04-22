<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Services\CallService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CallResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Call\ShowCallRequest;
use App\Http\Requests\Call\ShowCallsRequest;
use App\Http\Requests\Call\CreateCallRequest;
use App\Http\Requests\Call\UpdateCallRequest;
use App\Http\Requests\Call\DeleteCallRequest;
use App\Http\Requests\Call\DeleteCallsRequest;

class CallController extends BaseController
{
    /**
     * @var CallService
     */
    protected $service;

    /**
     * CallController constructor.
     *
     * @param CallService $service
     */
    public function __construct(CallService $service)
    {
        $this->service = $service;
    }

    /**
     * Show calls.
     *
     * @param ShowCallsRequest $request
     * @return CallResources|JsonResponse
     */
    public function showCalls(ShowCallsRequest $request): CallResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showCalls(request('organization_id')));
    }

    /**
     * Create call.
     *
     * @param CreateCallRequest $request
     * @return JsonResponse
     */
    public function createCall(CreateCallRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createCall(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple calls.
     *
     * @param DeleteCallsRequest $request
     * @return JsonResponse
     */
    public function deleteCalls(DeleteCallsRequest $request): JsonResponse
    {
        $callIds = request()->input('call_ids', []);
        return $this->prepareOutput($this->service->deleteCalls(request('organization_id'), $callIds));
    }

    /**
     * Show single call.
     *
     * @param ShowCallRequest $request
     * @param Call $call
     * @return JsonResponse
     */
    public function showCall(ShowCallRequest $request, Call $call): JsonResponse
    {
        return $this->prepareOutput($this->service->showCall($call->id));
    }

    /**
     * Update call.
     *
     * @param UpdateCallRequest $request
     * @param Call $call
     * @return JsonResponse
     */
    public function updateCall(UpdateCallRequest $request, Call $call): JsonResponse
    {
        return $this->prepareOutput($this->service->updateCall($call->id, $request->validated()));
    }

    /**
     * Delete call.
     *
     * @param DeleteCallRequest $request
     * @param Call $call
     * @return JsonResponse
     */
    public function deleteCall(DeleteCallRequest $request, Call $call): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteCall($call->id));
    }
}
