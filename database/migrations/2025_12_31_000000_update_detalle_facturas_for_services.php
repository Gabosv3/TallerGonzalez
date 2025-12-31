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
        Schema::table('detalle_facturas', function (Blueprint $table) {
            // Hacer producto_id nullable (para servicios)
            $table->foreignId('producto_id')->nullable()->change();
            
            // Agregar descripción para servicios
            $table->string('descripcion')->nullable()->after('producto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_facturas', function (Blueprint $table) {
            // Revertir cambios
            $table->foreignId('producto_id')->nullable(false)->change();
            $table->dropColumn('descripcion');
        });
    }
};
