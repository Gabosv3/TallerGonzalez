<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Productos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937; }
        .container { padding: 40px; }
        .header { margin-bottom: 30px; border-bottom: 3px solid #059669; padding-bottom: 15px; }
        .header h1 { color: #1f2937; font-size: 28px; margin-bottom: 5px; }
        .header p { color: #6b7280; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #059669; color: white; padding: 12px; text-align: left; font-weight: 600; font-size: 13px; }
        td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        tr:nth-child(even) { background-color: #f9fafb; }
        tr:hover { background-color: #f3f4f6; }
        .footer { margin-top: 40px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; padding-top: 15px; }
        .text-right { text-align: right; }
        .currency { font-weight: 600; }
        .stock-low { color: #dc2626; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Reporte de Productos</h1>
            <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th class="text-right">Stock Actual</th>
                    <th class="text-right">Mínimo</th>
                    <th class="text-right">Máximo</th>
                    <th class="text-right">Precio Venta</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $producto)
                    <tr>
                        <td>{{ $producto->codigo }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->marca?->nombre ?? 'N/A' }}</td>
                        <td class="text-right {{ $producto->stock_actual < $producto->stock_minimo ? 'stock-low' : '' }}">
                            {{ $producto->stock_actual }}
                        </td>
                        <td class="text-right">{{ $producto->stock_minimo }}</td>
                        <td class="text-right">{{ $producto->stock_maximo }}</td>
                        <td class="text-right currency">$ {{ number_format($producto->precio_venta, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #9ca3af;">No hay productos registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Total de registros: {{ $productos->count() }}</p>
        </div>
    </div>
</body>
</html>
