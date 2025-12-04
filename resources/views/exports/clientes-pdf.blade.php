<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Clientes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937; }
        .container { padding: 40px; }
        .header { margin-bottom: 30px; border-bottom: 3px solid #7c3aed; padding-bottom: 15px; }
        .header h1 { color: #1f2937; font-size: 28px; margin-bottom: 5px; }
        .header p { color: #6b7280; font-size: 14px; }
        .filters { background-color: #f3f4f6; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #7c3aed; color: white; padding: 12px; text-align: left; font-weight: 600; font-size: 13px; }
        td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        tr:nth-child(even) { background-color: #f9fafb; }
        tr:hover { background-color: #f3f4f6; }
        .footer { margin-top: 40px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; padding-top: 15px; }
        .text-right { text-align: right; }
        .currency { font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>👥 Reporte de Clientes</h1>
            <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        @if($desde || $hasta)
        <div class="filters">
            <strong>Filtro de fechas:</strong>
            {{ $desde ? 'Desde ' . \Carbon\Carbon::parse($desde)->format('d/m/Y') : 'Sin fecha inicial' }}
            {{ $hasta ? ' hasta ' . \Carbon\Carbon::parse($hasta)->format('d/m/Y') : '' }}
        </div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th class="text-right">Facturas</th>
                    <th class="text-right">Total Gasto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->email }}</td>
                        <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                        <td>{{ substr($cliente->direccion ?? 'N/A', 0, 40) }}</td>
                        <td class="text-right">{{ $cliente->facturas_count }}</td>
                        <td class="text-right currency">$ {{ number_format($cliente->facturas_sum_total ?? 0, 2) }}</td>
                        <td>Activo</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #9ca3af;">No hay clientes registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Total de registros: {{ $clientes->count() }} | Total de compras: $ {{ number_format($clientes->sum('facturas_sum_total'), 2) }}</p>
        </div>
    </div>
</body>
</html>
