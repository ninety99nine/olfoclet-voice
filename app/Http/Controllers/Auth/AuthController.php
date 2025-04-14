<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
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

    public function logout(Request $request)
    {
        app(AuthService::class)->logout();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function showAuthUser(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}
