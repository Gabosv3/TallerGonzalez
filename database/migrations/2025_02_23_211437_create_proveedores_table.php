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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique()->nullable()->comment('Código interno del proveedor');
            $table->string('nombre')->unique()->comment('Nombre del proveedor');
            $table->string('contacto')->nullable()->comment('Persona de contacto');
            $table->string('telefono')->nullable()->comment('Teléfono de contacto');
            $table->string('email')->nullable()->comment('Email de contacto');
            $table->string('direccion')->nullable()->comment('Dirección física');
            $table->string('ciudad')->nullable()->comment('Ciudad');
            $table->string('pais')->nullable()->default('México')->comment('País');
            $table->string('rfc')->nullable()->comment('RFC para facturación');
            $table->text('notas')->nullable()->comment('Observaciones adicionales');
            $table->boolean('activo')->default(true)->comment('Estado activo/inactivo');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['nombre', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
