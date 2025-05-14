<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Interaction;

class InteractionPolicy extends BasePolicy
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
     * Determine whether the user can view any interactions.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view interactions', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the interaction.
     *
     * @param User $user
     * @param Interaction $interaction
     * @return bool
     */
    public function view(User $user, Interaction $interaction): bool
    {
        return $this->isOrgUserWithPermission($user, 'view interactions', $interaction->organization_id);
    }

    /**
     * Determine whether the user can create interactions.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create interactions', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the interaction.
     *
     * @param User $user
     * @param Interaction $interaction
     * @return bool
     */
    public function update(User $user, Interaction $interaction): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit interactions', $interaction->organization_id);
    }

    /**
     * Determine whether the user can delete the interaction.
     *
     * @param User $user
     * @param Interaction $interaction
     * @return bool
     */
    public function delete(User $user, Interaction $interaction): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit interactions', $interaction->organization_id);
    }

    /**
     * Determine whether the user can delete any interactions.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit interactions', $organizationId) : false;
    }
}
