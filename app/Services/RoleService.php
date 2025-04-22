<?php

namespace App\Services;

use App\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleResources;

class RoleService extends BaseService
{
    /**
     * Show Roles.
     *
     * @param string|null $organizationId
     * @return RoleResources|array
     */
    public function showRoles(?string $organizationId = null): RoleResources|array
    {
        $query = Role::query()
            ->when($organizationId, fn($q) => $q->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($q) => $q->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create Role.
     *
     * @param string|null $organizationId
     * @param array $data
     * @return array
     */
    public function createRole(?string $organizationId, array $data): array
    {
        $role = Role::create([
            'name' => $data['name'],
            'organization_id' => $organizationId
        ]);

        return $this->showCreatedResource($role);
    }

    /**
     * Delete Roles.
     *
     * @param string|null $organizationId
     * @param array $roleIds
     * @return array
     */
    public function deleteRoles(?string $organizationId, array $roleIds): array
    {
        $query = Role::query()->whereIn('id', $roleIds);

        if ($organizationId) {
            $query->where('organization_id', $organizationId);
        }

        $roles = $query->get();

        if ($roles->count()) {
            $roles->each->delete();
            return ['deleted' => true, 'message' => "{$roles->count()} role(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No roles deleted'];
    }

    /**
     * Show Role.
     *
     * @param string $roleId
     * @return RoleResource
     */
    public function showRole(string $roleId): RoleResource
    {
        $role = Role::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($roleId);
        return $this->showResource($role);
    }

    /**
     * Update Role.
     *
     * @param string $roleId
     * @param array $data
     * @return array
     */
    public function updateRole(string $roleId, array $data): array
    {
        $query = Role::query();

        $role = $query->findOrFail($roleId);

        $role->update($data);

        return $this->showUpdatedResource($role);
    }

    /**
     * Delete Role.
     *
     * @param string $roleId
     * @return array
     */
    public function deleteRole(string $roleId): array
    {
        $query = Role::query();

        $role = $query->findOrFail($roleId);

        $deleted = $role->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Role deleted'];
        } else {
            return ['deleted' => false, 'message' => 'Role delete unsuccessful'];
        }
    }
}
