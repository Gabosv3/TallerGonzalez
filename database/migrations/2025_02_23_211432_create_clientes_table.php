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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            
            // Información Personal Básica
            $table->string('codigo_cliente')->unique()->nullable()->comment('Código interno del cliente');
            $table->string('nombre')->comment('Nombre del cliente');
            $table->string('apellido')->comment('Apellido del cliente');
            $table->string('email')->unique()->comment('Correo electrónico');
            $table->string('telefono')->nullable()->comment('Teléfono principal');
            $table->string('telefono_alternativo')->nullable()->comment('Teléfono alternativo');
            
            // Documentos de Identificación (El Salvador)
            $table->string('dui')->unique()->nullable()->comment('DUI del cliente');
            $table->string('nit')->unique()->nullable()->comment('NIT para crédito fiscal');
            $table->string('nrc')->nullable()->comment('Número de Registro de Contribuyente');
            
            // Tipo de Cliente
            $table->enum('tipo_cliente', [
                'consumidor_final',
                'contribuyente',
                'empresa',
                'distribuidor',
                'mayorista'
            ])->default('consumidor_final')->comment('Tipo de cliente para facturación');
            
            // Información de Empresa (si aplica)
            $table->string('razon_social')->nullable()->comment('Razón social para facturación');
            $table->string('nombre_comercial')->nullable()->comment('Nombre comercial');
            $table->string('giro')->nullable()->comment('Giro del negocio');
            
            // Dirección Principal
            $table->string('direccion')->nullable()->comment('Dirección completa');
            $table->string('departamento')->nullable()->comment('Departamento');
            $table->string('municipio')->nullable()->comment('Municipio');
            $table->string('distrito')->nullable()->comment('Distrito o cantón');
            $table->string('codigo_postal')->nullable()->comment('Código postal');
            
            // Dirección de Envío (puede ser diferente)
            $table->string('envio_direccion')->nullable()->comment('Dirección de envío');
            $table->string('envio_departamento')->nullable()->comment('Departamento de envío');
            $table->string('envio_municipio')->nullable()->comment('Municipio de envío');
            $table->string('envio_distrito')->nullable()->comment('Distrito de envío');
            $table->string('envio_referencia')->nullable()->comment('Referencia para envío');
            
            // Información de Contacto Adicional
            $table->string('contacto_empresa')->nullable()->comment('Persona de contacto en empresa');
            $table->string('cargo_contacto')->nullable()->comment('Cargo del contacto');
            
            // Límites y Condiciones Comerciales
            $table->decimal('limite_credito', 12, 2)->default(0)->comment('Límite de crédito asignado');
            $table->integer('dias_credito')->default(0)->comment('Días de crédito otorgados');
            $table->decimal('descuento_autorizado', 5, 2)->default(0)->comment('Porcentaje de descuento autorizado');
            
            // Estado y Control
            $table->boolean('activo')->default(true)->comment('Cliente activo/inactivo');
            $table->boolean('credito_activo')->default(false)->comment('Si tiene crédito autorizado');
            $table->text('observaciones')->nullable()->comment('Observaciones adicionales');
            
            // Auditoría
            $table->timestamp('aprobado_credito_at')->nullable()->comment('Fecha de aprobación de crédito');
            $table->foreignId('aprobado_por')->nullable()->constrained('users')->comment('Usuario que aprobó el crédito');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para optimización
            $table->index(['tipo_cliente', 'activo']);
            $table->index(['departamento', 'municipio']);
            $table->index('credito_activo');
            $table->index('codigo_cliente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
