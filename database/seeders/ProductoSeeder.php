<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('productos')->delete(); // <-- NO truncate()
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $rows = [
            // Aceites y lubricantes
            ['codigo' => 'ACE-001', 'nombre' => 'Aceite Carro Zerex', 'descripcion' => 'aceite', 'marca_id' => null, 'categoria_id' => null, 'unidad_medida' => 'l', 'tipo_producto_id' => 2, 'precio_compra' => 4.00, 'precio_venta' => 10.00, 'precio_minimo' => 9.00, 'stock_actual' => 7, 'stock_minimo' => 5, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '074130993136', 'nombre' => 'PACHON MAX LIFE SEMI SINTE.', 'descripcion' => 'Aceite Semi Sentetica ', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => 46.19, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => 12, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '074130024052', 'nombre' => 'PACHON MAX LIFE SEMI SINTE.', 'descripcion' => '', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => 48.46, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => 0, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '074130066724', 'nombre' => 'PACHON EZ ADVANCED SINTETICO', 'descripcion' => '', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => 63.61, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => 12, 'stock_maximo' => 24, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '074130048232', 'nombre' => 'PACHON EZ ADVANCED SINTETICO', 'descripcion' => '', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 5, 'stock_minimo' => 12, 'stock_maximo' => 24, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '074130059023', 'nombre' => 'PACHON EZ ADVANCED SINTETICO', 'descripcion' => '', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 3, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '074130048201', 'nombre' => 'PACHON EZ ADVANCED SINTETICO', 'descripcion' => '', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 2, 'stock_minimo' => 12, 'stock_maximo' => 24, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '074130017573', 'nombre' => 'ASIAN VEHICLE ZEREX', 'descripcion' => 'El anticongelante/refrigerante Zerex Asian Vehicle Blue ...', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 12, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '028882003889', 'nombre' => 'ASIAN VEHICLE ZEREX', 'descripcion' => 'El anticongelante/refrigerante Zerex Asian Vehicle Blue ...', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 11, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '028882501033', 'nombre' => 'ASIAN VEHICLE ZEREX', 'descripcion' => '', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 8, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '074130996632', 'nombre' => 'ANTICONGELANTE', 'descripcion' => '', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => 12, 'stock_maximo' => 24, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '7591871004066', 'nombre' => 'ULTRALUB', 'descripcion' => '', 'marca_id' => 3, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => 12, 'stock_maximo' => 24, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '0810050650113', 'nombre' => 'ULTRALUB', 'descripcion' => '', 'marca_id' => 3, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => 12, 'stock_maximo' => 24, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '810111112512', 'nombre' => 'ULTRAPLUS', 'descripcion' => '', 'marca_id' => 3, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => 5, 'stock_maximo' => 10, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '07413000003', 'nombre' => 'CVT', 'descripcion' => '', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 22, 'stock_minimo' => 5, 'stock_maximo' => 10, 'activo' => 1, 'control_stock' => 1],

            // Accesorios
            ['codigo' => '7453071501571', 'nombre' => 'DECORATIVOS DE AIRE', 'descripcion' => 'DECORATIVOS DE AIRE PEQUEÑO', 'marca_id' => 4, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 3, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU064966', 'nombre' => 'AIR DIVERTERS', 'descripcion' => '', 'marca_id' => 5, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 3, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU072805', 'nombre' => 'ANTENNA DECORATIVA', 'descripcion' => '', 'marca_id' => 5, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 1, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU052115', 'nombre' => 'MARCO PLACA REGULABLE', 'descripcion' => '', 'marca_id' => 6, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 1, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU052169', 'nombre' => 'MARCO PLACA HONDA', 'descripcion' => '', 'marca_id' => 6, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU05473', 'nombre' => 'MARCO PLACA TOYOTA', 'descripcion' => '', 'marca_id' => 6, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '7404003390466', 'nombre' => 'MARCO PLACA NISSAN', 'descripcion' => '', 'marca_id' => 6, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '7404003390404', 'nombre' => 'MARCO PLACA HYUNDAI', 'descripcion' => '', 'marca_id' => 6, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            // Más aceites y filtros
            ['codigo' => '810050653541', 'nombre' => 'ATF DEXRON MULTI-VEHICLE', 'descripcion' => '', 'marca_id' => 3, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '810050650120', 'nombre' => 'ULTRAPLUS', 'descripcion' => '', 'marca_id' => 3, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 16, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '0810050650304', 'nombre' => 'ULTRAPLUS', 'descripcion' => '', 'marca_id' => 3, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 4, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '0810050650182', 'nombre' => 'ULTRAPLUS', 'descripcion' => '', 'marca_id' => 3, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 12, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '074130056497', 'nombre' => 'MULTI-VEHICLE ATF', 'descripcion' => '', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'AUD00527535', 'nombre' => 'ALL-FLEET LEGACY', 'descripcion' => '', 'marca_id' => 2, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 0, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '810050653664', 'nombre' => 'ULTRAPLUS CVT', 'descripcion' => '', 'marca_id' => 3, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 2, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 1, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => 'ACCU066274', 'nombre' => 'Alfombras', 'descripcion' => '', 'marca_id' => 7, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 11, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU057515', 'nombre' => 'AUTO LAMP BUILDS', 'descripcion' => '', 'marca_id' => 8, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 10, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU057150', 'nombre' => 'AUTO LAMP BUILDS', 'descripcion' => '', 'marca_id' => 8, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 7, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU057872', 'nombre' => 'Bombilla cuad 9 power', 'descripcion' => '', 'marca_id' => 5, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 5, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU057869', 'nombre' => 'Bombilla Cuad. B/univ', 'descripcion' => '', 'marca_id' => 5, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 4, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            // Filtros
            ['codigo' => '*02907690', 'nombre' => 'Filtro de aceite', 'descripcion' => '', 'marca_id' => 9, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 3, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'PH8a', 'nombre' => 'Filtro de aceite ph8a', 'descripcion' => '', 'marca_id' => 10, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '6956216250651', 'nombre' => 'Filtro de aceite q 2169', 'descripcion' => '', 'marca_id' => 11, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 5, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '6956216251559', 'nombre' => 'Filtro de aceite sof 1 2314', 'descripcion' => '', 'marca_id' => 11, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '6956216252327', 'nombre' => 'Filtro de aceite sof 7 2370', 'descripcion' => '', 'marca_id' => 11, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 7, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '6956216253300', 'nombre' => 'Filtro de aceite sof 7 2374', 'descripcion' => '', 'marca_id' => 11, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => 'ACCU078702', 'nombre' => 'Filtro de aire azul', 'descripcion' => '', 'marca_id' => 12, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 2, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU078735', 'nombre' => 'Filtro de aire de estrella gris', 'descripcion' => '', 'marca_id' => 12, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 2, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU078727', 'nombre' => 'Filtro de aire estrella fc', 'descripcion' => '', 'marca_id' => 12, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 2, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU078705', 'nombre' => 'Filtro de aire f/c', 'descripcion' => '', 'marca_id' => 12, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 1, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU078703', 'nombre' => 'Filtro de aire rojo', 'descripcion' => '', 'marca_id' => 12, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 1, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => 'ACCU057951', 'nombre' => 'Foco de pellizoco', 'descripcion' => '', 'marca_id' => 7, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 8, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU070164', 'nombre' => 'Funda de volante Mazda', 'descripcion' => '', 'marca_id' => 7, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 2, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU070167', 'nombre' => 'Funda de volante toyota', 'descripcion' => '', 'marca_id' => 7, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 5, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => 'ACCU057404', 'nombre' => 'Halogen 12v60/55w', 'descripcion' => '', 'marca_id' => 14, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU057426', 'nombre' => 'Halogen 12V65W', 'descripcion' => '', 'marca_id' => 14, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 5, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => 'ACCU057965', 'nombre' => 'Led ligth', 'descripcion' => '', 'marca_id' => 5, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 4, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '765809513563', 'nombre' => 'OIL FILTER 51356', 'descripcion' => '', 'marca_id' => 16, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '765809515154', 'nombre' => 'OIL FILTER 51515', 'descripcion' => '', 'marca_id' => 16, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 5, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'BC0020772', 'nombre' => 'OIL FILTER O-1623', 'descripcion' => '', 'marca_id' => 17, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '25117', 'nombre' => 'OIL FILTER O-1637', 'descripcion' => '', 'marca_id' => 17, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '49778', 'nombre' => 'OIL FILTER 0-600', 'descripcion' => '', 'marca_id' => 17, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '79710', 'nombre' => 'OIL FILTER O-72600', 'descripcion' => '', 'marca_id' => 17, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 5, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => '4260610395415', 'nombre' => 'OIL FILTER OS-104', 'descripcion' => '', 'marca_id' => 18, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '4260610395453', 'nombre' => 'OIL FILTER OS-134', 'descripcion' => '', 'marca_id' => 18, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '4260610395798', 'nombre' => 'OIL FILTER OS-143', 'descripcion' => '', 'marca_id' => 18, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => '009100381019', 'nombre' => 'OIL FILTER PH 3614', 'descripcion' => '', 'marca_id' => 10, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '009100382139', 'nombre' => 'OIL FILTER PH 6607', 'descripcion' => '', 'marca_id' => 10, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 5, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => '009100382610', 'nombre' => 'OIL FILTER PH 7317', 'descripcion' => '', 'marca_id' => 10, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => '6257', 'nombre' => 'OIL FILTER O 8307', 'descripcion' => '', 'marca_id' => 17, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 6, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU057463', 'nombre' => 'Plafon lateral', 'descripcion' => '', 'marca_id' => 7, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 7, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => 'ACCU056493', 'nombre' => 'Timon adaptador 1', 'descripcion' => '', 'marca_id' => 19, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 1, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU056491', 'nombre' => 'Timon adaptador 2', 'descripcion' => '', 'marca_id' => 19, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 1, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU056495', 'nombre' => 'Timón adaptador 3', 'descripcion' => '', 'marca_id' => 19, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 1, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => 'ACCU056426', 'nombre' => 'Timon deportivo cuerina clasico', 'descripcion' => '', 'marca_id' => 13, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 1, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
            ['codigo' => 'ACCU056459', 'nombre' => 'Timon deportivo negro cintas rojas', 'descripcion' => '', 'marca_id' => 13, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 1, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],

            ['codigo' => 'ACCU052114', 'nombre' => 'MARCO PLACA REGULABLE', 'descripcion' => '', 'marca_id' => 6, 'categoria_id' => null, 'unidad_medida' => 'pza', 'tipo_producto_id' => 1, 'precio_compra' => null, 'precio_venta' => null, 'precio_minimo' => null, 'stock_actual' => 2, 'stock_minimo' => null, 'stock_maximo' => null, 'activo' => 1, 'control_stock' => 1],
        ];

        // Asignar id y timestamps
        foreach ($rows as $i => &$row) {
            $row['id'] = $i + 1;
            $row['created_at'] = now();
            $row['updated_at'] = now();
        }
        unset($row);

        // Asegurar que los precios no queden en NULL: precio_compra, precio_venta, precio_minimo => 0.0
        foreach ($rows as &$r) {
            if (!array_key_exists('precio_compra', $r) || $r['precio_compra'] === null) {
                $r['precio_compra'] = 0.0;
            } else {
                $r['precio_compra'] = (float) $r['precio_compra'];
            }

            if (!array_key_exists('precio_venta', $r) || $r['precio_venta'] === null) {
                $r['precio_venta'] = 0.0;
            } else {
                $r['precio_venta'] = (float) $r['precio_venta'];
            }

            if (!array_key_exists('precio_minimo', $r) || $r['precio_minimo'] === null) {
                $r['precio_minimo'] = 0.0;
            } else {
                $r['precio_minimo'] = (float) $r['precio_minimo'];
            }
        }
        unset($r);

        // Asegurar que los stocks no queden en NULL: stock_actual, stock_minimo => 0; stock_maximo => null si no existe
        foreach ($rows as &$r) {
            if (!array_key_exists('stock_actual', $r) || $r['stock_actual'] === null) {
                $r['stock_actual'] = 0;
            } else {
                $r['stock_actual'] = (int) $r['stock_actual'];
            }

            if (!array_key_exists('stock_minimo', $r) || $r['stock_minimo'] === null) {
                $r['stock_minimo'] = 0;
            } else {
                $r['stock_minimo'] = (int) $r['stock_minimo'];
            }

            if (!array_key_exists('stock_maximo', $r) || $r['stock_maximo'] === null) {
                $r['stock_maximo'] = null;
            } else {
                $r['stock_maximo'] = (int) $r['stock_maximo'];
            }
        }
        unset($r);

        // Columnas para actualizar
        $updateColumns = [
            'nombre',
            'descripcion',
            'marca_id',
            'categoria_id',
            'unidad_medida',
            'tipo_producto_id',
            'precio_compra',
            'precio_venta',
            'precio_minimo',
            'stock_actual',
            'stock_minimo',
            'stock_maximo',
            'activo',
            'control_stock'
        ];

        // Insertar en chunks
        $chunks = array_chunk($rows, 20);

        foreach ($chunks as $chunk) {
            DB::table('productos')->upsert(
                $chunk,
                ['codigo'],
                $updateColumns
            );
        }

        $this->command->info('Seeder de productos ejecutado correctamente.');
    }
}
