<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Call;
use App\Models\CallActivity;

class CallActivityPolicy extends BasePolicy
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
     * Determine whether the user can view any call activities.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Delegate to CallPolicy's viewAny, as call activities are tied to calls
        return $user->can('viewAny', Call::class);
    }

    /**
     * Determine whether the user can view the call activity.
     *
     * @param User $user
     * @param CallActivity $callActivity
     * @return bool
     */
    public function view(User $user, CallActivity $callActivity): bool
    {
        // Delegate to CallPolicy's view, checking the associated call
        return $user->can('view', $callActivity->call);
    }
}
