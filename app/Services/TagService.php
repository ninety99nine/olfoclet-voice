<?php

namespace App\Services;

use App\Models\Tag;
use App\Http\Resources\TagResource;
use App\Http\Resources\TagResources;

class TagService extends BaseService
{
    /**
     * Show tags.
     *
     * @param string|null $organizationId
     * @return TagResources|array
     */
    public function showTags(?string $organizationId = null): TagResources|array
    {
        $query = Tag::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create tag.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createTag(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;
        $tag = Tag::create($data);

        return $this->showCreatedResource($tag);
    }

    /**
     * Show tag.
     *
     * @param string $tagId
     * @return TagResource
     */
    public function showTag(string $tagId): TagResource
    {
        $tag = Tag::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($tagId);
        return $this->showResource($tag);
    }

    /**
     * Update tag.
     *
     * @param string $tagId
     * @param array $data
     * @return array
     */
    public function updateTag(string $tagId, array $data): array
    {
        $tag = Tag::findOrFail($tagId);
        $tag->update($data);

        return $this->showUpdatedResource($tag);
    }

    /**
     * Delete tag.
     *
     * @param string $tagId
     * @return array
     */
    public function deleteTag(string $tagId): array
    {
        $tag = Tag::findOrFail($tagId);

        $deleted = $tag->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Tag deleted'];
        }

        return ['deleted' => false, 'message' => 'Tag delete unsuccessful'];
    }

    /**
     * Delete tags.
     *
     * @param string|null $organizationId
     * @param array $tagIds
     * @return array
     */
    public function deleteTags(?string $organizationId, array $tagIds): array
    {
        $query = Tag::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $tagIds);

        $tags = $query->get();

        if ($totalTags = $tags->count()) {
            $tags->each->delete();
            return ['deleted' => true, 'message' => "$totalTags tag(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No tags deleted'];
    }
}
