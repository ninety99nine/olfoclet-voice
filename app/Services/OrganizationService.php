<?php

namespace App\Services;

use App\Models\Organization;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\OrganizationResources;

class OrganizationService extends BaseService
{
    /**
     * Show Organizations.
     *
     * @return OrganizationResources|array
     */
    public function showOrganizations(): OrganizationResources|array
    {
        $query = Organization::query()->when(!request()->has('_sort'), fn($query) => $query->latest());
        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create Organization.
     *
     * @param array $data
     * @return array
     */
    public function createOrganization(array $data): array
    {
        $Organization = Organization::create($data);
        return $this->showCreatedResource($Organization);
    }

    /**
     * Delete Organization.
     *
     * @param int $OrganizationId
     * @return array
     */
    public function deleteOrganization(int $OrganizationId): array
    {
        $Organization = Organization::findOrFail($OrganizationId);

        if($Organization) {

            $deleted = $Organization->delete();

            if ($deleted) {
                return ['deleted' => true, 'message' => 'Organization deleted'];
            }else{
                return ['deleted' => false, 'message' => 'Organization delete unsuccessful'];
            }

        }else{
            return ['deleted' => false, 'message' => 'This Organization does not exist'];
        }
    }

    /**
     * Show organization by alias.
     *
     * @param string $alias
     * @return OrganizationResource
     */
    public function showOrganizationByAlias(string $alias): OrganizationResource
    {
        $organization = Organization::where('alias', $alias)->firstOrFail();
        return $this->showResource($organization);
    }

    /**
     * Delete Organizations.
     *
     * @param array $OrganizationIds
     * @return array
     */
    public function deleteOrganizations(array $OrganizationIds): array
    {
        $Organizations = Organization::whereIn('id', $OrganizationIds)->get();

        if($totalOrganizations = $Organizations->count()) {

            foreach($Organizations as $Organization) {
                $Organization->delete();
            }

            return ['deleted' => true, 'message' => $totalOrganizations  .($totalOrganizations  == 1 ? ' Organization': ' Organizations') . ' deleted'];

        }else{
            return ['deleted' => false, 'message' => 'No Organizations deleted'];
        }
    }

    /**
     * Show Organization.
     *
     * @param int $OrganizationId
     * @return OrganizationResource
     */
    public function showOrganization(int $OrganizationId): OrganizationResource
    {
        $Organization = Organization::findOrFail($OrganizationId);
        return $this->showResource($Organization);
    }

    /**
     * Update Organization.
     *
     * @param int $OrganizationId
     * @param array $data
     * @return array
     */
    public function updateOrganization(int $OrganizationId, array $data): array
    {
        $Organization = Organization::findOrFail($OrganizationId);

        if($Organization) {

            $Organization->update($data);
            return $this->showUpdatedResource($Organization);

        }else{

            return ['updated' => false, 'message' => 'This Organization does not exist'];

        }
    }
}
