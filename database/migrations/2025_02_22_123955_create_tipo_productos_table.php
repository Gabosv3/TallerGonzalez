<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipos_producto', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->string('clase_modelo')->nullable();
            $table->boolean('requiere_especificaciones')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar datos básicos
        DB::table('tipos_producto')->insert([
            [
                'nombre' => 'normal',
                'descripcion' => 'Productos normales sin especificaciones técnicas',
                'clase_modelo' => null,
                'requiere_especificaciones' => false,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'aceite',
                'descripcion' => 'Aceites lubricantes y fluidos',
                'clase_modelo' => 'App\\Models\\Aceite',
                'requiere_especificaciones' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_producto');
    }
};
