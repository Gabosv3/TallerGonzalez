@extends('exports.layout')

@section('titulo', 'Reporte de clientes')

@section('contenido')
    <div class="filtros">
        <b>Estado:</b> {{ $filtroEstado }}
        · <b>Compras del período:</b>
        {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : 'Inicio' }}
        al
        {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : 'hoy' }}
    </div>

    <table class="kpis">
        <tr>
            <td><span class="l">Clientes</span><span class="v">{{ $resumen['cantidad'] }}</span></td>
            <td><span class="l">Con compras en el período</span><span class="v">{{ $resumen['con_compras'] }}</span></td>
            <td><span class="l">Total facturado</span><span class="v">$ {{ number_format($resumen['total'], 2) }}</span></td>
            <td><span class="l">Promedio por cliente</span><span class="v">$ {{ number_format($resumen['con_compras'] > 0 ? $resumen['total'] / $resumen['con_compras'] : 0, 2) }}</span></td>
        </tr>
    </table>

    <table class="datos">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Contacto</th>
                <th>DUI / NIT</th>
                <th class="c">Estado</th>
                <th class="c">Facturas</th>
                <th class="r">Total comprado</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clientes as $c)
                <tr>
                    <td>{{ \App\Support\ReportData::nombreCliente($c) }}</td>
                    <td>{{ $c->email }}<br><span style="color:#6b7280">{{ $c->telefono }}</span></td>
                    <td>{{ $c->dui ?: ($c->nit ?: '—') }}</td>
                    <td class="c"><span class="badge {{ $c->activo ? 'ok' : 'gris' }}">{{ $c->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td class="c">{{ $c->facturas_count }}</td>
                    <td class="r">$ {{ number_format($c->facturas_sum_total ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="vacio">No hay clientes para los filtros seleccionados.</td></tr>
            @endforelse
        </tbody>
        @if ($clientes->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="5" class="r">Total facturado</td>
                    <td class="r">$ {{ number_format($resumen['total'], 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
@endsection
