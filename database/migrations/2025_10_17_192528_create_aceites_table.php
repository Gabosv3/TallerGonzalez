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
        Schema::create('aceites', function (Blueprint $table) {
            $table->id();

            // Relación con producto principal
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();

            // Especificaciones técnicas
            $table->foreignId('marca_id')->constrained('marcas')->restrictOnDelete()->comment('Marca específica del aceite');
            $table->foreignId('aceite_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('modelo')->nullable()->comment('Modelo específico o referencia');
            $table->string('viscosidad')->comment('Ej: 5W-30, 10W-40');
            $table->foreignId('tipo_aceite_id')->constrained('tipos_aceites')->restrictOnDelete();

            // Envase y presentación
            $table->decimal('capacidad_ml', 8, 2)->comment('Capacidad en mililitros');
            $table->string('unidad_medida')->default('ml')->comment('ml, L, galón, etc.');
            $table->string('presentacion')->nullable()->comment('Botella, bidón, tambor');

            // Especificaciones técnicas adicionales
            $table->string('norma_api')->nullable()->comment('Ej: API SN, CK-4');
            $table->string('norma_acea')->nullable()->comment('Ej: A3/B4, C3');
            $table->string('viscosidad_sae')->nullable()->comment('Clasificación SAE');
            $table->decimal('punto_ignicion', 6, 1)->nullable()->comment('°C');
            $table->decimal('punto_fluidez', 6, 1)->nullable()->comment('°C');

            // Aplicaciones
            $table->json('aplicaciones')->nullable()->comment('Tipos de motor compatibles');
            $table->text('compatibilidad')->nullable()->comment('Marcas y modelos de vehículos');

            // Control de stock específico (sincronizado con productos)
            $table->integer('stock_disponible')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->integer('stock_maximo')->nullable();

            // Estado
            $table->boolean('activo')->default(true);

            $table->timestamps();

            // Índices
            $table->index(['viscosidad', 'tipo_aceite_id']);
            $table->index('stock_disponible');
            $table->index('producto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aceites');
    }
};
