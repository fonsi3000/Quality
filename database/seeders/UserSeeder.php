<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole('admin');

        // Agent User
        $agentUser = User::create([
            'name' => 'Agent User',
            'email' => 'agent@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $agentUser->assignRole('agent');

        // Normal User
        $normalUser = User::create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $normalUser->assignRole('user');

        // Brenda - Líder Calidad Medellín
        $brendaUser = User::create([
            'name' => 'Brenda Anaya Mora',
            'email' => 'lider.calidad@espumasmedellin.com.co',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $brendaUser->assignRole('admin');

        // Leydis - Líder Calidad Litoral
        $leydisUser = User::create([
            'name' => 'Leydis Carolina Madera',
            'email' => 'lider.calidad@espumadosdellitoral.com.co',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);
        $leydisUser->assignRole('admin');
    }
}