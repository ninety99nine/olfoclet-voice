<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\ValidateTokenRequest;
use App\Http\Requests\Auth\SetupPasswordRequest;
use App\Http\Requests\Auth\ShowAuthUserRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdateAccountRequest;

class AuthController extends BaseController
{
    /**
     * @var AuthService
     */
    protected $service;

    /**
     * AuthController constructor.
     *
     * @param AuthService $service
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * Perform user login.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $token = $this->service->attemptLogin($credentials);

        return $this->prepareOutput([
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }

    /**
     * Validate a password setup token.
     *
     * @param ValidateTokenRequest $request
     * @return JsonResponse
     */
    public function validateToken(ValidateTokenRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->service->validateToken($data['email'], $data['token']);

        return $this->prepareOutput([
            'message' => 'Token is valid.'
        ]);
    }

    /**
     * Request a password reset link.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->service->sendPasswordResetLink($data['email']);

        return $this->prepareOutput([
            'message' => 'A password reset link has been sent to your email.'
        ]);
    }

    /**
     * Reset user password using a token.
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->service->resetPassword($data['email'], $data['token'], $data['password']);

        return $this->prepareOutput([
            'message' => 'Password reset successfully. Please log in with your new password.'
        ]);
    }

    /**
     * Set up user password using a token.
     *
     * @param SetupPasswordRequest $request
     * @return JsonResponse
     */
    public function setupPassword(SetupPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $token = $this->service->setupPassword($data['email'], $data['token'], $data['password']);

        return $this->prepareOutput([
            'token' => $token,
            'message' => 'Password set successfully. You are now logged in.'
        ]);
    }

    /**
     * Perform user logout.
     *
     * @param LogoutRequest $request
     * @return JsonResponse
     */
    public function logout(LogoutRequest $request): JsonResponse
    {
        $this->service->logout();

        return $this->prepareOutput([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Show authenticated user.
     *
     * @param ShowAuthUserRequest $request
     * @return UserResource
     */
    public function showAuthUser(ShowAuthUserRequest $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Update authenticated user's account details.
     *
     * @param UpdateAccountRequest $request
     * @return JsonResponse
     */
    public function updateAccount(UpdateAccountRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $this->service->updateAccount($user, $data);

        return $this->prepareOutput([
            'message' => 'Account updated successfully.'
        ]);
    }
}
