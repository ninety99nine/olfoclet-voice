<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserType;
use App\Models\Organization;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    public function viewAny(User $user)
    {
        return $user->type === UserType::SUPER_ADMIN->value;
    }

    public function view(User $user, Organization $organization)
    {
        return $user->type === UserType::SUPER_ADMIN->value;
    }

    public function create(User $user)
    {
        return $user->type === UserType::SUPER_ADMIN->value;
    }

    public function update(User $user, Organization $organization)
    {
        return $user->type === UserType::SUPER_ADMIN->value;
    }

    public function delete(User $user, Organization $organization)
    {
        return $user->type === UserType::SUPER_ADMIN->value;
    }

}
