<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('descuento_tipo', 12)->default('monto')->after('subtotal')->comment('monto = valor en dólares, porcentaje = % sobre el subtotal');
            $table->decimal('descuento_valor', 12, 2)->default(0)->after('descuento_tipo')->comment('Valor tal como se digitó (monto o porcentaje)');
            $table->decimal('descuento_monto', 12, 2)->default(0)->after('descuento_valor')->comment('Descuento aplicado en dólares, antes del impuesto');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['descuento_tipo', 'descuento_valor', 'descuento_monto']);
        });
    }
};
