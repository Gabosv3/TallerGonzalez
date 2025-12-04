<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposAceitesSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tipos_aceites')->delete(); // <-- NO truncate()
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $rows = [
            ['id' => 3, 'nombre' => 'Semi-Sintético',          'clave' => 'ACE-001', 'color' => '#570721', 'orden' => 1, 'activo' => 1],
            ['id' => 4, 'nombre' => 'Totalmente Sintetico',    'clave' => 'ACE-002', 'color' => '#abacb0', 'orden' => 2, 'activo' => 1],
            ['id' => 5, 'nombre' => 'Antifreeze Coolant',      'clave' => 'ANT-001', 'color' => '#0e295e', 'orden' => 3, 'activo' => 1],
            ['id' => 6, 'nombre' => 'Ultra Sintetico',         'clave' => 'ACE-003', 'color' => '#6B7280', 'orden' => 4, 'activo' => 1],
            ['id' => 7, 'nombre' => 'Antifreeze+Coolant',      'clave' => 'ANT-002', 'color' => '#6B7280', 'orden' => 5, 'activo' => 1],
            ['id' => 8, 'nombre' => 'Full Sintetico',          'clave' => 'ACE-004', 'color' => '#6B7280', 'orden' => 6, 'activo' => 1],
        ];

        // eliminar tipos existentes por ID para evitar conflicto
        DB::table('tipos_aceites')->whereIn('id', array_column($rows, 'id'))->delete();

        // insertar respetando ID
        DB::table('tipos_aceites')->upsert(
            $rows,
            ['id'], // clave única
            ['nombre', 'clave', 'color', 'orden', 'activo']
        );

        $this->command->info('✅ Tipos de aceites sincronizados con IDs correctos.');
    }
}
