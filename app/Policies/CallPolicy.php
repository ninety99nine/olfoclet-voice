<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Call;

class CallPolicy extends BasePolicy
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
     * Determine whether the user can view any calls.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Super admins can view all calls; others need permission within an organization
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view calls', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the call.
     *
     * @param User $user
     * @param Call $call
     * @return bool
     */
    public function view(User $user, Call $call): bool
    {
        return $this->isOrgUserWithPermission($user, 'view calls', $call->organization_id);
    }

    /**
     * Determine whether the user can create calls.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Super admins can create calls; others need permission within an organization
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create calls', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the call.
     *
     * @param User $user
     * @param Call $call
     * @return bool
     */
    public function update(User $user, Call $call): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit calls', $call->organization_id);
    }

    /**
     * Determine whether the user can delete the call.
     *
     * @param User $user
     * @param Call $call
     * @return bool
     */
    public function delete(User $user, Call $call): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit calls', $call->organization_id);
    }
}
