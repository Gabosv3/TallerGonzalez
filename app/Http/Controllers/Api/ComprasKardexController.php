<?php

namespace App\Http\Controllers\Api;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ComprasKardexController
{
    /**
     * Devuelve órdenes de compra con sus detalles para alimentar el Kardex.
     * Filtra por rango de fechas (fecha_orden).
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'from'         => 'required|date',
            'to'           => 'required|date|after_or_equal:from',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'estado'       => 'nullable|in:pendiente,confirmado,en_camino,parcial,completado,cancelado',
        ]);

        $ordenes = Pedido::with([
            'proveedor:id,codigo,nombre,telefono,email',
            'detalles.producto:id,codigo,nombre,unidad_medida,precio_compra,precio_venta,stock_actual',
            'detalles.aceite:id,viscosidad,presentacion,capacidad_ml',
            'usuario:id,name,email',
        ])
        ->whereBetween('fecha_orden', [$request->from, $request->to])
        ->when($request->proveedor_id, fn($q) => $q->where('proveedor_id', $request->proveedor_id))
        ->when($request->estado,       fn($q) => $q->where('estado', $request->estado))
        ->orderBy('fecha_orden')
        ->get()
        ->map(fn($orden) => [
            'id'             => $orden->id,
            'numero_orden'   => $orden->numero_factura,
            'fecha_orden'    => $orden->fecha_orden,
            'fecha_esperada' => $orden->fecha_esperada,
            'fecha_entrega'  => $orden->fecha_entrega,
            'estado'         => $orden->estado,
            'proveedor'      => $orden->proveedor,
            'subtotal'       => $orden->subtotal,
            'monto_impuesto' => $orden->monto_impuesto,
            'total'          => $orden->total,
            'observaciones'  => $orden->observaciones,
            'creado_por'     => $orden->usuario?->name,
            'items'          => $orden->detalles->map(fn($d) => [
                'id'               => $d->id,
                'producto_id'      => $d->producto_id,
                'codigo'           => $d->producto_codigo ?? $d->producto?->codigo,
                'nombre'           => $d->producto_nombre  ?? $d->producto?->nombre,
                'unidad_medida'    => $d->unidad_medida    ?? $d->producto?->unidad_medida,
                'variante'         => $d->aceite ? $d->aceite->viscosidad . ' ' . $d->aceite->presentacion : null,
                'cantidad'         => $d->cantidad,
                'cantidad_recibida'=> $d->cantidad_recibida,
                'precio_unitario'  => $d->precio_unitario,
                'subtotal'         => $d->subtotal,
                'completado'       => $d->completado,
            ]),
        ]);

        return response()->json([
            'data'   => $ordenes,
            'total'  => $ordenes->count(),
            'periodo'=> [
                'from' => $request->from,
                'to'   => $request->to,
            ],
        ]);
    }

    /**
     * Devuelve una orden específica con todos sus detalles.
     */
    public function show(Pedido $orden): JsonResponse
    {
        $orden->load([
            'proveedor:id,codigo,nombre,telefono,email',
            'detalles.producto:id,codigo,nombre,unidad_medida,precio_compra,precio_venta,stock_actual',
            'detalles.aceite:id,viscosidad,presentacion,capacidad_ml',
            'usuario:id,name,email',
        ]);

        return response()->json([
            'data' => [
                'id'             => $orden->id,
                'numero_orden'   => $orden->numero_factura,
                'fecha_orden'    => $orden->fecha_orden,
                'fecha_esperada' => $orden->fecha_esperada,
                'fecha_entrega'  => $orden->fecha_entrega,
                'estado'         => $orden->estado,
                'proveedor'      => $orden->proveedor,
                'subtotal'       => $orden->subtotal,
                'monto_impuesto' => $orden->monto_impuesto,
                'total'          => $orden->total,
                'observaciones'  => $orden->observaciones,
                'creado_por'     => $orden->usuario?->name,
                'items'          => $orden->detalles->map(fn($d) => [
                    'id'               => $d->id,
                    'producto_id'      => $d->producto_id,
                    'codigo'           => $d->producto_codigo ?? $d->producto?->codigo,
                    'nombre'           => $d->producto_nombre  ?? $d->producto?->nombre,
                    'unidad_medida'    => $d->unidad_medida    ?? $d->producto?->unidad_medida,
                    'variante'         => $d->aceite ? $d->aceite->viscosidad . ' ' . $d->aceite->presentacion : null,
                    'cantidad'         => $d->cantidad,
                    'cantidad_recibida'=> $d->cantidad_recibida,
                    'precio_unitario'  => $d->precio_unitario,
                    'subtotal'         => $d->subtotal,
                    'completado'       => $d->completado,
                ]),
            ],
        ]);
    }
}
