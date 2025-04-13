<?php

namespace App\Models;

use App\Enums\UserType;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public static function USER_TYPES(): array
    {
        return array_map(fn($status) => $status->value, UserType::cases());
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $fillable = [
        'name', 'email', 'type', 'password'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];
}
