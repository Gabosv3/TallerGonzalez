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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            
            // Información del vehículo
            $table->string('placa')->comment('Placa del vehículo');
            
            // Productos usados en el servicio (guardado como JSON)
            $table->json('productos')->nullable()->comment('Productos utilizados en formato JSON');
            
            // Servicios realizados (guardado como JSON)
            $table->json('servicios')->nullable()->comment('Servicios realizados en formato JSON');
            
            // Estado del servicio
            $table->enum('estado', ['pendiente', 'en_proceso', 'completado', 'cancelado'])
                ->default('pendiente')
                ->comment('Estado actual del servicio');
            
            // Información adicional
            $table->text('notas')->nullable()->comment('Notas o observaciones del servicio');
            
            $table->timestamps();
            
            // Índices
            $table->index('placa');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
