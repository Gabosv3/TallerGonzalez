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
        Schema::create('pedido_detalles', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            
            // Información del producto al momento del pedido (snapshot)
            $table->string('producto_nombre')->comment('Nombre del producto al momento del pedido');
            $table->string('producto_codigo')->nullable()->comment('Código del producto al momento del pedido');
            $table->string('unidad_medida')->default('pza')->comment('Unidad de medida');
            
            // Cantidades y precios
            $table->decimal('cantidad', 10, 2)->default(1)->comment('Cantidad pedida');
            $table->decimal('cantidad_recibida', 10, 2)->default(0)->comment('Cantidad recibida');
            $table->decimal('precio_unitario', 10, 2)->default(0)->comment('Precio unitario al momento del pedido');
            $table->decimal('subtotal', 10, 2)->default(0)->comment('Subtotal línea');
            
            // Control de recepción
            $table->boolean('completado')->default(false)->comment('Línea completamente recibida');
            $table->timestamp('recibido_at')->nullable()->comment('Fecha de recepción completa');
            
            // Información adicional
            $table->text('notas')->nullable()->comment('Notas específicas de esta línea');
            
            $table->timestamps();
            
            // Índices
            $table->index(['pedido_id', 'producto_id']);
            $table->index('completado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_detalles');
    }
};
