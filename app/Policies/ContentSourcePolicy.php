<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ContentSource;

class ContentSourcePolicy extends BasePolicy
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
     * Determine whether the user can view any content sources.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view content sources', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the content source.
     *
     * @param User $user
     * @param ContentSource $contentSource
     * @return bool
     */
    public function view(User $user, ContentSource $contentSource): bool
    {
        return $this->isOrgUserWithPermission($user, 'view content sources', $contentSource->knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can create content sources.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create content sources', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the content source.
     *
     * @param User $user
     * @param ContentSource $contentSource
     * @return bool
     */
    public function update(User $user, ContentSource $contentSource): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit content sources', $contentSource->knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can delete the content source.
     *
     * @param User $user
     * @param ContentSource $contentSource
     * @return bool
     */
    public function delete(User $user, ContentSource $contentSource): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit content sources', $contentSource->knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can delete any content sources.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit content sources', $organizationId) : false;
    }
}
