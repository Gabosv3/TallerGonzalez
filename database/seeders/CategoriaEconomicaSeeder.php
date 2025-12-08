<?php

namespace Database\Seeders;

use App\Models\CategoriaEconomica;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CategoriaEconomicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Leer el archivo JSON
        $jsonPath = public_path('data/cat-19.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("El archivo {$jsonPath} no existe");
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (!isset($data['CAT-019'])) {
            $this->command->error("No se encontró la clave 'CAT-019' en el JSON");
            return;
        }

        $categorias = $data['CAT-019'];
        $total = count($categorias);
        
        $this->command->info("Iniciando carga de {$total} categorías económicas...");

        // Usar insert para mejor rendimiento con muchos registros
        $chunks = array_chunk($categorias, 100);
        
        foreach ($chunks as $chunk) {
            $records = array_map(function($categoria) {
                return [
                    'codigo' => $categoria['codigo'],
                    'descripcion' => $categoria['descripcion'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $chunk);

            CategoriaEconomica::insertOrIgnore($records);
            
            $this->command->line("Procesados " . count($records) . " registros...");
        }

        $this->command->info("✓ Categorías económicas cargadas exitosamente");
    }
}
