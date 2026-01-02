<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Compras por Producto</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 9px;
            color: #666;
        }
        
        .filtros {
            background-color: #f5f5f5;
            padding: 8px 10px;
            margin-bottom: 15px;
            border-left: 3px solid #007bff;
            font-size: 9px;
        }
        
        .producto-grupo {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .producto-titulo {
            background-color: #007bff;
            color: white;
            padding: 8px 10px;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            border-radius: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th {
            background-color: #e9ecef;
            padding: 6px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            border-bottom: 1px solid #dee2e6;
        }
        
        td {
            padding: 6px 5px;
            border-bottom: 1px solid #dee2e6;
            font-size: 9px;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .proveedor-fila {
            background-color: #f0f0f0;
            font-weight: 600;
        }
        
        .numero-factura {
            color: #007bff;
            font-weight: 500;
        }
        
        .precio {
            text-align: right;
            font-weight: 500;
        }
        
        .cantidad {
            text-align: center;
        }
        
        .resumen {
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-left: 3px solid #28a745;
            font-size: 9px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Compras por Producto</h1>
        <p>Taller González</p>
    </div>

    @if($fecha_inicio || $fecha_fin)
        <div class="filtros">
            <strong>Período:</strong>
            @if($fecha_inicio)
                Desde {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }}
            @endif
            @if($fecha_fin)
                hasta {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
            @endif
            <br>
            <strong>Generado:</strong> {{ $fecha_reporte }}
        </div>
    @endif

    @if($compras && count($compras) > 0)
        @foreach($compras as $productoId => $productoData)
            <div class="producto-grupo">
                <div class="producto-titulo">
                    📦 {{ $productoData['nombre'] }}
                    @if($productoData['codigo'] !== 'N/A')
                        <span style="font-size: 10px; font-weight: normal; margin-left: 15px;">Código: {{ $productoData['codigo'] }}</span>
                    @endif
                    <span style="font-size: 10px; font-weight: normal; margin-left: 15px;">Stock: {{ $productoData['stock_actual'] }}</span>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 25%">Proveedor</th>
                            <th style="width: 20%">Factura</th>
                            <th style="width: 12%">Fecha</th>
                            <th style="width: 18%">Precio Sin IVA</th>
                            <th style="width: 18%">Precio + IVA (13%)</th>
                            <th style="width: 12%">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalCantidad = 0;
                            $cantidadCompras = 0;
                        @endphp

                        @foreach($productoData['compras'] as $compra)
                            @php
                                $totalCantidad += $compra['cantidad_total'];
                                $cantidadCompras++;
                            @endphp
                            <tr class="proveedor-fila">
                                <td>{{ $compra['proveedor'] }}</td>
                                <td><span class="numero-factura">{{ $compra['numero_factura'] }}</span></td>
                                <td>{{ $compra['fecha_orden']?->format('d/m/Y') ?? 'N/A' }}</td>
                                <td class="precio">${{ number_format($compra['precio_sin_iva'], 2) }}</td>
                                <td class="precio">${{ number_format($compra['precio_con_iva'], 2) }}</td>
                                <td class="cantidad">{{ $compra['cantidad_total'] }}</td>
                            </tr>
                        @endforeach

                        <tr style="background-color: #e9ecef; font-weight: bold;">
                            <td colspan="5" style="text-align: right;">TOTAL CANTIDAD:</td>
                            <td class="cantidad">{{ $totalCantidad }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach

        <div class="resumen">
            <strong>Resumen del Reporte:</strong><br>
            • Total de productos reportados: <strong>{{ count($compras) }}</strong><br>
            • Total de compras (proveedor-factura): <strong>{{ array_sum(array_map(fn($p) => count($p['compras']), $compras)) }}</strong>
        </div>
    @else
        <div class="no-data">
            <p>No se encontraron compras para el período seleccionado.</p>
        </div>
    @endif

    <div class="footer">
        <p>Este reporte fue generado automáticamente el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
