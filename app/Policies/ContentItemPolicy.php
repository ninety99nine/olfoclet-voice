<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ContentItem;

class ContentItemPolicy extends BasePolicy
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
     * Determine whether the user can view any content items.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view content items', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the content item.
     *
     * @param User $user
     * @param ContentItem $contentItem
     * @return bool
     */
    public function view(User $user, ContentItem $contentItem): bool
    {
        return $this->isOrgUserWithPermission($user, 'view content items', $contentItem->knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can create content items.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create content items', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the content item.
     *
     * @param User $user
     * @param ContentItem $contentItem
     * @return bool
     */
    public function update(User $user, ContentItem $contentItem): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit content items', $contentItem->knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can delete the content item.
     *
     * @param User $user
     * @param ContentItem $contentItem
     * @return bool
     */
    public function delete(User $user, ContentItem $contentItem): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit content items', $contentItem->knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can delete any content items.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit content items', $organizationId) : false;
    }
}
