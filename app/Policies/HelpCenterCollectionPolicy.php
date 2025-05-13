<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HelpCenterCollection;

class HelpCenterCollectionPolicy extends BasePolicy
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
     * Determine whether the user can view any help center collections.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view help center collections', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the help center collection.
     *
     * @param User $user
     * @param HelpCenterCollection $helpCenterCollection
     * @return bool
     */
    public function view(User $user, HelpCenterCollection $helpCenterCollection): bool
    {
        return $this->isOrgUserWithPermission($user, 'view help center collections', $helpCenterCollection->knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can create help center collections.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create help center collections', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the help center collection.
     *
     * @param User $user
     * @param HelpCenterCollection $helpCenterCollection
     * @return bool
     */
    public function update(User $user, HelpCenterCollection $helpCenterCollection): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit help center collections', $helpCenterCollection->knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can delete the help center collection.
     *
     * @param User $user
     * @param HelpCenterCollection $helpCenterCollection
     * @return bool
     */
    public function delete(User $user, HelpCenterCollection $helpCenterCollection): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit help center collections', $helpCenterCollection->knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can delete any help center collections.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit help center collections', $organizationId) : false;
    }
}
