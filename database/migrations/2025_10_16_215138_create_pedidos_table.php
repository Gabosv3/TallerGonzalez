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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('restrict');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Usuario que creó el pedido');
            
            // Información principal
            $table->string('numero_factura')->unique()->comment('Número de orden de compra');
            $table->date('fecha_orden')->comment('Fecha de emisión del pedido');
            $table->date('fecha_esperada')->comment('Fecha esperada de entrega');
            $table->date('fecha_entrega')->nullable()->comment('Fecha real de entrega');
            
            // Estado del pedido
            $table->enum('estado', [
                'pendiente',    // Pendiente de confirmación
                'confirmado',   // Confirmado con proveedor
                'en_camino',    // Enviado por proveedor
                'parcial',      // Parcialmente recibido
                'completado',   // Completamente recibido
                'cancelado'     // Cancelado
            ])->default('pendiente');
            
            // Información de contacto
            $table->string('contacto_proveedor')->nullable()->comment('Contacto en el proveedor');
            $table->string('telefono_proveedor')->nullable()->comment('Teléfono de contacto');
            
            // Totales financieros
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Subtotal sin impuestos');
            $table->decimal('impuesto_porcentaje', 5, 2)->default(16)->comment('Porcentaje de impuesto');
            $table->decimal('monto_impuesto', 12, 2)->default(0)->comment('Monto de impuestos');
            $table->decimal('total', 12, 2)->default(0)->comment('Total general');
            
            // Información adicional
            $table->text('observaciones')->nullable()->comment('Observaciones generales');
            $table->text('terminos_pago')->nullable()->comment('Términos y condiciones de pago');
            $table->text('condiciones_entrega')->nullable()->comment('Condiciones de entrega');
            
            // Auditoría
            $table->timestamp('confirmado_at')->nullable()->comment('Fecha de confirmación');
            $table->timestamp('completado_at')->nullable()->comment('Fecha de completado');
            $table->timestamp('cancelado_at')->nullable()->comment('Fecha de cancelación');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index(['numero_factura', 'estado']);
            $table->index(['fecha_orden', 'proveedor_id']);
            $table->index('estado');
            $table->index('fecha_esperada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
