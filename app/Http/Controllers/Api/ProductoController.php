<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Listar todos los productos con filtros
     */
    public function index(Request $request)
    {
        try {
            $query = Producto::with(['marca', 'categoria', 'tipoProducto', 'aceites.marca', 'aceites.tipoAceite'])
                            ->activos()
                            ->where('stock_actual', '>', 0); // Solo productos con stock

            // Búsqueda por código
            if ($request->has('codigo')) {
                $query->where('codigo', $request->codigo);
            }

            // Búsqueda general
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('codigo', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%")
                      ->orWhereHas('marca', function ($q) use ($search) {
                          $q->where('nombre', 'like', "%{$search}%");
                      });
                });
            }

            // Filtro por tipo
            if ($request->has('tipo')) {
                if ($request->tipo === 'aceite') {
                    $query->aceites();
                } elseif ($request->tipo === 'normal') {
                    $query->normales();
                }
            }

            // Ordenamiento
            $sortField = $request->get('sort_field', 'nombre');
            $sortDirection = $request->get('sort_direction', 'asc');
            
            $productos = $query->orderBy($sortField, $sortDirection)
                             ->paginate($request->get('per_page', 20));

            return ProductoResource::collection($productos);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener productos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar producto por código exacto
     */
    public function buscarPorCodigo($codigo)
    {
        try {
            $producto = Producto::with(['marca', 'categoria', 'tipoProducto', 'aceites.marca', 'aceites.tipoAceite'])
                               ->where('codigo', $codigo)
                               ->where('stock_actual', '>', 0)
                               ->activos()
                               ->first();

            if (!$producto) {
                return response()->json([
                    'error' => 'Producto no encontrado o sin stock',
                    'message' => 'No se encontró ningún producto con el código: ' . $codigo . ' (o no tiene stock disponible)'
                ], 404);
            }

            return new ProductoResource($producto);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en la búsqueda',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un producto específico
     */
    public function show($id)
    {
        try {
            $producto = Producto::with(['marca', 'categoria', 'tipoProducto', 'aceites.marca', 'aceites.tipoAceite'])
                               ->activos()
                               ->find($id);

            if (!$producto) {
                return response()->json([
                    'error' => 'Producto no encontrado'
                ], 404);
            }

            return new ProductoResource($producto);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener el producto',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Productos con stock bajo
     */
    public function stockBajo()
    {
        try {
            $productos = Producto::with(['marca', 'categoria'])
                                ->bajoStock()
                                ->activos()
                                ->orderBy('stock_actual', 'asc')
                                ->get();

            return ProductoResource::collection($productos);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener productos con stock bajo',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Productos por tipo
     */
    public function porTipo($tipo)
    {
        try {
            $query = Producto::with(['marca', 'categoria', 'tipoProducto'])
                            ->activos();

            if ($tipo === 'aceites') {
                $productos = $query->aceites()->get();
            } elseif ($tipo === 'normales') {
                $productos = $query->normales()->get();
            } else {
                return response()->json([
                    'error' => 'Tipo no válido',
                    'message' => 'Los tipos válidos son: aceites, normales'
                ], 400);
            }

            return ProductoResource::collection($productos);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener productos por tipo',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}