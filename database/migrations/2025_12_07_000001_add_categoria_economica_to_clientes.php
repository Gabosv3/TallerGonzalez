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
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('categoria_economica_codigo')->nullable()->after('giro');
            $table->foreign('categoria_economica_codigo')
                  ->references('codigo')
                  ->on('categorias_economicas')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['categoria_economica_codigo']);
            $table->dropColumn('categoria_economica_codigo');
        });
    }
};
