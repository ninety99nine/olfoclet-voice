<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use App\Enums\SystemRole;
use Illuminate\Auth\Access\Response;

class RolePolicy extends BasePolicy
{
    /**
     * Grant all permissions to super admins who have roles not tied to any organization.
     */
    public function before(User $user, string $ability): bool|null
    {
        if(!in_array($ability, ['update', 'delete'])) {
            return $this->isSuperAdmin($user);
        }
        return null;
    }

    protected function isOrgUserWithPermission(User $user, string $ability, ?Role $role = null): bool
    {
        $organizationId = $role?->organization_id ?? request('organization_id');

        if (! $organizationId) return false;

        return $user->organizations()->where('organization_id', $organizationId)->exists()
            && $user->hasPermissionTo($ability, $organizationId);
    }

    public function viewAny(User $user): bool
    {
        return $this->isOrgUserWithPermission($user, 'view roles');
    }

    public function view(User $user, Role $role): bool
    {
        return $this->isOrgUserWithPermission($user, 'view roles', $role);
    }

    public function create(User $user): bool
    {
        return $this->isOrgUserWithPermission($user, 'create roles');
    }

    public function update(User $user, Role $role): bool
    {
        return $role->organization_id && ($this->isSuperAdmin($user) || $this->isOrgUserWithPermission($user, 'edit roles', $role));
    }

    public function delete(User $user, Role $role): bool
    {
        return $role->organization_id && ($this->isSuperAdmin($user) || $this->isOrgUserWithPermission($user, 'edit roles', $role));
    }
}
