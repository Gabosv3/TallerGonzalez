<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['nombre' => 'normal', 'descripcion' => 'Productos normales sin especificaciones técnicas', 'clase_modelo' => null, 'requiere_especificaciones' => 0, 'activo' => 1],
            ['nombre' => 'aceite', 'descripcion' => 'Aceites lubricantes y fluidos', 'clase_modelo' => 'App\\Models\\Aceite', 'requiere_especificaciones' => 1, 'activo' => 1],
        ];

        // Usar upsert para evitar error si los registros ya existen (evita UniqueConstraintViolation)
        DB::table('tipos_producto')->upsert(
            $rows,
            ['nombre'], // clave única para detectar duplicados
            ['descripcion', 'clase_modelo', 'requiere_especificaciones', 'activo'] // columnas a actualizar
        );

        $this->command->info('✅ Tipos de producto insertados/actualizados.');
    }
}
