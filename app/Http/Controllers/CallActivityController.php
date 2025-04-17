<?php

namespace App\Http\Controllers;

use App\Models\CallActivity;
use Illuminate\Http\JsonResponse;
use App\Services\CallActivityService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\CallActivityResources;
use App\Http\Requests\CallActivity\CreateCallActivityRequest;
use App\Http\Requests\CallActivity\UpdateCallActivityRequest;

class CallActivityController extends BaseController
{
    /**
     * @var CallActivityService
     */
    protected $service;

    /**
     * CallActivityController constructor.
     *
     * @param CallActivityService $service
     */
    public function __construct(CallActivityService $service)
    {
        $this->service = $service;
    }

    /**
     * Show call activities.
     *
     * @return CallActivityResources|JsonResponse
     */
    public function showCallActivities(): CallActivityResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showCallActivities(request('organization_id')));
    }

    /**
     * Create call activity.
     *
     * @param CreateCallActivityRequest $request
     * @return JsonResponse
     */
    public function createCallActivity(CreateCallActivityRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createCallActivity(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Show single call activity.
     *
     * @param CallActivity $callActivity
     * @return JsonResponse
     */
    public function showCallActivity(CallActivity $callActivity): JsonResponse
    {
        return $this->prepareOutput($this->service->showCallActivity($callActivity->id));
    }

    /**
     * Update call activity.
     *
     * @param UpdateCallActivityRequest $request
     * @param CallActivity $callActivity
     * @return JsonResponse
     */
    public function updateCallActivity(UpdateCallActivityRequest $request, CallActivity $callActivity): JsonResponse
    {
        return $this->prepareOutput($this->service->updateCallActivity($callActivity->id, $request->validated()));
    }

    /**
     * Delete call activity.
     *
     * @param CallActivity $callActivity
     * @return JsonResponse
     */
    public function deleteCallActivity(CallActivity $callActivity): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteCallActivity($callActivity->id));
    }

    /**
     * Delete multiple call activities.
     *
     * @return JsonResponse
     */
    public function deleteCallActivities(): JsonResponse
    {
        $callActivityIds = request()->input('call_activity_ids', []);
        return $this->prepareOutput($this->service->deleteCallActivities(request('organization_id'), $callActivityIds));
    }
}
