<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;

class DepartmentPolicy extends BasePolicy
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
     * Determine whether the user can view any departments.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view departments', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the department.
     *
     * @param User $user
     * @param Department $department
     * @return bool
     */
    public function view(User $user, Department $department): bool
    {
        return $this->isOrgUserWithPermission($user, 'view departments', $department->organization_id);
    }

    /**
     * Determine whether the user can create departments.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create departments', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the department.
     *
     * @param User $user
     * @param Department $department
     * @return bool
     */
    public function update(User $user, Department $department): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit departments', $department->organization_id);
    }

    /**
     * Determine whether the user can delete the department.
     *
     * @param User $user
     * @param Department $department
     * @return bool
     */
    public function delete(User $user, Department $department): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit departments', $department->organization_id);
    }

    /**
     * Determine whether the user can delete any departments.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit departments', $organizationId) : false;
    }
}
