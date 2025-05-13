<?php

namespace App\Services;

use App\Models\ContentItem;
use App\Http\Resources\ContentItemResource;
use App\Http\Resources\ContentItemResources;

class ContentItemService extends BaseService
{
    /**
     * Show content items.
     *
     * @param string|null $knowledgeBaseId
     * @return ContentItemResources|array
     */
    public function showContentItems(?string $knowledgeBaseId = null): ContentItemResources|array
    {
        $query = ContentItem::query()
            ->when($knowledgeBaseId, fn($query) => $query->where('knowledge_base_id', $knowledgeBaseId))
            ->when(request()->input('parent_id'), fn($query) => $query->where('parent_id', request()->input('parent_id')))
            ->when(request()->input('type'), fn($query) => $query->where('type', request()->input('type')))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        $query->with($this->getRequestRelationships());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Get the folder tree for a knowledge base.
     *
     * @param string $knowledgeBaseId
     * @return array
     */
    public function getFolderTree(string $knowledgeBaseId): array
    {
        // Fetch all folders for the given knowledge base with minimal fields
        $folders = ContentItem::query()
            ->select('id', 'title', 'parent_id')
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->where('type', 'folder')
            ->withCount('children')
            ->get();

        return $this->buildFolderTree($folders);
    }

    /**
     * Build a hierarchical folder tree structure.
     *
     * @param \Illuminate\Database\Eloquent\Collection $folders
     * @return array
     */
    protected function buildFolderTree($folders): array
    {
        // Group folders by parent_id for efficient tree building
        $folderMap = [];
        foreach ($folders as $folder) {
            $folderMap[$folder->parent_id ?? 'root'][] = $folder;
        }

        // Recursive function to build the tree and compute hasContent
        $buildTree = function ($parentId = null) use (&$buildTree, $folderMap) {
            $tree = [];
            $folders = $folderMap[$parentId === null ? 'root' : $parentId] ?? [];

            foreach ($folders as $folder) {
                $subfolders = $buildTree($folder->id);

                // Check if the folder has direct content (any child items, including subfolders)
                $hasDirectContent = $folder->children_count > 0;

                // Check if any subfolder has content
                $hasSubfolderContent = collect($subfolders)->some(fn($subfolder) => $subfolder['hasContent']);

                $tree[] = [
                    'id' => $folder->id,
                    'title' => $folder->title,
                    'subfolders' => $subfolders,
                    'hasContent' => $hasDirectContent || $hasSubfolderContent,
                ];
            }

            return $tree;
        };

        return $buildTree();
    }

    /**
     * Create content item.
     *
     * @param string|null $knowledgeBaseId
     * @param array $data
     * @return array
     */
    public function createContentItem(?string $knowledgeBaseId = null, array $data): array
    {
        $contentItem = ContentItem::create($data);
        return $this->showCreatedResource($contentItem);
    }

    /**
     * Show content item.
     *
     * @param string $contentItemId
     * @return ContentItemResource
     */
    public function showContentItem(string $contentItemId): ContentItemResource
    {
        $contentItem = ContentItem::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($contentItemId);
        return $this->showResource($contentItem);
    }

    /**
     * Update content item.
     *
     * @param string $contentItemId
     * @param array $data
     * @return array
     */
    public function updateContentItem(string $contentItemId, array $data): array
    {
        $contentItem = ContentItem::findOrFail($contentItemId);
        $contentItem->update($data);
        return $this->showUpdatedResource($contentItem);
    }

    /**
     * Delete content item.
     *
     * @param string $contentItemId
     * @return array
     */
    public function deleteContentItem(string $contentItemId): array
    {
        $contentItem = ContentItem::findOrFail($contentItemId);
        $deleted = $contentItem->delete();
        if ($deleted) {
            return ['deleted' => true, 'message' => 'Content item deleted'];
        }
        return ['deleted' => false, 'message' => 'Content item delete unsuccessful'];
    }

    /**
     * Delete content items.
     *
     * @param string|null $knowledgeBaseId
     * @param array $contentItemIds
     * @return array
     */
    public function deleteContentItems(?string $knowledgeBaseId, array $contentItemIds): array
    {
        $query = ContentItem::query()
            ->when($knowledgeBaseId, fn($query) => $query->where('knowledge_base_id', $knowledgeBaseId))
            ->whereIn('id', $contentItemIds);

        $contentItems = $query->get();

        if ($totalContentItems = $contentItems->count()) {
            $contentItems->each->delete();
            return ['deleted' => true, 'message' => "$totalContentItems content item(s) deleted"];
        }
        return ['deleted' => false, 'message' => 'No content items deleted'];
    }
}
