<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacturaRequest;
use App\Models\Factura;
use App\Models\DetalleFactura;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Responses\ApiResponse;
use App\Http\Resources\FacturaResource;
use App\Http\Requests\UpdateFacturaRequest;
use Carbon\Carbon;

class FacturaController extends Controller
{
    public function show(Factura $factura)
    {
        $factura->load('detalles.producto');
        return new FacturaResource($factura);
    }

    public function store(StoreFacturaRequest $request)
    {
        $data = $request->validated();

        try {
            $result = DB::transaction(function () use ($data) {
                // Cliente: usa cliente_id si viene, si no crea cliente mínimo con nombre
                $clienteId = $data['cliente_id'] ?? null;
                if (!$clienteId) {
                    $cliente = Cliente::create([
                        'nombre' => $data['cliente'],
                    ]);
                    $clienteId = $cliente->id;
                }

                // Generar numero_factura si no viene
                $numero = $data['numero_factura'] ?? ('FAC-' . now()->format('YmdHis'));

                // Crear factura
                $factura = Factura::create([
                    'numero_factura' => $numero,
                    'cliente_id' => $clienteId,
                    'cliente' => $data['cliente'] ?? null,
                    'fecha' => $data['fecha'] ?? now()->toDateString(),
                    'total' => 0,
                    'created_by' => auth()->id() ?? null,
                ]);

                $total = 0;

                foreach ($data['items'] as $item) {
                    // Bloquear fila del producto para evitar condiciones de carrera
                    $producto = Producto::where('id', $item['producto_id'])->lockForUpdate()->firstOrFail();

                    // Control de stock si aplica
                    if ($producto->control_stock && isset($item['cantidad'])) {
                        if ($producto->stock_actual < $item['cantidad']) {
                            throw new \Exception("Stock insuficiente para producto ID {$producto->id}");
                        }
                        // actualizarStock asume decremento; al tener lock, es seguro
                        $producto->actualizarStock((int)$item['cantidad'], 'salida');
                    }

                    $cantidad = $item['cantidad'];
                    $precio = $item['precio_unitario'];
                    $subtotal = round($cantidad * $precio, 2);

                    DetalleFactura::create([
                        'factura_id' => $factura->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $subtotal,
                    ]);

                    $total += $subtotal;
                }

                $factura->update(['total' => $total]);

                return $factura->load('detalles.producto');
            });

            return ApiResponse::success($result, 'Factura creada', 201);
        } catch (\Exception $e) {
            Log::error('Error creando factura via API: ' . $e->getMessage());
            return ApiResponse::error($e->getMessage(), 422);
        }
    }

    // Listado paginado con filtros: from, to, cliente_id, estado
    public function index(Request $request)
    {
        $query = Factura::with('detalles.producto');

        if ($request->has('from')) {
            $query->where('fecha', '>=', Carbon::parse($request->query('from'))->startOfDay());
        }
        if ($request->has('to')) {
            $query->where('fecha', '<=', Carbon::parse($request->query('to'))->endOfDay());
        }
        if ($request->has('cliente_id')) {
            $query->where('cliente_id', $request->query('cliente_id'));
        }
        if ($request->has('estado')) {
            $query->where('estado', $request->query('estado'));
        }

        $perPage = (int) $request->query('per_page', 15);
        $paginado = $query->orderBy('fecha', 'desc')->paginate($perPage);

        return FacturaResource::collection($paginado)->additional(['meta' => [
            'total' => $paginado->total(),
            'per_page' => $paginado->perPage(),
            'current_page' => $paginado->currentPage(),
        ]]);
    }

    // Actualizar estado o marcar pago sencillo
    public function update(UpdateFacturaRequest $request, Factura $factura)
    {
        $data = $request->validated();

        try {
            $result = DB::transaction(function () use ($data, $factura) {
                $oldEstado = $factura->estado;

                if ($data['pago'] ?? false) {
                    // Si se envía pago=true, marcar pagada
                    $factura->update(['estado' => 'pagada']);
                } elseif (isset($data['estado'])) {
                    $newEstado = $data['estado'];

                    // Si la factura pasa a "cancelada" desde otro estado, restaurar stock
                    if ($newEstado === 'cancelada' && $oldEstado !== 'cancelada') {
                        $factura->restoreStock();
                    }

                    $factura->update(['estado' => $newEstado]);
                }

                return $factura->fresh()->load('detalles.producto');
            });

            return new FacturaResource($result);
        } catch (\Exception $e) {
            Log::error('Error updating factura: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
