<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Website;

class WebsitePolicy extends BasePolicy
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
     * Determine whether the user can view any websites.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view websites', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the website.
     *
     * @param User $user
     * @param Website $website
     * @return bool
     */
    public function view(User $user, Website $website): bool
    {
        return $this->isOrgUserWithPermission($user, 'view websites', $website->organization_id);
    }

    /**
     * Determine whether the user can create websites.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create websites', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the website.
     *
     * @param User $user
     * @param Website $website
     * @return bool
     */
    public function update(User $user, Website $website): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit websites', $website->organization_id);
    }

    /**
     * Determine whether the user can delete the website.
     *
     * @param User $user
     * @param Website $website
     * @return bool
     */
    public function delete(User $user, Website $website): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit websites', $website->organization_id);
    }

    /**
     * Determine whether the user can delete any websites.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit websites', $organizationId) : false;
    }
}
