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
        Schema::create('tipos_aceites', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('clave')->unique()->comment('Para uso interno: sintetico, semi, mineral');
            $table->text('descripcion')->nullable();
            $table->string('color')->default('#6B7280')->comment('Color para UI');
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_aceites');
    }
};
