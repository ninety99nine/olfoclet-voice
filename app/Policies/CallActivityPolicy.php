<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CallActivity;

class CallActivityPolicy extends BasePolicy
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
     * Determine whether the user can view any call activities.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Super admins can view all call activities; others need permission within an organization
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view call activities', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the call activity.
     *
     * @param User $user
     * @param CallActivity $callActivity
     * @return bool
     */
    public function view(User $user, CallActivity $callActivity): bool
    {
        return $this->isOrgUserWithPermission($user, 'view call activities', $callActivity->call->organization_id);
    }

    /**
     * Determine whether the user can create call activities.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Super admins can create call activities; others need permission within an organization
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create call activities', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the call activity.
     *
     * @param User $user
     * @param CallActivity $callActivity
     * @return bool
     */
    public function update(User $user, CallActivity $callActivity): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit call activities', $callActivity->call->organization_id);
    }

    /**
     * Determine whether the user can delete the call activity.
     *
     * @param User $user
     * @param CallActivity $callActivity
     * @return bool
     */
    public function delete(User $user, CallActivity $callActivity): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit call activities', $callActivity->call->organization_id);
    }
}
