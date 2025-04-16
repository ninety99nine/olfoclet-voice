<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'sanctum']);
        $agentRole = Role::create(['name' => 'agent', 'guard_name' => 'sanctum']);

        // Create permissions
        $permissions = [
            'view users',
            'create user',
            'edit user',
            'delete user'
        ];

        foreach ($permissions as $permissionName) {
            $permission = Permission::create(['name' => $permissionName, 'guard_name' => 'sanctum']);
            $adminRole->givePermissionTo($permission);
        }

        // Assign fewer permissions to agents
        $agentRole->givePermissionTo('view users');
    }
}
