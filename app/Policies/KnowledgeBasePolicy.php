<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KnowledgeBase;

class KnowledgeBasePolicy extends BasePolicy
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
     * Determine whether the user can view any knowledge bases.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view knowledge bases', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the knowledge base.
     *
     * @param User $user
     * @param KnowledgeBase $knowledgeBase
     * @return bool
     */
    public function view(User $user, KnowledgeBase $knowledgeBase): bool
    {
        return $this->isOrgUserWithPermission($user, 'view knowledge bases', $knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can create knowledge bases.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create knowledge bases', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the knowledge base.
     *
     * @param User $user
     * @param KnowledgeBase $knowledgeBase
     * @return bool
     */
    public function update(User $user, KnowledgeBase $knowledgeBase): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit knowledge bases', $knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can delete the knowledge base.
     *
     * @param User $user
     * @param KnowledgeBase $knowledgeBase
     * @return bool
     */
    public function delete(User $user, KnowledgeBase $knowledgeBase): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit knowledge bases', $knowledgeBase->organization_id);
    }

    /**
     * Determine whether the user can delete any knowledge bases.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit knowledge bases', $organizationId) : false;
    }
}
