<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Resources\UserResource;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends BaseController
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $token = app(AuthService::class)->attemptLogin($credentials);

        return $this->prepareOutput([
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }

    public function logout(Request $request)
    {
        app(AuthService::class)->logout();

        return $this->prepareOutput([
            'message' => 'Logged out successfully'
        ]);
    }

    public function showAuthUser(Request $request)
    {
        return new UserResource($request->user());
    }
}
