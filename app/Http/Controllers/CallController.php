<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Services\CallService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CallResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Call\CreateCallRequest;
use App\Http\Requests\Call\UpdateCallRequest;

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
     * @return CallResources|JsonResponse
     */
    public function showCalls(): CallResources|JsonResponse
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
     * Show single call.
     *
     * @param Call $call
     * @return JsonResponse
     */
    public function showCall(Call $call): JsonResponse
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
     * @param Call $call
     * @return JsonResponse
     */
    public function deleteCall(Call $call): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteCall($call->id));
    }

    /**
     * Delete multiple calls.
     *
     * @return JsonResponse
     */
    public function deleteCalls(): JsonResponse
    {
        $callIds = request()->input('call_ids', []);
        return $this->prepareOutput($this->service->deleteCalls(request('organization_id'), $callIds));
    }
}
