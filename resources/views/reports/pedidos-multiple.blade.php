<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pedidos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .pedido { page-break-after: always; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f4f4f4; text-align: left; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h2>Listado de Pedidos</h2>
    <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>

    @forelse($pedidos as $pedido)
        <div class="pedido">
            <h3>Orden: {{ $pedido->numero_factura }} — {{ $pedido->proveedor?->nombre }}</h3>
            <p>Fecha: {{ $pedido->fecha_orden?->format('d/m/Y') }} — Estado: {{ ucfirst($pedido->estado) }}</p>
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
                            <td>{{ $i + 1 }}</td>
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
        </div>
    @empty
        <p>No hay pedidos para mostrar.</p>
    @endforelse
</body>
</html>
