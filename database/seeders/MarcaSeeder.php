<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            [
                'nombre' => 'Mobil',
                'pais_origen' => 'Estados Unidos',
                'descripcion' => 'Líder mundial en lubricantes de alto rendimiento',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'Castrol',
                'pais_origen' => 'Reino Unido',
                'descripcion' => 'Tecnología alemana en lubricantes avanzados',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'Shell',
                'pais_origen' => 'Países Bajos',
                'descripcion' => 'Innovación en lubricantes con tecnología Shell',
                'orden' => 3,
                'activo' => true,
            ],
            [
                'nombre' => 'Valvoline',
                'pais_origen' => 'Estados Unidos',
                'descripcion' => 'Protección superior para motores exigentes',
                'orden' => 4,
                'activo' => true,
            ],
            [
                'nombre' => 'Total',
                'pais_origen' => 'Francia',
                'descripcion' => 'Lubricantes de calidad francesa',
                'orden' => 5,
                'activo' => true,
            ],
            [
                'nombre' => 'Pennzoil',
                'pais_origen' => 'Estados Unidos',
                'descripcion' => 'Tecnología de limpieza avanzada',
                'orden' => 6,
                'activo' => true,
            ],
            [
                'nombre' => 'Quaker State',
                'pais_origen' => 'Estados Unidos',
                'descripcion' => 'Protección confiable para tu motor',
                'orden' => 7,
                'activo' => true,
            ],
            [
                'nombre' => 'Lubrax',
                'pais_origen' => 'Brasil',
                'descripcion' => 'Lubricantes brasileños de alta calidad',
                'orden' => 8,
                'activo' => true,
            ],
            [
                'nombre' => 'Elf',
                'pais_origen' => 'Francia',
                'descripcion' => 'Tecnología francesa en lubricantes',
                'orden' => 9,
                'activo' => true,
            ],
            [
                'nombre' => 'Repsol',
                'pais_origen' => 'España',
                'descripcion' => 'Lubricantes españoles de alto rendimiento',
                'orden' => 10,
                'activo' => true,
            ],
        ];

        foreach ($marcas as $marca) {
            Marca::create($marca);
        }

        $this->command->info('✅ 10 marcas de aceite creadas exitosamente.');
    }
}