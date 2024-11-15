<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $agent = Role::create(['name' => 'agent']);
        $user = Role::create(['name' => 'user']);

        // Create permission for organization access
        $adminagent = Permission::create(['name' => 'admin.agent']);
        $userview = Permission::create(['name' => 'user']);

        // Assign permission to admin and agent roles
        $admin->givePermissionTo($adminagent);
        $agent->givePermissionTo($adminagent);
        $user->givePermissionTo($userview);

    }
}