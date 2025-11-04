<x-filament-panels::page>
    <x-filament-panels::header>
        <x-slot name="heading">
            
        </x-slot>

        <x-slot name="description">
            N° {{ $this->record->numero_factura }} - {{ $this->record->proveedor->nombre }}
        </x-slot>
    </x-filament-panels::header>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" id="reporte-content">
        <div class="text-center mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-900">ORDEN DE COMPRA</h1>
            <h2 class="text-xl font-semibold text-gray-700">N° {{ $this->record->numero_factura }}</h2>
            <p class="text-gray-600 mt-2">Sistema de Gestión de Lubricantes</p>
            <p class="text-gray-500">Fecha de emisión: {{ now()->format('d/m/Y') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="space-y-3">
                <h3 class="font-semibold text-gray-900">PROVEEDOR:</h3>
                <div class="text-gray-700">
                    <p class="font-medium">{{ $this->record->proveedor->nombre }}</p>
                    <p>Contacto: {{ $this->record->contacto_proveedor ?? 'No especificado' }}</p>
                    <p>Teléfono: {{ $this->record->telefono_proveedor ?? 'No especificado' }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <h3 class="font-semibold text-gray-900">INFORMACIÓN DE LA ORDEN:</h3>
                <div class="text-gray-700">
                    <p>Fecha de orden: {{ $this->record->fecha_orden->format('d/m/Y') }}</p>
                    <p>Fecha esperada: {{ $this->record->fecha_esperada->format('d/m/Y') }}</p>
                    <p>
                        Estado: 
                        <span @class([
                            'px-2 py-1 text-xs font-medium rounded-full',
                            'bg-yellow-100 text-yellow-800' => $this->record->estado === 'pendiente',
                            'bg-blue-100 text-blue-800' => $this->record->estado === 'confirmado',
                            'bg-orange-100 text-orange-800' => $this->record->estado === 'en_camino',
                            'bg-purple-100 text-purple-800' => $this->record->estado === 'parcial',
                            'bg-green-100 text-green-800' => $this->record->estado === 'completado',
                            'bg-red-100 text-red-800' => $this->record->estado === 'cancelado',
                        ])>
                            {{ strtoupper($this->record->estado) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">DETALLES DEL PEDIDO:</h3>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">Producto</th>
                            <th class="border border-gray-300 px-4 py-2 text-left font-semibold">Variante</th>
                            <th class="border border-gray-300 px-4 py-2 text-center font-semibold">Cantidad</th>
                            <th class="border border-gray-300 px-4 py-2 text-right font-semibold">Precio Unitario</th>
                            <th class="border border-gray-300 px-4 py-2 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->record->detalles as $detalle)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $detalle->producto->nombre }}
                                @if($detalle->producto->es_aceite)
                                    <span class="text-xs text-blue-600 block">🛢️ Aceite</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if($detalle->aceite_id && $detalle->aceite)
                                    <div class="text-sm">
                                        <div class="font-medium">{{ $detalle->aceite->marca->nombre ?? 'N/A' }}</div>
                                        <div class="text-gray-600">{{ $detalle->aceite->viscosidad }}</div>
                                        @if($detalle->aceite->tipoAceite)
                                            <div class="text-xs text-gray-500">{{ $detalle->aceite->tipoAceite->nombre }}</div>
                                        @endif
                                    </div>
                                @elseif($detalle->producto->es_aceite)
                                    <span class="text-xs text-gray-500">Variante no especificada</span>
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ number_format($detalle->cantidad, 2) }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-right">${{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Resumen de productos con variantes -->
        @php
            $productosConVariantes = $this->record->detalles->filter(fn($detalle) => $detalle->aceite_id && $detalle->aceite);
            $totalVariantes = $productosConVariantes->count();
        @endphp

        @if($totalVariantes > 0)
        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <h3 class="font-semibold text-blue-900 mb-2">📊 Resumen de Variantes:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @foreach($productosConVariantes as $detalle)
                <div class="flex items-center justify-between p-2 bg-white rounded border">
                    <div>
                        <span class="font-medium">{{ $detalle->producto->nombre }}</span>
                        <div class="text-xs text-gray-600">
                            {{ $detalle->aceite->marca->nombre ?? 'N/A' }} {{ $detalle->aceite->viscosidad }}
                            @if($detalle->aceite->tipoAceite)
                                - {{ $detalle->aceite->tipoAceite->nombre }}
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-semibold">{{ number_format($detalle->cantidad, 2) }} und.</span>
                        <div class="text-xs text-gray-500">${{ number_format($detalle->subtotal, 2) }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-2 text-sm text-blue-700">
                Total de variantes específicas: <strong>{{ $totalVariantes }}</strong>
            </div>
        </div>
        @endif

        <div class="flex justify-end">
            <div class="w-64 space-y-2">
                <div class="flex justify-between">
                    <span class="font-semibold">Subtotal:</span>
                    <span>${{ number_format($this->record->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-semibold">Impuesto ({{ $this->record->impuesto_porcentaje }}%):</span>
                    <span>${{ number_format($this->record->monto_impuesto, 2) }}</span>
                </div>
                <div class="flex justify-between border-t pt-2 font-bold text-lg">
                    <span>TOTAL:</span>
                    <span>${{ number_format($this->record->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Estadísticas del pedido -->
        <div class="mt-6 grid grid-cols-3 gap-4 text-center">
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ $this->record->detalles->count() }}</div>
                <div class="text-sm text-gray-600">Productos</div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($this->record->detalles->sum('cantidad'), 2) }}</div>
                <div class="text-sm text-gray-600">Unidades</div>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">{{ $totalVariantes }}</div>
                <div class="text-sm text-gray-600">Variantes</div>
            </div>
        </div>

        @if($this->record->observaciones)
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="font-semibold text-gray-900 mb-2">Observaciones:</h3>
            <p class="text-gray-700">{{ $this->record->observaciones }}</p>
        </div>
        @endif

        <div class="mt-8 pt-4 border-t text-center text-gray-500 text-sm">
            <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
            <p class="mt-1">Total de items: {{ $this->record->detalles->count() }} | Variantes específicas: {{ $totalVariantes }}</p>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('print', () => {
                window.print();
            });
        });
    </script>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #reporte-content, #reporte-content * {
                visibility: visible;
            }
            #reporte-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none;
                box-shadow: none;
                padding: 0;
            }
            .bg-blue-50 {
                background-color: #eff6ff !important;
                -webkit-print-color-adjust: exact;
            }
            .bg-gray-50 {
                background-color: #f9fafb !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</x-filament-panels::page>