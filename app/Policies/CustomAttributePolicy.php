<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CustomAttribute;

class CustomAttributePolicy extends BasePolicy
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
     * Determine whether the user can view any custom attributes.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view custom attributes', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the custom attribute.
     *
     * @param User $user
     * @param CustomAttribute $customAttribute
     * @return bool
     */
    public function view(User $user, CustomAttribute $customAttribute): bool
    {
        return $this->isOrgUserWithPermission($user, 'view custom attributes', $customAttribute->organization_id);
    }

    /**
     * Determine whether the user can create custom attributes.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create custom attributes', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the custom attribute.
     *
     * @param User $user
     * @param CustomAttribute $customAttribute
     * @return bool
     */
    public function update(User $user, CustomAttribute $customAttribute): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit custom attributes', $customAttribute->organization_id);
    }

    /**
     * Determine whether the user can delete the custom attribute.
     *
     * @param User $user
     * @param CustomAttribute $customAttribute
     * @return bool
     */
    public function delete(User $user, CustomAttribute $customAttribute): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit custom attributes', $customAttribute->organization_id);
    }

    /**
     * Determine whether the user can delete any custom attributes.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit custom attributes', $organizationId) : false;
    }
}
