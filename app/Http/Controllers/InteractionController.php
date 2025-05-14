<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use Illuminate\Http\JsonResponse;
use App\Services\InteractionService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\InteractionResources;
use App\Http\Requests\Interaction\ShowInteractionRequest;
use App\Http\Requests\Interaction\ShowInteractionsRequest;
use App\Http\Requests\Interaction\CreateInteractionRequest;
use App\Http\Requests\Interaction\UpdateInteractionRequest;
use App\Http\Requests\Interaction\DeleteInteractionRequest;
use App\Http\Requests\Interaction\DeleteInteractionsRequest;

class InteractionController extends BaseController
{
    /**
     * @var InteractionService
     */
    protected $service;

    /**
     * InteractionController constructor.
     *
     * @param InteractionService $service
     */
    public function __construct(InteractionService $service)
    {
        $this->service = $service;
    }

    /**
     * Show interactions.
     *
     * @param ShowInteractionsRequest $request
     * @return InteractionResources|JsonResponse
     */
    public function showInteractions(ShowInteractionsRequest $request): InteractionResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showInteractions(request('organization_id')));
    }

    /**
     * Create interaction.
     *
     * @param CreateInteractionRequest $request
     * @return JsonResponse
     */
    public function createInteraction(CreateInteractionRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createInteraction(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple interactions.
     *
     * @param DeleteInteractionsRequest $request
     * @return JsonResponse
     */
    public function deleteInteractions(DeleteInteractionsRequest $request): JsonResponse
    {
        $interactionIds = request()->input('interaction_ids', []);
        return $this->prepareOutput($this->service->deleteInteractions(request('organization_id'), $interactionIds));
    }

    /**
     * Show single interaction.
     *
     * @param ShowInteractionRequest $request
     * @param Interaction $interaction
     * @return JsonResponse
     */
    public function showInteraction(ShowInteractionRequest $request, Interaction $interaction): JsonResponse
    {
        return $this->prepareOutput($this->service->showInteraction($interaction->id));
    }

    /**
     * Update interaction.
     *
     * @param UpdateInteractionRequest $request
     * @param Interaction $interaction
     * @return JsonResponse
     */
    public function updateInteraction(UpdateInteractionRequest $request, Interaction $interaction): JsonResponse
    {
        return $this->prepareOutput($this->service->updateInteraction($interaction->id, $request->validated()));
    }

    /**
     * Delete interaction.
     *
     * @param DeleteInteractionRequest $request
     * @param Interaction $interaction
     * @return JsonResponse
     */
    public function deleteInteraction(DeleteInteractionRequest $request, Interaction $interaction): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteInteraction($interaction->id));
    }
}
