<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facturas periodo</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 12px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ccc; padding: 6px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Facturas</h1>
        <p>
            @if($from) Desde: {{ $from->format('Y-m-d') }} @endif
            @if($to) - Hasta: {{ $to->format('Y-m-d') }} @endif
        </p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>N° Factura</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th class="right">Total</th>
                <th class="right">Items</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facturas as $f)
            <tr>
                <td>{{ $f->id }}</td>
                <td>{{ $f->numero_factura }}</td>
                <td>{{ $f->cliente }}</td>
                <td>{{ optional($f->fecha)->format('Y-m-d') }}</td>
                <td class="right">{{ number_format($f->total, 2) }}</td>
                <td class="right">{{ $f->detalles->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
