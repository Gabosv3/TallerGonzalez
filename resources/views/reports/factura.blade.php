<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Factura - {{ $factura->numero_factura }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ccc; padding: 6px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Factura</h1>
        <p>{{ $factura->numero_factura }} - {{ optional($factura->fecha)->format('Y-m-d') }}</p>
    </div>

    <div>
        <table class="table">
            <tr><th>Cliente</th><td>{{ $factura->cliente }}</td></tr>
            <tr><th>Fecha</th><td>{{ optional($factura->fecha)->format('Y-m-d') }}</td></tr>
            <tr><th>Total</th><td>{{ number_format($factura->total, 2) }}</td></tr>
        </table>
    </div>

    <h3>Detalles</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="right">Cantidad</th>
                <th class="right">Precio Unit.</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->detalles as $det)
            <tr>
                <td>{{ $det->producto?->nombre ?? '-' }}</td>
                <td class="right">{{ $det->cantidad }}</td>
                <td class="right">{{ number_format($det->precio_unitario, 2) }}</td>
                <td class="right">{{ number_format($det->subtotal, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="3" class="right">Total</th>
                <th class="right">{{ number_format($factura->total, 2) }}</th>
            </tr>
        </tbody>
    </table>
</body>
</html>
