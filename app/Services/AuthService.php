<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function attemptLogin(array $credentials): string
    {
        $authCredentials = collect($credentials)->only(['email', 'password'])->toArray();

        if (!Auth::attempt($authCredentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        return Auth::user()->createToken('auth_token')->plainTextToken;
    }
}

