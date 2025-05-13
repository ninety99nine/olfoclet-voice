<?php

namespace App\Services;

use App\Enums\SystemRole;
use App\Models\User;
use App\Mail\PasswordResetLink;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !$user->password) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect or the account has not been set up.'],
            ]);
        }

        $authCredentials = collect($credentials)->only(['email', 'password'])->toArray();

        if (!auth()->attempt($authCredentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return auth()->user()->createToken('auth_token')->plainTextToken;
    }

    /**
     * Send a password reset link to the user.
     *
     * @param string $email
     * @return void
     * @throws ValidationException
     */
    public function sendPasswordResetLink(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The email address does not exist.'],
            ]);
        }

        if (!$user->password) {
            throw ValidationException::withMessages([
                'email' => ['The account has not been set up. Please use the setup link sent to your email.'],
            ]);
        }

        // Generate a temporary token for password reset
        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => hash('sha256', $token), 'created_at' => now()]
        );

        // Send email to the user with a reset link
        $resetUrl = config('app.frontend_url', 'http://localhost:8000') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        Mail::to($user->email)->send(new PasswordResetLink($user->email, $resetUrl));
    }

    /**
     * Reset a user's password using a token.
     *
     * @param string $email
     * @param string $token
     * @param string $password
     * @return void
     * @throws ValidationException
     */
    public function resetPassword(string $email, string $token, string $password): void
    {
        // Validate the token
        $tokenRecord = DB::table('password_reset_tokens')
                         ->where('email', $email)
                         ->where('token', hash('sha256', $token))
                         ->first();

        if (!$tokenRecord) {
            throw ValidationException::withMessages([
                'token' => ['The token is invalid.'],
            ]);
        }

        if ($tokenRecord->created_at < now()->subHours(24)) {
            throw ValidationException::withMessages([
                'token' => ['The token has expired.'],
            ]);
        }

        // Find the user
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The email address does not exist.'],
            ]);
        }

        // Set the new password
        $user->password = Hash::make($password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }

    /**
     * Validate a password setup token.
     *
     * @param string $email
     * @param string $token
     * @return void
     * @throws ValidationException
     */
    public function validateToken(string $email, string $token): void
    {
        // Validate the token
        $tokenRecord = DB::table('password_reset_tokens')
                         ->where('email', $email)
                         ->where('token', hash('sha256', $token))
                         ->first();

        if (!$tokenRecord) {
            throw ValidationException::withMessages([
                'token' => ['The token is invalid.'],
            ]);
        }

        if ($tokenRecord->created_at < now()->subHours(24)) {
            throw ValidationException::withMessages([
                'token' => ['The token has expired.'],
            ]);
        }

        // Find the user
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The email address does not exist.'],
            ]);
        }

        if ($user->password) {
            throw ValidationException::withMessages([
                'email' => ['The account has already been set up. Please log in or reset your password.'],
            ]);
        }
    }

    /**
     * Set up a user's password using a token.
     *
     * @param string $email
     * @param string $token
     * @param string $password
     * @return string
     * @throws ValidationException
     */
    public function setupPassword(string $email, string $token, string $password): string
    {
        // Validate the token
        $tokenRecord = DB::table('password_reset_tokens')
                         ->where('email', $email)
                         ->where('token', hash('sha256', $token))
                         ->first();

        if (!$tokenRecord) {
            throw ValidationException::withMessages([
                'token' => ['The token is invalid.'],
            ]);
        }

        if ($tokenRecord->created_at < now()->subHours(24)) {
            throw ValidationException::withMessages([
                'token' => ['The token has expired.'],
            ]);
        }

        // Find the user
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The email address does not exist.'],
            ]);
        }

        if ($user->password) {
            throw ValidationException::withMessages([
                'email' => ['The account has already been set up. Please log in or reset your password.'],
            ]);
        }

        // Set the password
        $user->password = Hash::make($password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Log the user in by generating a new token
        return $user->createToken('auth_token')->plainTextToken;
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

    /**
     * Update the authenticated user's account details.
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    public function updateAccount(User $user, array $data): void
    {
        $user->name = $data['name'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
    }
}
