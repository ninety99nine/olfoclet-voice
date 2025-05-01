<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Copilot;

class CopilotPolicy extends BasePolicy
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
     * Determine whether the user can view any copilots.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view copilots', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the copilot.
     *
     * @param User $user
     * @param Copilot $copilot
     * @return bool
     */
    public function view(User $user, Copilot $copilot): bool
    {
        return $this->isOrgUserWithPermission($user, 'view copilots', $copilot->organization_id) && $copilot->users->contains($user->id);
    }

    /**
     * Determine whether the user can create copilots.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create copilots', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the copilot.
     *
     * @param User $user
     * @param Copilot $copilot
     * @return bool
     */
    public function update(User $user, Copilot $copilot): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit copilots', $copilot->organization_id);
    }

    /**
     * Determine whether the user can delete the copilot.
     *
     * @param User $user
     * @param Copilot $copilot
     * @return bool
     */
    public function delete(User $user, Copilot $copilot): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit copilots', $copilot->organization_id);
    }

    /**
     * Determine whether the user can delete any copilots.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit copilots', $organizationId) : false;
    }
}
