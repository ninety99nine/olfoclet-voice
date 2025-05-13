<?php

namespace App\Services;

use App\Models\ContentSource;
use App\Http\Resources\ContentSourceResource;
use App\Http\Resources\ContentSourceResources;

class ContentSourceService extends BaseService
{
    /**
     * Show content sources.
     *
     * @param string|null $knowledgeBaseId
     * @return ContentSourceResources|array
     */
    public function showContentSources(?string $knowledgeBaseId = null): ContentSourceResources|array
    {
        $query = ContentSource::query()
            ->when($knowledgeBaseId, fn($query) => $query->where('knowledge_base_id', $knowledgeBaseId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create content source.
     *
     * @param string|null $knowledgeBaseId
     * @param array $data
     * @return array
     */
    public function createContentSource(?string $knowledgeBaseId = null, array $data): array
    {
        $contentSource = ContentSource::create($data);

        return $this->showCreatedResource($contentSource);
    }

    /**
     * Show content source.
     *
     * @param string $contentSourceId
     * @return ContentSourceResource
     */
    public function showContentSource(string $contentSourceId): ContentSourceResource
    {
        $contentSource = ContentSource::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($contentSourceId);
        return $this->showResource($contentSource);
    }

    /**
     * Update content source.
     *
     * @param string $contentSourceId
     * @param array $data
     * @return array
     */
    public function updateContentSource(string $contentSourceId, array $data): array
    {
        $contentSource = ContentSource::findOrFail($contentSourceId);
        $contentSource->update($data);

        return $this->showUpdatedResource($contentSource);
    }

    /**
     * Delete content source.
     *
     * @param string $contentSourceId
     * @return array
     */
    public function deleteContentSource(string $contentSourceId): array
    {
        $contentSource = ContentSource::findOrFail($contentSourceId);

        $deleted = $contentSource->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Content source deleted'];
        }

        return ['deleted' => false, 'message' => 'Content source delete unsuccessful'];
    }

    /**
     * Delete content sources.
     *
     * @param string|null $knowledgeBaseId
     * @param array $contentSourceIds
     * @return array
     */
    public function deleteContentSources(?string $knowledgeBaseId, array $contentSourceIds): array
    {
        $query = ContentSource::query()
            ->when($knowledgeBaseId, fn($query) => $query->where('knowledge_base_id', $knowledgeBaseId))
            ->whereIn('id', $contentSourceIds);

        $contentSources = $query->get();

        if ($totalContentSources = $contentSources->count()) {
            $contentSources->each->delete();
            return ['deleted' => true, 'message' => "$totalContentSources content source(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No content sources deleted'];
    }
}
