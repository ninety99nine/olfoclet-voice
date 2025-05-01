<?php

namespace App\Services;

use App\Models\KnowledgeBase;
use App\Http\Resources\KnowledgeBaseResource;
use App\Http\Resources\KnowledgeBaseResources;

class KnowledgeBaseService extends BaseService
{
    /**
     * Show knowledge bases.
     *
     * @param string|null $organizationId
     * @return KnowledgeBaseResources|array
     */
    public function showKnowledgeBases(?string $organizationId = null): KnowledgeBaseResources|array
    {
        $query = KnowledgeBase::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create knowledge base.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createKnowledgeBase(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;
        $knowledgeBase = KnowledgeBase::create($data);

        return $this->showCreatedResource($knowledgeBase);
    }

    /**
     * Show knowledge base.
     *
     * @param string $knowledgeBaseId
     * @return KnowledgeBaseResource
     */
    public function showKnowledgeBase(string $knowledgeBaseId): KnowledgeBaseResource
    {
        $knowledgeBase = KnowledgeBase::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($knowledgeBaseId);
        return $this->showResource($knowledgeBase);
    }

    /**
     * Update knowledge base.
     *
     * @param string $knowledgeBaseId
     * @param array $data
     * @return array
     */
    public function updateKnowledgeBase(string $knowledgeBaseId, array $data): array
    {
        $knowledgeBase = KnowledgeBase::findOrFail($knowledgeBaseId);
        $knowledgeBase->update($data);

        return $this->showUpdatedResource($knowledgeBase);
    }

    /**
     * Delete knowledge base.
     *
     * @param string $knowledgeBaseId
     * @return array
     */
    public function deleteKnowledgeBase(string $knowledgeBaseId): array
    {
        $knowledgeBase = KnowledgeBase::findOrFail($knowledgeBaseId);

        $deleted = $knowledgeBase->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Knowledge base deleted'];
        }

        return ['deleted' => false, 'message' => 'Knowledge base delete unsuccessful'];
    }

    /**
     * Delete knowledge bases.
     *
     * @param string|null $organizationId
     * @param array $knowledgeBaseIds
     * @return array
     */
    public function deleteKnowledgeBases(?string $organizationId, array $knowledgeBaseIds): array
    {
        $query = KnowledgeBase::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $knowledgeBaseIds);

        $knowledgeBases = $query->get();

        if ($totalKnowledgeBases = $knowledgeBases->count()) {
            $knowledgeBases->each->delete();
            return ['deleted' => true, 'message' => "$totalKnowledgeBases knowledge base(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No knowledge bases deleted'];
    }
}
