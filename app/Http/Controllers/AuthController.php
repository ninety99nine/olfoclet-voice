<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\ShowAuthUserRequest;

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
}
