<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use Illuminate\Http\JsonResponse;
use App\Services\KnowledgeBaseService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\KnowledgeBaseResources;
use App\Http\Requests\KnowledgeBase\ShowKnowledgeBasesRequest;
use App\Http\Requests\KnowledgeBase\CreateKnowledgeBaseRequest;
use App\Http\Requests\KnowledgeBase\ShowKnowledgeBaseRequest;
use App\Http\Requests\KnowledgeBase\UpdateKnowledgeBaseRequest;
use App\Http\Requests\KnowledgeBase\DeleteKnowledgeBaseRequest;
use App\Http\Requests\KnowledgeBase\DeleteKnowledgeBasesRequest;

class KnowledgeBaseController extends BaseController
{
    /**
     * @var KnowledgeBaseService
     */
    protected $service;

    /**
     * KnowledgeBaseController constructor.
     *
     * @param KnowledgeBaseService $service
     */
    public function __construct(KnowledgeBaseService $service)
    {
        $this->service = $service;
    }

    /**
     * Show knowledge bases.
     *
     * @param ShowKnowledgeBasesRequest $request
     * @return KnowledgeBaseResources|JsonResponse
     */
    public function showKnowledgeBases(ShowKnowledgeBasesRequest $request): KnowledgeBaseResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showKnowledgeBases(request('organization_id')));
    }

    /**
     * Create knowledge base.
     *
     * @param CreateKnowledgeBaseRequest $request
     * @return JsonResponse
     */
    public function createKnowledgeBase(CreateKnowledgeBaseRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createKnowledgeBase(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Show single knowledge base.
     *
     * @param ShowKnowledgeBaseRequest $request
     * @param KnowledgeBase $knowledgeBase
     * @return JsonResponse
     */
    public function showKnowledgeBase(ShowKnowledgeBaseRequest $request, KnowledgeBase $knowledgeBase): JsonResponse
    {
        return $this->prepareOutput($this->service->showKnowledgeBase($knowledgeBase->id));
    }

    /**
     * Update knowledge base.
     *
     * @param UpdateKnowledgeBaseRequest $request
     * @param KnowledgeBase $knowledgeBase
     * @return JsonResponse
     */
    public function updateKnowledgeBase(UpdateKnowledgeBaseRequest $request, KnowledgeBase $knowledgeBase): JsonResponse
    {
        return $this->prepareOutput($this->service->updateKnowledgeBase($knowledgeBase->id, $request->validated()));
    }

    /**
     * Delete knowledge base.
     *
     * @param DeleteKnowledgeBaseRequest $request
     * @param KnowledgeBase $knowledgeBase
     * @return JsonResponse
     */
    public function deleteKnowledgeBase(DeleteKnowledgeBaseRequest $request, KnowledgeBase $knowledgeBase): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteKnowledgeBase($knowledgeBase->id));
    }

    /**
     * Delete multiple knowledge bases.
     *
     * @param DeleteKnowledgeBasesRequest $request
     * @return JsonResponse
     */
    public function deleteKnowledgeBases(DeleteKnowledgeBasesRequest $request): JsonResponse
    {
        $knowledgeBaseIds = request()->input('knowledge_base_ids', []);
        return $this->prepareOutput($this->service->deleteKnowledgeBases(request('organization_id'), $knowledgeBaseIds));
    }
}
