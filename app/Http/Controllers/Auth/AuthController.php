<?php

namespace App\Http\Controllers\Auth;

use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $token = app(AuthService::class)->attemptLogin($credentials);

        return response()->json([
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }
}
