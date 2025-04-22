<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Organization;

class OrganizationPolicy extends BasePolicy
{
    /**
     * Grant all permissions to super admins who have roles not tied to any organization.
     *
     * @param User $user
     * @param string $ability
     * @return bool|null
     */
    public function before(User $user, string $ability): bool|null
    {
        return $this->authService->isSuperAdmin($user);
    }

    /**
     * Determine whether the user can view any organizations.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view organizations', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the organization.
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function view(User $user, Organization $organization): bool
    {
        return $this->isOrgUserWithPermission($user, 'view organizations', $organization->id);
    }

    /**
     * Determine whether the user can create organizations.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $this->authService->isSuperAdmin($user);
    }

    /**
     * Determine whether the user can update the organization.
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function update(User $user, Organization $organization): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit organizations', $organization->id);
    }

    /**
     * Determine whether the user can delete the organization.
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit organizations', $organization->id);
    }

    /**
     * Determine whether the user can delete any organizations.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit organizations', $organizationId) : false;
    }
}
