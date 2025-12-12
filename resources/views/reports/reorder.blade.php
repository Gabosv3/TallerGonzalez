<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Reorden</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; text-align: left; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h2>Reporte de Reorden - Productos por debajo del stock mínimo</h2>
    <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Marca</th>
                <th class="right">Stock Actual</th>
                <th class="right">Stock Mínimo</th>
                <th class="right">Faltante</th>
                <th class="right">Precio Venta</th>
                <th class="right">Precio + IVA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->codigo }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td>{{ $p->marca?->nombre }}</td>
                    <td class="right">{{ $p->stock_actual }}</td>
                    <td class="right">{{ $p->stock_minimo }}</td>
                    <td class="right">{{ max(0, $p->stock_minimo - $p->stock_actual) }}</td>
                    <td class="right">${{ number_format($p->precio_venta, 2) }}</td>
                    <td class="right">${{ number_format($p->precio_venta * 1.13, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No hay productos que requieran reorden.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
