<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Enums\SystemRole;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class DefaultSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create default user with generated UUID and hashed password
        $user = User::create([
            'id' => Str::uuid(),
            'name' => 'Julian Tabona',
            'email' => 'brandontabona@gmail.com',
            'password' => Hash::make('QWEasd'),     // 🔐 Use a secure password
        ]);

        // Create system-level super admin role (no organization_id)
        $role = Role::create([
            'name' => SystemRole::SUPER_ADMIN->value,
            'guard_name' => 'sanctum',
            'organization_id' => null
        ]);

        // Disable team context
        app(PermissionRegistrar::class)->setPermissionsTeamId(null);

        // Assign role to super admin
        $user->assignRole($role);

        // Clear cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
