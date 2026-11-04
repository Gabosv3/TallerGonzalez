<x-filament-panels::page>
    @php
        $secciones = [
            'facturas' => [
                'titulo' => 'Ventas y facturas',
                'descripcion' => 'Facturas emitidas en un período, por cliente o estado.',
                'icono' => 'heroicon-o-document-text',
                'form' => $this->facturasForm,
                'query' => $facturasQuery,
                'kpis' => [
                    ['Facturas', $facturasResumen['cantidad']],
                    ['Ventas (sin canceladas)', '$' . number_format($facturasResumen['total'], 2)],
                    ['Promedio por factura', '$' . number_format($facturasResumen['promedio'], 2)],
                    ['Canceladas', $facturasResumen['canceladas']],
                ],
            ],
            'productos' => [
                'titulo' => 'Inventario',
                'descripcion' => 'Existencias, niveles de stock y valor del inventario.',
                'icono' => 'heroicon-o-cube',
                'form' => $this->productosForm,
                'query' => $productosQuery,
                'kpis' => [
                    ['Productos', $productosResumen['cantidad']],
                    ['Unidades en stock', number_format($productosResumen['unidades'])],
                    ['Valor a costo', '$' . number_format($productosResumen['valor'], 2)],
                    ['Con stock bajo', $productosResumen['bajo']],
                ],
            ],
            'clientes' => [
                'titulo' => 'Clientes',
                'descripcion' => 'Cartera de clientes y su volumen de compras.',
                'icono' => 'heroicon-o-user-group',
                'form' => $this->clientesForm,
                'query' => $clientesQuery,
                'kpis' => [
                    ['Clientes', $clientesResumen['cantidad']],
                    ['Con compras', $clientesResumen['con_compras']],
                    ['Total facturado', '$' . number_format($clientesResumen['total'], 2)],
                ],
            ],
        ];
    @endphp

    <div x-data="{ tab: 'facturas' }" class="grid gap-6">
        <x-filament::tabs>
            @foreach ($secciones as $clave => $s)
                <x-filament::tabs.item :icon="$s['icono']" alpine-active="tab === '{{ $clave }}'" x-on:click="tab = '{{ $clave }}'">
                    {{ $s['titulo'] }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>

        @foreach ($secciones as $clave => $s)
            <div x-show="tab === '{{ $clave }}'" @if (! $loop->first) x-cloak @endif class="grid gap-6">
                <x-filament::section :heading="$s['titulo']" :description="$s['descripcion']" :icon="$s['icono']">
                    {{ $s['form'] }}
                </x-filament::section>

                <div class="kpi-grid">
                    @foreach ($s['kpis'] as [$etiqueta, $valor])
                        <div class="kpi kpi--info">
                            <div>
                                <span class="kpi-label">{{ $etiqueta }}</span>
                                <strong class="kpi-value">{{ $valor }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <x-filament::button tag="a" color="gray" icon="heroicon-o-table-cells"
                        :href="route('reports.' . $clave . '.excel', $s['query'])">
                        Descargar Excel
                    </x-filament::button>
                    <x-filament::button tag="a" icon="heroicon-o-arrow-down-tray"
                        :href="route('reports.' . $clave . '.pdf', $s['query'])">
                        Descargar PDF
                    </x-filament::button>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
