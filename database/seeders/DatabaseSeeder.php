<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Primero llamamos al UserSeeder
        $this->call([
            UserSeeder::class,
        ]);

        // Luego creamos el usuario de prueba específico
        

        // Opcionalmente, puedes crear más usuarios de prueba
        // User::factory(10)->create();
    }
}