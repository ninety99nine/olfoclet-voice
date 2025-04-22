<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Number;

class NumberPolicy extends BasePolicy
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
     * Determine whether the user can view any numbers.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view numbers', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the number.
     *
     * @param User $user
     * @param Number $number
     * @return bool
     */
    public function view(User $user, Number $number): bool
    {
        return $this->isOrgUserWithPermission($user, 'view numbers', $number->organization_id);
    }

    /**
     * Determine whether the user can create numbers.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create numbers', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the number.
     *
     * @param User $user
     * @param Number $number
     * @return bool
     */
    public function update(User $user, Number $number): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit numbers', $number->organization_id);
    }

    /**
     * Determine whether the user can delete the number.
     *
     * @param User $user
     * @param Number $number
     * @return bool
     */
    public function delete(User $user, Number $number): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit numbers', $number->organization_id);
    }

    /**
     * Determine whether the user can delete any numbers.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit numbers', $organizationId) : false;
    }
}
