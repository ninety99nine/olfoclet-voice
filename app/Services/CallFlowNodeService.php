<?php

namespace App\Services;

use App\Models\CallFlow;
use App\Models\CallFlowNode;
use App\Http\Resources\CallFlowNodeResource;
use App\Http\Resources\CallFlowNodeResources;

class CallFlowNodeService extends BaseService
{
    /**
     * Show call flow nodes.
     *
     * @param string|null $callFlowId
     * @return CallFlowNodeResources|array
     */
    public function showCallFlowNodes(?string $callFlowId = null): CallFlowNodeResources|array
    {
        $query = CallFlowNode::query()
            ->when($callFlowId, fn($query) => $query->where('call_flow_id', $callFlowId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create call flow node.
     *
     * @param string $callFlowId
     * @param array $data
     * @return array
     */
    public function createCallFlowNode(string $callFlowId, array $data): array
    {
        $callFlow = CallFlow::findOrFail($callFlowId);
        $data['call_flow_id'] = $callFlowId;
        $callFlowNode = CallFlowNode::create($data);
        return $this->showCreatedResource($callFlowNode);
    }

    /**
     * Show call flow node.
     *
     * @param string $callFlowNodeId
     * @return CallFlowNodeResource
     */
    public function showCallFlowNode(string $callFlowNodeId): CallFlowNodeResource
    {
        $callFlowNode = CallFlowNode::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($callFlowNodeId);
        return $this->showResource($callFlowNode);
    }

    /**
     * Update call flow node.
     *
     * @param string $callFlowNodeId
     * @param array $data
     * @return array
     */
    public function updateCallFlowNode(string $callFlowNodeId, array $data): array
    {
        $callFlowNode = CallFlowNode::findOrFail($callFlowNodeId);
        $callFlowNode->update($data);
        return $this->showUpdatedResource($callFlowNode);
    }

    /**
     * Delete call flow node.
     *
     * @param string $callFlowNodeId
     * @return array
     */
    public function deleteCallFlowNode(string $callFlowNodeId): array
    {
        $callFlowNode = CallFlowNode::findOrFail($callFlowNodeId);

        $deleted = $callFlowNode->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Call flow node deleted'];
        }

        return ['deleted' => false, 'message' => 'Call flow node delete unsuccessful'];
    }

    /**
     * Delete call flow nodes.
     *
     * @param string|null $callFlowId
     * @param array $callFlowNodeIds
     * @return array
     */
    public function deleteCallFlowNodes(?string $callFlowId, array $callFlowNodeIds): array
    {
        $query = CallFlowNode::query()
            ->when($callFlowId, fn($query) => $query->where('call_flow_id', $callFlowId))
            ->whereIn('id', $callFlowNodeIds);

        $callFlowNodes = $query->get();

        if ($totalCallFlowNodes = $callFlowNodes->count()) {
            $callFlowNodes->each->delete();
            return ['deleted' => true, 'message' => "$totalCallFlowNodes call flow node(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No call flow nodes deleted'];
    }
}
