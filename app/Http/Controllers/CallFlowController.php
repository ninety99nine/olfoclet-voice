<?php

namespace App\Http\Controllers;

use App\Models\CallFlow;
use App\Services\CallFlowService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\BaseController;
use App\Http\Resources\CallFlowResources;
use App\Http\Requests\CallFlow\ShowCallFlowRequest;
use App\Http\Requests\CallFlow\ShowCallFlowsRequest;
use App\Http\Requests\CallFlow\CreateCallFlowRequest;
use App\Http\Requests\CallFlow\UpdateCallFlowRequest;
use App\Http\Requests\CallFlow\DeleteCallFlowRequest;
use App\Http\Requests\CallFlow\DeleteCallFlowsRequest;

class CallFlowController extends BaseController
{
    /**
     * @var CallFlowService
     */
    protected $service;

    /**
     * CallFlowController constructor.
     *
     * @param CallFlowService $service
     */
    public function __construct(CallFlowService $service)
    {
        $this->service = $service;
    }

    /**
     * Show call flows.
     *
     * @param ShowCallFlowsRequest $request
     * @return CallFlowResources|JsonResponse
     */
    public function showCallFlows(ShowCallFlowsRequest $request): CallFlowResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showCallFlows(request('organization_id')));
    }

    /**
     * Create call flow.
     *
     * @param CreateCallFlowRequest $request
     * @return JsonResponse
     */
    public function createCallFlow(CreateCallFlowRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createCallFlow(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple call flows.
     *
     * @param DeleteCallFlowsRequest $request
     * @return JsonResponse
     */
    public function deleteCallFlows(DeleteCallFlowsRequest $request): JsonResponse
    {
        $callFlowIds = request()->input('call_flow_ids', []);
        return $this->prepareOutput($this->service->deleteCallFlows(request('organization_id'), $callFlowIds));
    }

    /**
     * Show single call flow.
     *
     * @param ShowCallFlowRequest $request
     * @param CallFlow $callFlow
     * @return JsonResponse
     */
    public function showCallFlow(ShowCallFlowRequest $request, CallFlow $callFlow): JsonResponse
    {
        return $this->prepareOutput($this->service->showCallFlow($callFlow->id));
    }

    /**
     * Update call flow.
     *
     * @param UpdateCallFlowRequest $request
     * @param CallFlow $callFlow
     * @return JsonResponse
     */
    public function updateCallFlow(UpdateCallFlowRequest $request, CallFlow $callFlow): JsonResponse
    {
        return $this->prepareOutput($this->service->updateCallFlow($callFlow->id, $request->validated()));
    }

    /**
     * Delete call flow.
     *
     * @param DeleteCallFlowRequest $request
     * @param CallFlow $callFlow
     * @return JsonResponse
     */
    public function deleteCallFlow(DeleteCallFlowRequest $request, CallFlow $callFlow): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteCallFlow($callFlow->id));
    }
}
