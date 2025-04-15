<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserType;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->type === UserType::SUPER_ADMIN->value;
    }

    public function view(User $user, User $model): bool
    {
        return $user->type === UserType::SUPER_ADMIN->value;
    }

    public function create(User $user): bool
    {
        return $user->type === UserType::SUPER_ADMIN->value;
    }

    public function update(User $user, User $model): bool
    {
        return $user->type === UserType::SUPER_ADMIN->value;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->type === UserType::SUPER_ADMIN->value;
    }
}
