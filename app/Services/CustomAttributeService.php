<?php

namespace App\Services;

use App\Models\CustomAttribute;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\CustomAttributeResource;
use App\Http\Resources\CustomAttributeResources;

class CustomAttributeService extends BaseService
{
    /**
     * Show custom attributes.
     *
     * @param string|null $organizationId
     * @return CustomAttributeResources|array
     */
    public function showCustomAttributes(?string $organizationId = null): CustomAttributeResources|array
    {
        $query = CustomAttribute::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create custom attribute.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createCustomAttribute(string $organizationId, array $data): array
    {
        return DB::transaction(function () use ($organizationId, $data) {
            $data['organization_id'] = $organizationId;
            $customAttribute = CustomAttribute::create([
                'organization_id' => $data['organization_id'],
                'name' => $data['name'],
                'type' => $data['type'],
            ]);

            return $this->showCreatedResource($customAttribute);
        });
    }

    /**
     * Show custom attribute.
     *
     * @param string $customAttributeId
     * @return CustomAttributeResource
     */
    public function showCustomAttribute(string $customAttributeId): CustomAttributeResource
    {
        $customAttribute = CustomAttribute::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($customAttributeId);
        return $this->showResource($customAttribute);
    }

    /**
     * Update custom attribute.
     *
     * @param string $customAttributeId
     * @param array $data
     * @return array
     */
    public function updateCustomAttribute(string $customAttributeId, array $data): array
    {
        $customAttribute = CustomAttribute::findOrFail($customAttributeId);
        $customAttribute->update($data);

        return $this->showUpdatedResource($customAttribute);
    }

    /**
     * Delete custom attribute.
     *
     * @param string $customAttributeId
     * @return array
     */
    public function deleteCustomAttribute(string $customAttributeId): array
    {
        $customAttribute = CustomAttribute::findOrFail($customAttributeId);

        $deleted = $customAttribute->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Custom attribute deleted'];
        }

        return ['deleted' => false, 'message' => 'Custom attribute delete unsuccessful'];
    }

    /**
     * Delete custom attributes.
     *
     * @param string|null $organizationId
     * @param array $customAttributeIds
     * @return array
     */
    public function deleteCustomAttributes(?string $organizationId, array $customAttributeIds): array
    {
        $query = CustomAttribute::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $customAttributeIds);

        $customAttributes = $query->get();

        if ($totalCustomAttributes = $customAttributes->count()) {
            $customAttributes->each->delete();
            return ['deleted' => true, 'message' => "$totalCustomAttributes custom attribute(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No custom attributes deleted'];
    }
}
