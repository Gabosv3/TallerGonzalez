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

            // Datos básicos
            $table->string('codigo')->unique()->nullable()->comment('SKU o código interno');
            $table->string('nombre')->comment('Nombre del producto');
            $table->text('descripcion')->nullable()->comment('Descripción detallada');
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->restrictOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->restrictOnDelete();
            $table->string('unidad_medida')->default('pza')->comment('pza, kg, litro, etc.');

            // Tipo de producto
            $table->foreignId('tipo_producto_id')->constrained('tipos_producto')->restrictOnDelete();
            $table->string('producto_type')->nullable()->comment('Tipo de producto específico: App\\Models\\Aceite, etc.');
            $table->unsignedBigInteger('producto_id')->nullable()->comment('ID del producto específico');

            // Precios
            $table->decimal('precio_compra', 12, 2)->default(0)->comment('Precio de compra promedio');
            $table->decimal('precio_venta', 12, 2)->default(0)->comment('Precio de venta al público');
            $table->decimal('precio_minimo', 12, 2)->default(0)->comment('Precio mínimo de venta');

            // Inventario (control centralizado)
            $table->integer('stock_actual')->default(0)->comment('Stock actual en inventario');
            $table->integer('stock_minimo')->default(0)->comment('Stock mínimo alerta');
            $table->integer('stock_maximo')->nullable()->comment('Stock máximo recomendado');

            // Control
            $table->boolean('activo')->default(true)->comment('Producto activo/inactivo');
            $table->boolean('control_stock')->default(true)->comment('Controlar stock del producto');
            $table->text('especificaciones_generales')->nullable()->comment('Especificaciones técnicas generales');

            // Auditoría
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['nombre', 'activo']);
            $table->index('codigo');
            $table->index(['tipo_producto_id', 'activo']);
            $table->index(['producto_type', 'producto_id']);
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
