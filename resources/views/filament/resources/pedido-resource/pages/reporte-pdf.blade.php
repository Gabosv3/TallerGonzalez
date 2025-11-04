<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orden de Compra {{ $pedido->numero_factura }}</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            margin: 20px; 
            font-size: 12px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #333; 
            padding-bottom: 20px; 
        }
        .company-info { 
            text-align: left; 
            margin-bottom: 20px; 
        }
        .order-info { 
            margin-bottom: 30px; 
        }
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .table th, .table td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        .table th { 
            background-color: #f5f5f5; 
            font-weight: bold;
        }
        .totals { 
            float: right; 
            width: 300px; 
            margin-top: 20px; 
        }
        .footer { 
            margin-top: 50px; 
            border-top: 1px solid #ddd; 
            padding-top: 20px; 
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ORDEN DE COMPRA</h1>
        <h2>N° {{ $pedido->numero_factura }}</h2>
    </div>

    <div class="company-info">
        <strong>SISTEMA DE GESTIÓN DE LUBRICANTES</strong><br>
        Fecha de emisión: {{ now()->format('d/m/Y') }}
    </div>

    <div class="order-info">
        <table style="width: 100%; margin-bottom: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <strong>PROVEEDOR:</strong><br>
                    {{ $pedido->proveedor->nombre }}<br>
                    @if($pedido->contacto_proveedor)
                    Contacto: {{ $pedido->contacto_proveedor }}<br>
                    @endif
                    @if($pedido->telefono_proveedor)
                    Teléfono: {{ $pedido->telefono_proveedor }}
                    @endif
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <strong>INFORMACIÓN DE LA ORDEN:</strong><br>
                    Fecha de orden: {{ $pedido->fecha_orden->format('d/m/Y') }}<br>
                    Fecha esperada: {{ $pedido->fecha_esperada->format('d/m/Y') }}<br>
                    Estado: {{ strtoupper($pedido->estado) }}
                </td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Precio Unitario</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->detalles as $detalle)
            <tr>
                <td>{{ $detalle->producto->nombre }}</td>
                <td class="text-center">{{ number_format($detalle->cantidad, 2) }}</td>
                <td class="text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">${{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table style="width: 100%;">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="text-right">${{ number_format($pedido->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Impuesto ({{ $pedido->impuesto_porcentaje }}%):</strong></td>
                <td class="text-right">${{ number_format($pedido->monto_impuesto, 2) }}</td>
            </tr>
            <tr>
                <td class="text-bold">TOTAL:</td>
                <td class="text-right text-bold">${{ number_format($pedido->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($pedido->observaciones)
    <div class="footer">
        <strong>Observaciones:</strong><br>
        {{ $pedido->observaciones }}
    </div>
    @endif

    <div class="footer">
        <p><strong>Generado el:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>