<?php

namespace App\Policies;

use App\Models\User;
use App\Services\AuthService;

class BasePolicy
{
    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * Create a new policy instance.
     *
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Check if the user has the specified permission within the organization.
     *
     * @param User $user
     * @param string $ability
     * @param string|null $organizationId
     * @return bool
     */
    protected function isOrgUserWithPermission(User $user, string $ability, ?string $organizationId): bool
    {
        // Middleware ensures organization_id is valid and user is a member (for non-super admins)
        if (!$organizationId) {
            return false;
        }

        return $user->hasPermissionTo($ability, $organizationId);
    }
}
