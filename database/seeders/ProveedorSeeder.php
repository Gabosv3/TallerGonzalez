<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'codigo' => 'PRO-001',
                'nombre' => 'ECONOPARTS',
                'contacto' => 'www.econoparts.com.sv',
                'telefono' => '25058000',
                'email' => 'Serviciosalcliente@econoparts.com',
                'direccion' => 'CASA MATRIZ — Carr. al Puerto de La Libertad, KM 9 1/2, Edif. Econo-Parts, Santa Tecla, La Libertad',
                'ciudad' => 'San salvador',
                'pais' => 'El Salvador',
                'rfc' => '06142302770010',
                'activo' => 1,
            ],
        ];

        // Upsert por `codigo` para evitar duplicados
        DB::table('proveedores')->upsert(
            $rows,
            ['codigo'],
            ['nombre','contacto','telefono','email','direccion','ciudad','pais','rfc','notas','activo']
        );

        $this->command->info('✅ Proveedores insertados/actualizados.');
    }
}
