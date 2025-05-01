<?php

namespace App\Http\Controllers;

use App\Models\Copilot;
use App\Services\CopilotService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\BaseController;
use App\Http\Resources\CopilotResources;
use App\Http\Requests\Copilot\ShowCopilotRequest;
use App\Http\Requests\Copilot\ShowCopilotsRequest;
use App\Http\Requests\Copilot\QueryCopilotRequest;
use App\Http\Requests\Copilot\CreateCopilotRequest;
use App\Http\Requests\Copilot\UpdateCopilotRequest;
use App\Http\Requests\Copilot\DeleteCopilotRequest;
use App\Http\Requests\Copilot\DeleteCopilotsRequest;

class CopilotController extends BaseController
{
    /**
     * @var CopilotService
     */
    protected $service;

    /**
     * CopilotController constructor.
     *
     * @param CopilotService $service
     */
    public function __construct(CopilotService $service)
    {
        $this->service = $service;
    }

    /**
     * Show copilots.
     *
     * @param ShowCopilotsRequest $request
     * @return CopilotResources|JsonResponse
     */
    public function showCopilots(ShowCopilotsRequest $request): CopilotResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showCopilots(request('organization_id')));
    }

    /**
     * Create copilot.
     *
     * @param CreateCopilotRequest $request
     * @return JsonResponse
     */
    public function createCopilot(CreateCopilotRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createCopilot(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Show single copilot.
     *
     * @param ShowCopilotRequest $request
     * @param Copilot $copilot
     * @return JsonResponse
     */
    public function showCopilot(ShowCopilotRequest $request, Copilot $copilot): JsonResponse
    {
        return $this->prepareOutput($this->service->showCopilot($copilot->id));
    }

    /**
     * Update copilot.
     *
     * @param UpdateCopilotRequest $request
     * @param Copilot $copilot
     * @return JsonResponse
     */
    public function updateCopilot(UpdateCopilotRequest $request, Copilot $copilot): JsonResponse
    {
        return $this->prepareOutput($this->service->updateCopilot($copilot->id, $request->validated()));
    }

    /**
     * Delete copilot.
     *
     * @param DeleteCopilotRequest $request
     * @param Copilot $copilot
     * @return JsonResponse
     */
    public function deleteCopilot(DeleteCopilotRequest $request, Copilot $copilot): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteCopilot($copilot->id));
    }

    /**
     * Delete multiple copilots.
     *
     * @param DeleteCopilotsRequest $request
     * @return JsonResponse
     */
    public function deleteCopilots(DeleteCopilotsRequest $request): JsonResponse
    {
        $copilotIds = request()->input('copilot_ids', []);
        return $this->prepareOutput($this->service->deleteCopilots(request('organization_id'), $copilotIds));
    }

    /**
     * Query the copilot.
     *
     * @param QueryCopilotRequest $request
     * @param Copilot $copilot
     * @return JsonResponse
     */
    public function queryCopilot(QueryCopilotRequest $request, Copilot $copilot): JsonResponse
    {
        return $this->prepareOutput($this->service->queryCopilot($copilot, $request->input('query'), $request->input('conversation_thread_id')));
    }
}
