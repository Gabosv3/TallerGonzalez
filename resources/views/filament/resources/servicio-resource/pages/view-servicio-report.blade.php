@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
        
        <!-- Header -->
        <div class="border-b-2 border-gray-300 pb-6 mb-6">
            <div class="grid grid-cols-3 gap-4 items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">PREFACTURA</h1>
                    <p class="text-gray-600 text-sm">Documento de servicio</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-600">Servicio #{{ $record->id ?? 'N/A' }}</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $record->placa ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-600 text-sm">Fecha: {{ $record->created_at ? $record->created_at->format('d/m/Y') : 'N/A' }}</p>
                    <p class="text-gray-600 text-sm">Actualizado: {{ $record->updated_at ? $record->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Estado -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="font-semibold text-gray-700">Estado del Servicio:</span>
                @php
                    $estado = $record->estado ?? 'pendiente';
                    $estadoClases = [
                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                        'en_proceso' => 'bg-blue-100 text-blue-800',
                        'completado' => 'bg-green-100 text-green-800',
                        'cancelado' => 'bg-red-100 text-red-800',
                    ];
                    $estadoTexto = [
                        'pendiente' => '🟡 Pendiente',
                        'en_proceso' => '🔵 En Proceso',
                        'completado' => '🟢 Completado',
                        'cancelado' => '🔴 Cancelado',
                    ];
                @endphp
                <span class="px-4 py-2 rounded-full font-bold {{ $estadoClases[$estado] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $estadoTexto[$estado] ?? 'Sin estado' }}
                </span>
            </div>
        </div>

        <!-- Productos -->
        @php
            $productos = $this->getProductos();
        @endphp
        @if(is_array($productos) && count($productos) > 0)
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-blue-500">
                📦 Productos Utilizados
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-blue-50 border-b-2 border-blue-200">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Código</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Producto</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                            @php
                                $codigo = $producto['codigo_producto'] ?? 'N/A';
                                $nombre = $producto['nombre_producto'] ?? 'Sin nombre';
                                $cantidad = $producto['cantidad'] ?? 0;
                            @endphp
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700 font-mono">{{ $codigo }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $nombre }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700">{{ $cantidad }}x</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Servicios -->
        @php
            $servicios = $this->getServicios();
        @endphp
        @if(is_array($servicios) && count($servicios) > 0)
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-green-500">
                🔧 Servicios Realizados
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-green-50 border-b-2 border-green-200">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Servicio</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Cantidad</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Precio Unitario</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($servicios as $servicio)
                            @php
                                $nombre = $servicio['servicio_nombre'] ?? 'Sin nombre';
                                $cantidad = (float)($servicio['servicio_cantidad'] ?? 1);
                                $precio = (float)($servicio['servicio_precio'] ?? 0);
                                $subtotal = $precio * $cantidad;
                            @endphp
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $nombre }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700">{{ $cantidad }}x</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">${{ number_format($precio, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-800">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Resumen Financiero -->
        <div class="mb-8 bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg border-2 border-blue-300">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Resumen Financiero</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-700">Total Productos:</p>
                    <p class="text-gray-600 text-sm">{{ is_array($productos) ? count($productos) : 0 }} items</p>
                </div>
                <div class="text-right">
                    <p class="text-gray-700">Total Servicios:</p>
                    <p class="text-2xl font-bold text-green-600">
                        ${{ number_format($this->getTotalServicios(), 2) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Notas -->
        @php
            $notas = $record->notas;
            $notasString = is_string($notas) ? $notas : (is_array($notas) ? json_encode($notas) : '');
        @endphp
        @if($notasString)
        <div class="mb-8 p-4 bg-yellow-50 border-l-4 border-yellow-400">
            <h3 class="font-semibold text-gray-800 mb-2">📝 Notas</h3>
            <p class="text-gray-700 text-sm whitespace-pre-wrap">{{ $notasString }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="border-t-2 border-gray-300 pt-6 mt-8 text-center text-gray-600 text-sm">
            <p>Este documento es una prefactura. No es válido como factura oficial.</p>
            <p class="mt-2">Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        <!-- Botones de Acción -->
        <div class="mt-8 flex gap-4 justify-center print:hidden">
            <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                🖨️ Imprimir/PDF
            </button>
            <a href="{{ route('filament.admin.resources.servicios.edit', $record) }}" class="px-6 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition">
                ← Volver a Editar
            </a>
        </div>
    </div>
</div>

<style>
    @media print {
        .print\:hidden {
            display: none !important;
        }
        body {
            background: white;
        }
    }
</style>
@endsection
