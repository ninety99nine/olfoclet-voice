<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ConversationThread;

class ConversationThreadPolicy extends BasePolicy
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
     * Determine whether the user can view any conversation threads.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Since conversation threads are user-specific, any authenticated user can view their own threads
        return true;
    }

    /**
     * Determine whether the user can view the conversation thread.
     *
     * @param User $user
     * @param ConversationThread $conversationThread
     * @return bool
     */
    public function view(User $user, ConversationThread $conversationThread): bool
    {
        // Ensure the conversation thread belongs to the user
        return $conversationThread->user_id === $user->id;
    }

    /**
     * Determine whether the user can create conversation threads.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the conversation thread.
     *
     * @param User $user
     * @param ConversationThread $conversationThread
     * @return bool
     */
    public function update(User $user, ConversationThread $conversationThread): bool
    {
        return $conversationThread->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the conversation thread.
     *
     * @param User $user
     * @param ConversationThread $conversationThread
     * @return bool
     */
    public function delete(User $user, ConversationThread $conversationThread): bool
    {
        return $conversationThread->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete any conversation threads.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return true;
    }
}
