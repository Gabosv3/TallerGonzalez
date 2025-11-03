<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique()->nullable()->comment('SKU o código interno');
            $table->string('nombre')->comment('Nombre del producto');
            $table->text('descripcion')->nullable()->comment('Descripción detallada');
            $table->string('marca')->nullable()->comment('Marca del producto');
            $table->string('categoria')->nullable()->comment('Categoría del producto');
            $table->string('unidad_medida')->default('pza')->comment('Unidad de medida: pza, kg, litro, etc.');
            
            // Precios
            $table->decimal('precio_compra', 10, 2)->default(0)->comment('Precio de compra promedio');
            $table->decimal('precio_venta', 10, 2)->default(0)->comment('Precio de venta al público');
            $table->decimal('precio_minimo', 10, 2)->default(0)->comment('Precio mínimo de venta');
            
            // Inventario
            $table->integer('stock_actual')->default(0)->comment('Stock actual en inventario');
            $table->integer('stock_minimo')->default(0)->comment('Stock mínimo alerta');
            $table->integer('stock_maximo')->nullable()->comment('Stock máximo recomendado');
            
            // Control
            $table->boolean('activo')->default(true)->comment('Producto activo/inactivo');
            $table->boolean('control_stock')->default(true)->comment('Controlar stock del producto');
            $table->text('especificaciones')->nullable()->comment('Especificaciones técnicas');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['nombre', 'marca', 'categoria', 'activo']);
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
