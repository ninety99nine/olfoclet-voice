<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
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
     * Determine whether the user can view any users.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $this->isOrgUserWithPermission($user, 'view users', request('organization_id'));
    }

    /**
     * Determine whether the user can view the user.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return $this->isOrgUserWithPermission($user, 'view users', $model->organization_id)
            || $user->id === $model->id; // Allow users to view their own profile
    }

    /**
     * Determine whether the user can create users.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $this->isOrgUserWithPermission($user, 'create users', request('organization_id'));
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit users', $model->organization_id)
            || $user->id === $model->id; // Allow users to update their own profile
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit users', $model->organization_id);
    }
}
