<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AceiteSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['producto_id'=>2,'marca_id'=>2,'modelo'=>'MAX LIFE','viscosidad'=>'10W-30','tipo_aceite_id'=>3,'capacidad_ml'=>4730.00,'unidad_medida'=>'ml','presentacion'=>'Pachon','stock_disponible'=>0,'stock_minimo'=>5,'activo'=>1],
            ['producto_id'=>3,'marca_id'=>2,'modelo'=>'MAX LIFE','viscosidad'=>'20W-50','tipo_aceite_id'=>3,'capacidad_ml'=>4730.00,'unidad_medida'=>'ml','presentacion'=>'Pachon','stock_disponible'=>0,'stock_minimo'=>5,'activo'=>1],
            ['producto_id'=>4,'marca_id'=>2,'modelo'=>'ADVANCED','viscosidad'=>'5W-30','tipo_aceite_id'=>4,'capacidad_ml'=>4730.00,'unidad_medida'=>'ml','presentacion'=>'Pachon','stock_disponible'=>0,'stock_minimo'=>5,'stock_maximo'=>10,'activo'=>1],
            ['producto_id'=>5,'marca_id'=>2,'modelo'=>'ADVANCED','viscosidad'=>'0W-20','tipo_aceite_id'=>4,'capacidad_ml'=>4730.00,'unidad_medida'=>'ml','presentacion'=>'Pachon','stock_disponible'=>5,'stock_minimo'=>0,'stock_maximo'=>0,'activo'=>1],
            ['producto_id'=>6,'marca_id'=>2,'modelo'=>'ADVANCED','viscosidad'=>'10W-30','tipo_aceite_id'=>4,'capacidad_ml'=>4731.00,'unidad_medida'=>'ml','presentacion'=>'Pachon','stock_disponible'=>3,'stock_minimo'=>0,'activo'=>1],
            ['producto_id'=>7,'marca_id'=>2,'modelo'=>'ADVANCED','viscosidad'=>'5W-20','tipo_aceite_id'=>4,'capacidad_ml'=>4730.00,'unidad_medida'=>'ml','presentacion'=>'Pachon','stock_disponible'=>2,'activo'=>1],
            ['producto_id'=>8,'marca_id'=>2,'modelo'=>'ZEREX','viscosidad'=>'50/50','tipo_aceite_id'=>5,'capacidad_ml'=>3780.00,'unidad_medida'=>'ml','presentacion'=>'Galon','compatibilidad'=>'HONDA','stock_disponible'=>12,'stock_minimo'=>5,'stock_maximo'=>10,'activo'=>1],
            ['producto_id'=>9,'marca_id'=>2,'modelo'=>'ZEREX','viscosidad'=>'50/50','tipo_aceite_id'=>5,'capacidad_ml'=>3781.00,'unidad_medida'=>'ml','presentacion'=>'Galon','compatibilidad'=>'Toyota','stock_disponible'=>11,'stock_minimo'=>5,'activo'=>1],
            ['producto_id'=>10,'marca_id'=>2,'modelo'=>'ZEREX','viscosidad'=>'50/50','tipo_aceite_id'=>5,'capacidad_ml'=>3782.00,'unidad_medida'=>'ml','presentacion'=>'Galon','compatibilidad'=>'FORD','stock_disponible'=>8,'stock_minimo'=>5,'stock_maximo'=>10,'activo'=>1],
            ['producto_id'=>11,'marca_id'=>2,'modelo'=>'Para todas las marcas','viscosidad'=>'33%','tipo_aceite_id'=>5,'capacidad_ml'=>3785.00,'unidad_medida'=>'ml','presentacion'=>'Galon','stock_disponible'=>0,'stock_minimo'=>5,'stock_maximo'=>10,'activo'=>1],
            ['producto_id'=>12,'marca_id'=>3,'modelo'=>'ULTRALUB','viscosidad'=>'5W-20','tipo_aceite_id'=>6,'capacidad_ml'=>3780.00,'unidad_medida'=>'ml','presentacion'=>'4 QT','stock_disponible'=>0,'stock_minimo'=>5,'stock_maximo'=>10,'activo'=>1],
            ['producto_id'=>13,'marca_id'=>3,'modelo'=>'ULTRALUB','viscosidad'=>'0W-20','tipo_aceite_id'=>6,'capacidad_ml'=>3780.00,'unidad_medida'=>'ml','presentacion'=>'4 QT','stock_disponible'=>0,'stock_minimo'=>5,'stock_maximo'=>10,'activo'=>1],
            ['producto_id'=>14,'marca_id'=>3,'modelo'=>'ULTRAPLUS','viscosidad'=>'50/50','tipo_aceite_id'=>7,'capacidad_ml'=>3785.00,'unidad_medida'=>'ml','presentacion'=>'Galon','stock_disponible'=>0,'stock_minimo'=>5,'stock_maximo'=>10,'activo'=>1],
            ['producto_id'=>15,'marca_id'=>2,'modelo'=>'ACEITE PARA TRANSMISIONES CONTINUAMENTE','viscosidad'=>'CVT','tipo_aceite_id'=>8,'capacidad_ml'=>946.00,'unidad_medida'=>'ml','presentacion'=>'1 QT','stock_disponible'=>22,'stock_minimo'=>5,'stock_maximo'=>10,'activo'=>1],
            ['producto_id'=>20,'marca_id'=>3,'modelo'=>'MULTI-VEHICLE','viscosidad'=>'ATF','tipo_aceite_id'=>8,'capacidad_ml'=>946.00,'unidad_medida'=>'ml','presentacion'=>'1 QT','stock_disponible'=>0,'stock_minimo'=>5,'stock_maximo'=>10,'activo'=>1],
            ['producto_id'=>21,'marca_id'=>3,'modelo'=>'API SP ILSAC GF-6A','viscosidad'=>'0W-20','tipo_aceite_id'=>8,'capacidad_ml'=>946.00,'unidad_medida'=>'ml','presentacion'=>'1 QT','stock_disponible'=>16,'activo'=>1],
            ['producto_id'=>22,'marca_id'=>3,'modelo'=>'ILSAC GF-6A','viscosidad'=>'10W-30','tipo_aceite_id'=>8,'capacidad_ml'=>946.00,'unidad_medida'=>'ml','presentacion'=>'1 QT','stock_disponible'=>4,'activo'=>1],
            ['producto_id'=>23,'marca_id'=>3,'modelo'=>'ILSAC GF-6A','viscosidad'=>'5W-20','tipo_aceite_id'=>8,'capacidad_ml'=>946.00,'unidad_medida'=>'ml','presentacion'=>'1 QT','stock_disponible'=>12,'activo'=>1],
            ['producto_id'=>24,'marca_id'=>2,'modelo'=>'MULTI-VEHICLE ATF ','viscosidad'=>'MULTI-VEHICLE ATF ','tipo_aceite_id'=>8,'capacidad_ml'=>3780.00,'unidad_medida'=>'ml','presentacion'=>'Pachon','stock_disponible'=>0,'stock_minimo'=>5,'stock_maximo'=>10,'activo'=>1],
            ['producto_id'=>30,'marca_id'=>3,'modelo'=>'CVT','viscosidad'=>'CVT','tipo_aceite_id'=>8,'capacidad_ml'=>947.00,'unidad_medida'=>'ml','presentacion'=>'1 QT','stock_disponible'=>1,'stock_minimo'=>0,'stock_maximo'=>0,'activo'=>1],
        ];

        // Antes de insertar, asegurarnos de que los `producto_id` referenciados existan.
        $productoIds = array_unique(array_filter(array_map(function ($r) {
            return $r['producto_id'] ?? null;
        }, $rows)));

        if (!empty($productoIds)) {
            $existingProducts = DB::table('productos')->whereIn('id', $productoIds)->pluck('id')->toArray();
            $missingProducts = array_diff($productoIds, $existingProducts);

            if (!empty($missingProducts)) {
                $placeholders = [];
                foreach ($missingProducts as $missingId) {
                    // intentar inferir marca_id a partir de la primera fila que referencia este producto
                    $row = null;
                    foreach ($rows as $r) {
                        if (isset($r['producto_id']) && $r['producto_id'] == $missingId) {
                            $row = $r;
                            break;
                        }
                    }

                    $placeholders[] = [
                        'id' => $missingId,
                        'codigo' => 'PLACEHOLDER-PROD-' . $missingId,
                        'nombre' => 'Producto placeholder ' . $missingId,
                        'descripcion' => 'Creado automáticamente por AceiteSeeder para cumplir FK',
                        'marca_id' => $row['marca_id'] ?? null,
                        'unidad_medida' => $row['unidad_medida'] ?? 'pza',
                        'tipo_producto_id' => $row['tipo_producto_id'] ?? 1,
                        'precio_compra' => 0.00,
                        'precio_venta' => 0.00,
                        'precio_minimo' => 0.00,
                        'stock_actual' => 0,
                        'stock_minimo' => 0,
                        'stock_maximo' => 0,
                        'activo' => 1,
                        'control_stock' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                try {
                    DB::table('productos')->insert($placeholders);
                    $this->command->info('ℹ️ Productos placeholder creados: ' . implode(', ', $missingProducts));
                } catch (\Exception $e) {
                    $this->command->warn('⚠️ No se pudieron crear todos los productos placeholder: ' . $e->getMessage());
                }
            }
        }

        // Normalizar filas: asegurar que todas las filas tengan las mismas claves
        $expected = [
            'producto_id','marca_id','modelo','viscosidad','tipo_aceite_id',
            'capacidad_ml','unidad_medida','presentacion','aplicaciones','compatibilidad',
            'stock_disponible','stock_minimo','stock_maximo','activo'
        ];

        $defaults = [
            'viscosidad' => null,
            'tipo_aceite_id' => null,
            'capacidad_ml' => 0,
            'unidad_medida' => null,
            'presentacion' => null,
            'aplicaciones' => null,
            'compatibilidad' => null,
            'stock_disponible' => 0,
            'stock_minimo' => 0,
            'stock_maximo' => 0,
            'activo' => 1,
        ];

        $prepared = array_map(function ($r) use ($expected, $defaults) {
            foreach ($expected as $col) {
                if (!array_key_exists($col, $r)) {
                    $r[$col] = $defaults[$col] ?? null;
                }
            }

            // Asegurar tipos numéricos
            $r['capacidad_ml'] = isset($r['capacidad_ml']) ? (float) $r['capacidad_ml'] : (float) $defaults['capacidad_ml'];
            $r['stock_disponible'] = isset($r['stock_disponible']) ? (int) $r['stock_disponible'] : (int) $defaults['stock_disponible'];
            $r['stock_minimo'] = isset($r['stock_minimo']) ? (int) $r['stock_minimo'] : (int) $defaults['stock_minimo'];
            $r['stock_maximo'] = isset($r['stock_maximo']) ? (int) $r['stock_maximo'] : (int) $defaults['stock_maximo'];
            $r['tipo_aceite_id'] = isset($r['tipo_aceite_id']) ? (int) $r['tipo_aceite_id'] : null;
            $r['marca_id'] = isset($r['marca_id']) ? (int) $r['marca_id'] : null;
            $r['producto_id'] = isset($r['producto_id']) ? (int) $r['producto_id'] : null;

            return $r;
        }, $rows);

        // Upsert usando combinación producto_id+marca_id+modelo como clave compuesta
        DB::table('aceites')->upsert(
            $prepared,
            ['producto_id','marca_id','modelo'],
            ['viscosidad','tipo_aceite_id','capacidad_ml','unidad_medida','presentacion','aplicaciones','compatibilidad','stock_disponible','stock_minimo','stock_maximo','activo']
        );

        $this->command->info('✅ Aceites insertados/actualizados.');
    }
}
