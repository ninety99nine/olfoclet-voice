<?php

namespace App\Policies;

use App\Enums\SystemRole;

class BasePolicy
{
    protected function isSuperAdmin($user): bool
    {
        return $user->roles()->where('name', SystemRole::SUPER_ADMIN->value)
                    ->whereNull('roles.organization_id')
                    ->exists();
    }
}
