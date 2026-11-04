@extends('exports.layout')

@section('titulo', 'Reporte de inventario')

@section('contenido')
    <div class="filtros">
        <b>Marca:</b> {{ $filtroMarca ?: 'Todas' }}
        · <b>Stock:</b> {{ $filtroStock ?: 'Todos' }}
    </div>

    <table class="kpis">
        <tr>
            <td><span class="l">Productos</span><span class="v">{{ $resumen['cantidad'] }}</span></td>
            <td><span class="l">Unidades en stock</span><span class="v">{{ number_format($resumen['unidades']) }}</span></td>
            <td><span class="l">Valor del inventario (costo)</span><span class="v">$ {{ number_format($resumen['valor'], 2) }}</span></td>
            <td><span class="l">Con stock bajo</span><span class="v">{{ $resumen['bajo'] }}</span></td>
        </tr>
    </table>

    <table class="datos">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Marca</th>
                <th class="c">Stock</th>
                <th class="c">Mín.</th>
                <th class="c">Estado</th>
                <th class="r">P. compra</th>
                <th class="r">P. venta</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($productos as $p)
                @php
                    [$etiqueta, $clase] = $p->stock_actual <= 0 ? ['Agotado', 'bad'] : ($p->stock_actual <= $p->stock_minimo ? ['Bajo', 'warn'] : ['Normal', 'ok']);
                @endphp
                <tr>
                    <td>{{ $p->codigo }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->marca?->nombre ?? '—' }}</td>
                    <td class="c">{{ $p->stock_actual }}</td>
                    <td class="c">{{ $p->stock_minimo }}</td>
                    <td class="c"><span class="badge {{ $clase }}">{{ $etiqueta }}</span></td>
                    <td class="r">$ {{ number_format($p->precio_compra, 2) }}</td>
                    <td class="r">$ {{ number_format($p->precio_venta, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="vacio">No hay productos para los filtros seleccionados.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
