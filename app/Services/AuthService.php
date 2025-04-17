<?php

namespace App\Services;

use App\Enums\SystemRole;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Cache the super admin status for the request to avoid multiple checks.
     *
     * @var array
     */
    protected static $superAdminCache = [];

    /**
     * Attempt to log in a user with the provided credentials.
     *
     * @param array $credentials
     * @return string
     * @throws ValidationException
     */
    public function attemptLogin(array $credentials): string
    {
        $authCredentials = collect($credentials)->only(['email', 'password'])->toArray();

        if (!auth()->attempt($authCredentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return auth()->user()->createToken('auth_token')->plainTextToken;
    }

    /**
     * Log out the authenticated user.
     *
     * @return void
     */
    public function logout(): void
    {
        auth()->user()->currentAccessToken()?->delete();
    }

    /**
     * Check if the user is a super admin with a role not tied to any organization.
     *
     * @param User $user
     * @return bool
     */
    public function isSuperAdmin(User $user): bool
    {
        // Use request-scoped cache to avoid multiple checks within the same request
        $cacheKey = "super_admin:{$user->id}";

        if (!isset(static::$superAdminCache[$cacheKey])) {
            // Check the persistent cache first
            static::$superAdminCache[$cacheKey] = Cache::remember($cacheKey, now()->addHours(24), function () use ($user) {
                return $user->roles()->where('name', SystemRole::SUPER_ADMIN->value)
                            ->whereNull('roles.organization_id')
                            ->exists();
            });
        }

        return static::$superAdminCache[$cacheKey];
    }
}
