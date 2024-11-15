<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario Admin
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole('admin');

        // Crear usuario Agente
        $agentUser = User::create([
            'name' => 'Agent User',
            'email' => 'agent@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $agentUser->assignRole('agent');

        // Crear usuario normal
        $normalUser = User::create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $normalUser->assignRole('user');

        // Opcional: crear usuarios adicionales con rol 'user'
        // User::factory(10)->create()->each(function ($user) {
        //     $user->assignRole('user');
        // });
    }
}