<?php

namespace App\Services;

use App\Models\HelpCenterCollection;
use App\Http\Resources\HelpCenterCollectionResource;
use App\Http\Resources\HelpCenterCollectionResources;

class HelpCenterCollectionService extends BaseService
{
    /**
     * Show help center collections.
     *
     * @param string|null $organizationId
     * @return HelpCenterCollectionResources|array
     */
    public function showHelpCenterCollections(?string $organizationId = null): HelpCenterCollectionResources|array
    {
        $query = HelpCenterCollection::query()
            ->when($organizationId, fn($query) => $query->whereHas('knowledgeBase', fn($kbQuery) => $kbQuery->where('organization_id', $organizationId)))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create help center collection.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createHelpCenterCollection(string $organizationId, array $data): array
    {
        $helpCenterCollection = HelpCenterCollection::create($data);

        return $this->showCreatedResource($helpCenterCollection);
    }

    /**
     * Show help center collection.
     *
     * @param string $helpCenterCollectionId
     * @return HelpCenterCollectionResource
     */
    public function showHelpCenterCollection(string $helpCenterCollectionId): HelpCenterCollectionResource
    {
        $helpCenterCollection = HelpCenterCollection::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($helpCenterCollectionId);
        return $this->showResource($helpCenterCollection);
    }

    /**
     * Update help center collection.
     *
     * @param string $helpCenterCollectionId
     * @param array $data
     * @return array
     */
    public function updateHelpCenterCollection(string $helpCenterCollectionId, array $data): array
    {
        $helpCenterCollection = HelpCenterCollection::findOrFail($helpCenterCollectionId);
        $helpCenterCollection->update($data);

        return $this->showUpdatedResource($helpCenterCollection);
    }

    /**
     * Delete help center collection.
     *
     * @param string $helpCenterCollectionId
     * @return array
     */
    public function deleteHelpCenterCollection(string $helpCenterCollectionId): array
    {
        $helpCenterCollection = HelpCenterCollection::findOrFail($helpCenterCollectionId);

        $deleted = $helpCenterCollection->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Help center collection deleted'];
        }

        return ['deleted' => false, 'message' => 'Help center collection delete unsuccessful'];
    }

    /**
     * Delete help center collections.
     *
     * @param string|null $organizationId
     * @param array $helpCenterCollectionIds
     * @return array
     */
    public function deleteHelpCenterCollections(?string $organizationId, array $helpCenterCollectionIds): array
    {
        $query = HelpCenterCollection::query()
            ->when($organizationId, fn($query) => $query->whereHas('knowledgeBase', fn($kbQuery) => $kbQuery->where('organization_id', $organizationId)))
            ->whereIn('id', $helpCenterCollectionIds);

        $helpCenterCollections = $query->get();

        if ($totalHelpCenterCollections = $helpCenterCollections->count()) {
            $helpCenterCollections->each->delete();
            return ['deleted' => true, 'message' => "$totalHelpCenterCollections help center collection(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No help center collections deleted'];
    }
}
