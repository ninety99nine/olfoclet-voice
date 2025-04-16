<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\SystemRole;

class UserPolicy extends BasePolicy
{
    /**
     * Grant all permissions to super admins who have roles not tied to any organization.
     */
    public function before(User $user, string $ability): bool|null
    {
        return $this->isSuperAdmin($user);
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, User $model): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, User $model): bool
    {
        return false;
    }

    public function delete(User $user, User $model): bool
    {
        return false;
    }
}
