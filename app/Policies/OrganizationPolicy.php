<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\SystemRole;
use App\Models\Organization;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy extends BasePolicy
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

    public function view(User $user, Organization $organization): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Organization $organization): bool
    {
        return false;
    }

    public function delete(User $user, Organization $organization): bool
    {
        return false;
    }
}
