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
        Schema::table('aceites', function (Blueprint $table) {
            $table->dropColumn([
                'norma_api',
                'norma_acea',
                'viscosidad_sae',
                'punto_ignicion',
                'punto_fluidez',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aceites', function (Blueprint $table) {
            $table->string('norma_api')->nullable()->comment('Ej: API SN, CK-4');
            $table->string('norma_acea')->nullable()->comment('Ej: A3/B4, C3');
            $table->string('viscosidad_sae')->nullable()->comment('Clasificación SAE');
            $table->decimal('punto_ignicion', 6, 1)->nullable()->comment('°C');
            $table->decimal('punto_fluidez', 6, 1)->nullable()->comment('°C');
        });
    }
};
