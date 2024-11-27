<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $agent = Role::create(['name' => 'agent']);
        $user = Role::create(['name' => 'user']);

        // Create permissions
        $adminagent = Permission::create(['name' => 'admin.agent']);
        $userview = Permission::create(['name' => 'user']);
        $viewAgent = Permission::create(['name' => 'view.agent']);
        $adminOnly = Permission::create(['name' => 'admin.only']); // Nuevo permiso solo para admin

        // Assign permissions to roles
        $admin->givePermissionTo([$adminagent, $adminOnly]); // Admin tiene sus permisos exclusivos
        $agent->givePermissionTo([$adminagent, $viewAgent]); // Agente mantiene sus permisos
        $user->givePermissionTo($userview);
    }
}