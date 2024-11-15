<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Es importante mantener este orden
        $this->call([
            RolesAndPermissionsSeeder::class, // Primero crear roles
            UserSeeder::class,                // Luego crear usuarios
        ]);
    }
}