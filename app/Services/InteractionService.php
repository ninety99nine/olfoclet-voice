<?php

namespace App\Services;

use App\Models\Interaction;
use App\Http\Resources\InteractionResource;
use App\Http\Resources\InteractionResources;

class InteractionService extends BaseService
{
    /**
     * Show interactions.
     *
     * @param string|null $organizationId
     * @return InteractionResources|array
     */
    public function showInteractions(?string $organizationId = null): InteractionResources|array
    {
        $query = Interaction::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create interaction.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createInteraction(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;
        $interaction = Interaction::create($data);

        return $this->showCreatedResource($interaction);
    }

    /**
     * Show interaction.
     *
     * @param string $interactionId
     * @return InteractionResource
     */
    public function showInteraction(string $interactionId): InteractionResource
    {
        $interaction = Interaction::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($interactionId);
        return $this->showResource($interaction);
    }

    /**
     * Update interaction.
     *
     * @param string $interactionId
     * @param array $data
     * @return array
     */
    public function updateInteraction(string $interactionId, array $data): array
    {
        $interaction = Interaction::findOrFail($interactionId);
        $interaction->update($data);

        return $this->showUpdatedResource($interaction);
    }

    /**
     * Delete interaction.
     *
     * @param string $interactionId
     * @return array
     */
    public function deleteInteraction(string $interactionId): array
    {
        $interaction = Interaction::findOrFail($interactionId);

        $deleted = $interaction->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Interaction deleted'];
        }

        return ['deleted' => false, 'message' => 'Interaction delete unsuccessful'];
    }

    /**
     * Delete interactions.
     *
     * @param string|null $organizationId
     * @param array $interactionIds
     * @return array
     */
    public function deleteInteractions(?string $organizationId, array $interactionIds): array
    {
        $query = Interaction::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $interactionIds);

        $interactions = $query->get();

        if ($totalInteractions = $interactions->count()) {
            $interactions->each->delete();
            return ['deleted' => true, 'message' => "$totalInteractions interaction(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No interactions deleted'];
    }
}
