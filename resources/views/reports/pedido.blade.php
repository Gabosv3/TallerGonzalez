<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pedido {{ $pedido->numero_factura }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f4f4f4; text-align: left; }
        .right { text-align: right; }
        .center { text-align: center; }
        .header { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Orden de Compra: {{ $pedido->numero_factura }}</h2>
        <p>Proveedor: {{ $pedido->proveedor?->nombre }} | Fecha: {{ $pedido->fecha_orden?->format('d/m/Y') }}</p>
        <p>Estado: {{ ucfirst($pedido->estado) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Variante</th>
                <th class="right">Cantidad</th>
                <th class="right">Precio Sin IVA</th>
                <th class="right">Precio + IVA</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $i => $d)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $d->producto?->nombre }}</td>
                <td>{{ $d->aceite?->marca?->nombre }} {{ $d->aceite?->viscosidad }}</td>
                <td class="right">{{ $d->cantidad }}</td>
                <td class="right">${{ number_format($d->precio_unitario, 2) }}</td>
                <td class="right">${{ number_format($d->precio_con_iva, 2) }}</td>
                <td class="right">${{ number_format($d->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="right"><strong>Total</strong></td>
                <td class="right"><strong>${{ number_format($pedido->total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($pedido->observaciones)
    <div style="margin-top:10px">
        <strong>Observaciones:</strong>
        <p>{{ $pedido->observaciones }}</p>
    </div>
    @endif
</body>
</html>
