<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte Producto - {{ $producto->codigo }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 12px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ccc; padding: 6px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Producto</h1>
        <p>{{ $producto->codigo }} - {{ $producto->nombre }}</p>
    </div>

    <div class="section">
        <h3>Detalles</h3>
        <table class="table">
            <tr><th>Código</th><td>{{ $producto->codigo }}</td></tr>
            <tr><th>Nombre</th><td>{{ $producto->nombre }}</td></tr>
            <tr><th>Tipo</th><td>{{ $producto->tipoProducto?->nombre }}</td></tr>
            <tr><th>Marca</th><td>{{ $producto->marca?->nombre }}</td></tr>
            
            <tr><th>Precio Venta</th><td>${{ number_format($producto->precio_venta, 2) }}</td></tr>
            <tr><th>Precio + IVA (13%)</th><td>${{ number_format($producto->precio_venta * 1.13, 2) }}</td></tr>
            <tr><th>Precio Compra</th><td>${{ number_format($producto->precio_compra, 2) }}</td></tr>
            <tr><th>Stock Actual</th><td>{{ $producto->stock_actual }} {{ $producto->unidad_medida }}</td></tr>
            <tr><th>Activo</th><td>{{ $producto->activo ? 'Sí' : 'No' }}</td></tr>
        </table>
    </div>

    @if($producto->es_aceite)
    <div class="section">
        <h3>Variantes</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Marca</th>
                    <th>Viscosidad</th>
                    <th>Tipo</th>
                    <th>Capacidad</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($producto->aceites as $variante)
                <tr>
                    <td>{{ $variante->marca?->nombre }}</td>
                    <td>{{ $variante->viscosidad }}</td>
                    <td>{{ $variante->tipoAceite?->nombre }}</td>
                    <td>{{ $variante->capacidad_formateada }}</td>
                    <td>{{ $variante->stock_disponible }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>