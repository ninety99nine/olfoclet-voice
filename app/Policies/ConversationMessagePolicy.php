<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ConversationMessage;

class ConversationMessagePolicy extends BasePolicy
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
     * Determine whether the user can view any conversation messages.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the conversation message.
     *
     * @param User $user
     * @param ConversationMessage $conversationMessage
     * @return bool
     */
    public function view(User $user, ConversationMessage $conversationMessage): bool
    {
        return $conversationMessage->thread->user_id === $user->id;
    }

    /**
     * Determine whether the user can create conversation messages.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the conversation message.
     *
     * @param User $user
     * @param ConversationMessage $conversationMessage
     * @return bool
     */
    public function update(User $user, ConversationMessage $conversationMessage): bool
    {
        return $conversationMessage->thread->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the conversation message.
     *
     * @param User $user
     * @param ConversationMessage $conversationMessage
     * @return bool
     */
    public function delete(User $user, ConversationMessage $conversationMessage): bool
    {
        return $conversationMessage->thread->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete any conversation messages.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return true;
    }
}
