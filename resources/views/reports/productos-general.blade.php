<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte General de Productos</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; text-align: left; }
        .small { font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Reporte General de Productos</div>
        <div class="small">Generado: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:8%">ID</th>
                <th style="width:18%">Código</th>
                <th>Nombre</th>
                <th style="width:12%">Stock</th>
                <th style="width:14%">Precio Venta</th>
                <th style="width:14%">Precio + IVA</th>
                <th style="width:16%">Marca / Tipo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $p)
                <tr>
                    <td>{{ $p->id }}</td>
                    <td>{{ $p->codigo }}</td>
                    <td>{{ $p->nombre }}</td>
                    <td style="text-align:right">{{ number_format($p->stock_actual, 0, ',', '.') }} {{ $p->unidad_medida }}</td>
                    <td style="text-align:right">${{ number_format($p->precio_venta, 2) }}</td>
                    <td style="text-align:right">${{ number_format($p->precio_venta * 1.13, 2) }}</td>
                    <td>{{ $p->marca?->nombre ?? '-' }} / {{ $p->tipoProducto?->nombre ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px; font-size:11px; color:#666">
        Total productos: {{ $products->count() }}
    </div>
</body>
</html>