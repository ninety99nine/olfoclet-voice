<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
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
     * Determine whether the user can view any users.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view users', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the user.
     *
     * @param User $authUser
     * @param User $targetUser
     * @return bool
     */
    public function view(User $authUser, User $targetUser): bool
    {
        return $targetUser->organization_id && (
            $this->authService->isSuperAdmin($authUser) ||
            $this->isOrgUserWithPermission($authUser, 'view users', $targetUser->organization_id)
        );
    }

    /**
     * Determine whether the user can create users.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create users', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param User $authUser
     * @param User $targetUser
     * @return bool
     */
    public function update(User $authUser, User $targetUser): bool
    {
        return $targetUser->organization_id && (
            $this->authService->isSuperAdmin($authUser) ||
            $this->isOrgUserWithPermission($authUser, 'edit users', $targetUser->organization_id)
        );
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param User $authUser
     * @param User $targetUser
     * @return bool
     */
    public function delete(User $authUser, User $targetUser): bool
    {
        return $targetUser->organization_id && (
            $this->authService->isSuperAdmin($authUser) ||
            $this->isOrgUserWithPermission($authUser, 'edit users', $targetUser->organization_id)
        );
    }

    /**
     * Determine whether the user can delete any users.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId && (
            $this->authService->isSuperAdmin($user) ||
            $this->isOrgUserWithPermission($user, 'edit users', $organizationId)
        );
    }
}
