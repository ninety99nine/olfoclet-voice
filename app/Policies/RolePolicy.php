<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

class RolePolicy extends BasePolicy
{
    /**
     * Grant all permissions to super admins who have roles not tied to any organization, except for update and delete.
     *
     * @param User $user
     * @param string $ability
     * @return bool|null
     */
    public function before(User $user, string $ability): bool|null
    {
        if (!in_array($ability, ['update', 'delete', 'deleteAny'])) {
            return $this->authService->isSuperAdmin($user);
        }
        return null;
    }

    /**
     * Determine whether the user can view any roles.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view roles', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the role.
     *
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function view(User $user, Role $role): bool
    {
        return $this->isOrgUserWithPermission($user, 'view roles', $role->organization_id);
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create roles', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function update(User $user, Role $role): bool
    {
        return $role->organization_id && (
            $this->authService->isSuperAdmin($user) ||
            $this->isOrgUserWithPermission($user, 'edit roles', $role->organization_id)
        );
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param User $user
     * @param Role $role
     * @return bool
     */
    public function delete(User $user, Role $role): bool
    {
        return $role->organization_id && (
            $this->authService->isSuperAdmin($user) ||
            $this->isOrgUserWithPermission($user, 'edit roles', $role->organization_id)
        );
    }

    /**
     * Determine whether the user can delete any roles.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId && (
            $this->authService->isSuperAdmin($user) ||
            $this->isOrgUserWithPermission($user, 'edit roles', $organizationId)
        );
    }
}
