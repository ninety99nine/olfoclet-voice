<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OmniChannelMessage;

class OmniChannelMessagePolicy extends BasePolicy
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
     * Determine whether the user can view any omni channel messages.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view omni channel messages', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the omni channel message.
     *
     * @param User $user
     * @param OmniChannelMessage $omniChannelMessage
     * @return bool
     */
    public function view(User $user, OmniChannelMessage $omniChannelMessage): bool
    {
        return $this->isOrgUserWithPermission($user, 'view omni channel messages', $omniChannelMessage->organization_id);
    }

    /**
     * Determine whether the user can create omni channel messages.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create omni channel messages', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the omni channel message.
     *
     * @param User $user
     * @param OmniChannelMessage $omniChannelMessage
     * @return bool
     */
    public function update(User $user, OmniChannelMessage $omniChannelMessage): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit omni channel messages', $omniChannelMessage->organization_id);
    }

    /**
     * Determine whether the user can delete the omni channel message.
     *
     * @param User $user
     * @param OmniChannelMessage $omniChannelMessage
     * @return bool
     */
    public function delete(User $user, OmniChannelMessage $omniChannelMessage): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit omni channel messages', $omniChannelMessage->organization_id);
    }

    /**
     * Determine whether the user can delete any omni channel messages.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit omni channel messages', $organizationId) : false;
    }
}
