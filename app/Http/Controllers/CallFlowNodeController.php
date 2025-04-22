<?php

namespace App\Http\Controllers;

use App\Models\CallFlowNode;
use Illuminate\Http\JsonResponse;
use App\Services\CallFlowNodeService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\CallFlowNodeResources;
use App\Http\Requests\CallFlowNode\ShowCallFlowNodeRequest;
use App\Http\Requests\CallFlowNode\ShowCallFlowNodesRequest;
use App\Http\Requests\CallFlowNode\CreateCallFlowNodeRequest;
use App\Http\Requests\CallFlowNode\UpdateCallFlowNodeRequest;
use App\Http\Requests\CallFlowNode\DeleteCallFlowNodeRequest;
use App\Http\Requests\CallFlowNode\DeleteCallFlowNodesRequest;

class CallFlowNodeController extends BaseController
{
    /**
     * @var CallFlowNodeService
     */
    protected $service;

    /**
     * CallFlowNodeController constructor.
     *
     * @param CallFlowNodeService $service
     */
    public function __construct(CallFlowNodeService $service)
    {
        $this->service = $service;
    }

    /**
     * Show call flow nodes.
     *
     * @param ShowCallFlowNodesRequest $request
     * @return CallFlowNodeResources|JsonResponse
     */
    public function showCallFlowNodes(ShowCallFlowNodesRequest $request): CallFlowNodeResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showCallFlowNodes(request('call_flow_id')));
    }

    /**
     * Create call flow node.
     *
     * @param CreateCallFlowNodeRequest $request
     * @return JsonResponse
     */
    public function createCallFlowNode(CreateCallFlowNodeRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createCallFlowNode(request('call_flow_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple call flow nodes.
     *
     * @param DeleteCallFlowNodesRequest $request
     * @return JsonResponse
     */
    public function deleteCallFlowNodes(DeleteCallFlowNodesRequest $request): JsonResponse
    {
        $callFlowNodeIds = request()->input('call_flow_node_ids', []);
        return $this->prepareOutput($this->service->deleteCallFlowNodes(request('call_flow_id'), $callFlowNodeIds));
    }

    /**
     * Show single call flow node.
     *
     * @param ShowCallFlowNodeRequest $request
     * @param CallFlowNode $callFlowNode
     * @return JsonResponse
     */
    public function showCallFlowNode(ShowCallFlowNodeRequest $request, CallFlowNode $callFlowNode): JsonResponse
    {
        return $this->prepareOutput($this->service->showCallFlowNode($callFlowNode->id));
    }

    /**
     * Update call flow node.
     *
     * @param UpdateCallFlowNodeRequest $request
     * @param CallFlowNode $callFlowNode
     * @return JsonResponse
     */
    public function updateCallFlowNode(UpdateCallFlowNodeRequest $request, CallFlowNode $callFlowNode): JsonResponse
    {
        return $this->prepareOutput($this->service->updateCallFlowNode($callFlowNode->id, $request->validated()));
    }

    /**
     * Delete call flow node.
     *
     * @param DeleteCallFlowNodeRequest $request
     * @param CallFlowNode $callFlowNode
     * @return JsonResponse
     */
    public function deleteCallFlowNode(DeleteCallFlowNodeRequest $request, CallFlowNode $callFlowNode): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteCallFlowNode($callFlowNode->id));
    }
}
