<?php

namespace App\Http\Controllers;

use App\Models\CallActivity;
use Illuminate\Http\JsonResponse;
use App\Services\CallActivityService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\CallActivityResources;
use App\Http\Requests\CallActivity\ShowCallActivityRequest;
use App\Http\Requests\CallActivity\ShowCallActivitiesRequest;

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
     * @param ShowCallActivitiesRequest $request
     * @return CallActivityResources|JsonResponse
     */
    public function showCallActivities(ShowCallActivitiesRequest $request): CallActivityResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showCallActivities(request('organization_id')));
    }

    /**
     * Show single call activity.
     *
     * @param ShowCallActivityRequest $request
     * @param CallActivity $callActivity
     * @return JsonResponse
     */
    public function showCallActivity(ShowCallActivityRequest $request, CallActivity $callActivity): JsonResponse
    {
        return $this->prepareOutput($this->service->showCallActivity($callActivity->id));
    }
}
