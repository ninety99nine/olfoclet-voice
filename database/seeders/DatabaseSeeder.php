<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 🔐 Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 🔄 Truncate all related tables
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('organization_user')->truncate();

        DB::table('personal_access_tokens')->truncate();
        DB::table('sessions')->truncate();
        DB::table('password_reset_tokens')->truncate();

        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('organizations')->truncate();
        DB::table('users')->truncate();

        // ✅ Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Seed base records
        $this->call(DefaultSuperAdminSeeder::class);
        //$this->call(RolesAndPermissionsSeeder::class);
    }
}
