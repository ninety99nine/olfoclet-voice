<?php

namespace App\Services;

use App\Models\CallFlow;
use App\Http\Resources\CallFlowResource;
use App\Http\Resources\CallFlowResources;

class CallFlowService extends BaseService
{
    /**
     * Show call flows.
     *
     * @param string|null $organizationId
     * @return CallFlowResources|array
     */
    public function showCallFlows(?string $organizationId = null): CallFlowResources|array
    {
        $query = CallFlow::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create call flow.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createCallFlow(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;
        $callFlow = CallFlow::create($data);
        return $this->showCreatedResource($callFlow);
    }

    /**
     * Show call flow.
     *
     * @param string $callFlowId
     * @return CallFlowResource
     */
    public function showCallFlow(string $callFlowId): CallFlowResource
    {
        $callFlow = CallFlow::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($callFlowId);
        return $this->showResource($callFlow);
    }

    /**
     * Update call flow.
     *
     * @param string $callFlowId
     * @param array $data
     * @return array
     */
    public function updateCallFlow(string $callFlowId, array $data): array
    {
        $callFlow = CallFlow::findOrFail($callFlowId);
        $callFlow->update($data);
        return $this->showUpdatedResource($callFlow);
    }

    /**
     * Delete call flow.
     *
     * @param string $callFlowId
     * @return array
     */
    public function deleteCallFlow(string $callFlowId): array
    {
        $callFlow = CallFlow::findOrFail($callFlowId);

        $deleted = $callFlow->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Call flow deleted'];
        }

        return ['deleted' => false, 'message' => 'Call flow delete unsuccessful'];
    }

    /**
     * Delete call flows.
     *
     * @param string|null $organizationId
     * @param array $callFlowIds
     * @return array
     */
    public function deleteCallFlows(?string $organizationId, array $callFlowIds): array
    {
        $query = CallFlow::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $callFlowIds);

        $callFlows = $query->get();

        if ($totalCallFlows = $callFlows->count()) {
            $callFlows->each->delete();
            return ['deleted' => true, 'message' => "$totalCallFlows call flow(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No call flows deleted'];
    }
}
