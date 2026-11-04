@extends('exports.layout')

@section('titulo', 'Reporte de facturas')

@section('contenido')
    @php
        $estados = ['pagada' => ['Pagada', 'ok'], 'pendiente' => ['Pendiente', 'warn'], 'cancelada' => ['Cancelada', 'bad']];
    @endphp

    <div class="filtros">
        <b>Período:</b>
        {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : 'Inicio' }}
        al
        {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : 'hoy' }}
        @if ($filtroCliente) · <b>Cliente:</b> {{ $filtroCliente }} @endif
        @if ($filtroEstado) · <b>Estado:</b> {{ ucfirst($filtroEstado) }} @endif
    </div>

    <table class="kpis">
        <tr>
            <td><span class="l">Facturas</span><span class="v">{{ $resumen['cantidad'] }}</span></td>
            <td><span class="l">Ventas (sin canceladas)</span><span class="v">$ {{ number_format($resumen['total'], 2) }}</span></td>
            <td><span class="l">Promedio por factura</span><span class="v">$ {{ number_format($resumen['promedio'], 2) }}</span></td>
            <td><span class="l">Canceladas</span><span class="v">{{ $resumen['canceladas'] }}</span></td>
        </tr>
    </table>

    <table class="datos">
        <thead>
            <tr>
                <th>N.º factura</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th class="c">Ítems</th>
                <th class="c">Estado</th>
                <th class="r">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($facturas as $f)
                @php [$etiqueta, $clase] = $estados[$f->estado] ?? [ucfirst((string) $f->estado), 'gris']; @endphp
                <tr>
                    <td>{{ $f->numero_factura }}</td>
                    <td>{{ $f->fecha?->format('d/m/Y') ?? $f->created_at->format('d/m/Y') }}</td>
                    <td>{{ \App\Support\ReportData::nombreCliente($f->getRelation("cliente"), $f->getRawOriginal("cliente")) }}</td>
                    <td class="c">{{ $f->detalles_count }}</td>
                    <td class="c"><span class="badge {{ $clase }}">{{ $etiqueta }}</span></td>
                    <td class="r">$ {{ number_format($f->total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="vacio">No hay facturas para los filtros seleccionados.</td></tr>
            @endforelse
        </tbody>
        @if ($facturas->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="5" class="r">Total de ventas</td>
                    <td class="r">$ {{ number_format($resumen['total'], 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
@endsection
