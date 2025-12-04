<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders principales (excluyendo roles, permissions y general_settings)
        $this->call([
            MarcaSeeder::class,
            TiposAceitesSeeder::class,
            TipoProductoSeeder::class,
            ProveedorSeeder::class,
            ProductoSeeder::class,
            AceiteSeeder::class,
            UserSeeder::class,
        ]);
    }
}
