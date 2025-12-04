<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcaSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            ['id' => 2, 'nombre' => 'Valvoline', 'logo' => 'marcas-logos/01K9AESN4YYZX1KR7XVTDEAXJB.png', 'pais_origen' => 'Mexico', 'descripcion' => 'empresa estadounidense que fabrica y comercializa lubricantes para motores y ofrece servicios automotrices', 'activo' => 1, 'orden' => 1, 'created_at' => '2025-11-05 16:49:58', 'updated_at' => '2025-11-05 16:49:58'],
            ['id' => 3, 'nombre' => 'Ultra', 'logo' => null, 'pais_origen' => 'ESTADOS UNIDOS', 'descripcion' => null, 'activo' => 1, 'orden' => 2, 'created_at' => '2025-11-05 19:51:21', 'updated_at' => '2025-11-05 20:06:41'],
            ['id' => 4, 'nombre' => 'Lafa Racing', 'logo' => null, 'pais_origen' => 'ESTADOS UNIDOS', 'descripcion' => null, 'activo' => 1, 'orden' => 3, 'created_at' => '2025-11-05 20:36:22', 'updated_at' => '2025-11-05 20:36:22'],
            ['id' => 5, 'nombre' => 'Inizio Tuning', 'logo' => null, 'pais_origen' => 'ESTADOS UNIDOS', 'descripcion' => null, 'activo' => 1, 'orden' => 7, 'created_at' => '2025-11-05 20:40:23', 'updated_at' => '2025-11-05 20:40:23'],
            ['id' => 6, 'nombre' => 'Producto Chino', 'logo' => null, 'pais_origen' => 'CHINA', 'descripcion' => null, 'activo' => 1, 'orden' => 5, 'created_at' => '2025-11-05 20:51:04', 'updated_at' => '2025-11-05 20:51:04'],
            ['id' => 7, 'nombre' => 'Racingtec', 'logo' => null, 'pais_origen' => 'México', 'descripcion' => null, 'activo' => 1, 'orden' => 6, 'created_at' => '2025-11-07 16:43:20', 'updated_at' => '2025-11-07 16:43:20'],
            ['id' => 8, 'nombre' => 'Skt', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 16:48:46', 'updated_at' => '2025-11-07 16:48:46'],
            ['id' => 9, 'nombre' => 'Han', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 17:51:12', 'updated_at' => '2025-11-07 17:51:12'],
            ['id' => 10, 'nombre' => 'Fram', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 17:53:54', 'updated_at' => '2025-11-07 17:53:54'],
            ['id' => 11, 'nombre' => 'Seineca', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 17:56:46', 'updated_at' => '2025-11-07 17:56:46'],
            ['id' => 12, 'nombre' => 'Super Power Flow', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 18:11:25', 'updated_at' => '2025-11-07 18:11:25'],
            ['id' => 13, 'nombre' => 'Momo', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 19:55:08', 'updated_at' => '2025-11-07 19:55:08'],
            ['id' => 14, 'nombre' => 'Ckt', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 20:01:34', 'updated_at' => '2025-11-07 20:01:34'],
            ['id' => 15, 'nombre' => 'Nizio Tuning', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 20:05:24', 'updated_at' => '2025-11-07 20:05:24'],
            ['id' => 16, 'nombre' => 'Wix', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 20:07:23', 'updated_at' => '2025-11-07 20:07:23'],
            ['id' => 17, 'nombre' => 'Osk', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 20:14:40', 'updated_at' => '2025-11-07 20:14:40'],
            ['id' => 18, 'nombre' => 'Senfineco', 'logo' => null, 'pais_origen' => 'Germany', 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 20:37:32', 'updated_at' => '2025-11-07 20:37:32'],
            ['id' => 19, 'nombre' => 'Hkb Sports', 'logo' => null, 'pais_origen' => null, 'descripcion' => null, 'activo' => 1, 'orden' => 0, 'created_at' => '2025-11-07 21:44:43', 'updated_at' => '2025-11-07 21:44:43'],
        ];

        DB::table('marcas')->upsert(
            $marcas,
            ['id'], // clave única: respeta tus IDs
            ['nombre','logo','pais_origen','descripcion','activo','orden','created_at','updated_at']
        );

        $this->command->info('✅ Marcas sincronizadas sin eliminar registros.');
    }
}
