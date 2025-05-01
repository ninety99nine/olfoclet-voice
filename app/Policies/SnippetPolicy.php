<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Snippet;

class SnippetPolicy extends BasePolicy
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
     * Determine whether the user can view any snippets.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view snippets', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the snippet.
     *
     * @param User $user
     * @param Snippet $snippet
     * @return bool
     */
    public function view(User $user, Snippet $snippet): bool
    {
        return $this->isOrgUserWithPermission($user, 'view snippets', $snippet->organization_id);
    }

    /**
     * Determine whether the user can create snippets.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create snippets', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the snippet.
     *
     * @param User $user
     * @param Snippet $snippet
     * @return bool
     */
    public function update(User $user, Snippet $snippet): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit snippets', $snippet->organization_id);
    }

    /**
     * Determine whether the user can delete the snippet.
     *
     * @param User $user
     * @param Snippet $snippet
     * @return bool
     */
    public function delete(User $user, Snippet $snippet): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit snippets', $snippet->organization_id);
    }

    /**
     * Determine whether the user can delete any snippets.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit snippets', $organizationId) : false;
    }
}
