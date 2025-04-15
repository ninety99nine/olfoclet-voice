<?php

namespace App\Services;

use App\Models\Organization;
use Spatie\Permission\Models\Role;
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
        $organization = Organization::create($data);
        Role::create(['name' => 'admin', 'organization_id' => $organization->id]);
        Role::create(['name' => 'agent', 'organization_id' => $organization->id]);

        return $this->showCreatedResource($organization);
    }

    /**
     * Delete Organization.
     *
     * @param int $organizationId
     * @return array
     */
    public function deleteOrganization(int $organizationId): array
    {
        $organization = Organization::findOrFail($organizationId);

        if($organization) {

            $deleted = $organization->delete();

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
     * @param array $organizationIds
     * @return array
     */
    public function deleteOrganizations(array $organizationIds): array
    {
        $organizations = Organization::whereIn('id', $organizationIds)->get();

        if($totalOrganizations = $organizations->count()) {

            foreach($organizations as $organization) {
                $organization->delete();
            }

            return ['deleted' => true, 'message' => $totalOrganizations  .($totalOrganizations  == 1 ? ' Organization': ' Organizations') . ' deleted'];

        }else{
            return ['deleted' => false, 'message' => 'No Organizations deleted'];
        }
    }

    /**
     * Show Organization.
     *
     * @param int $organizationId
     * @return OrganizationResource
     */
    public function showOrganization(int $organizationId): OrganizationResource
    {
        $organization = Organization::findOrFail($organizationId);
        return $this->showResource($organization);
    }

    /**
     * Update Organization.
     *
     * @param int $organizationId
     * @param array $data
     * @return array
     */
    public function updateOrganization(int $organizationId, array $data): array
    {
        $organization = Organization::findOrFail($organizationId);

        if($organization) {

            $organization->update($data);
            return $this->showUpdatedResource($organization);

        }else{

            return ['updated' => false, 'message' => 'This Organization does not exist'];

        }
    }
}
