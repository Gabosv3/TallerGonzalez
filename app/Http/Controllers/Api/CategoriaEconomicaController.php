<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaEconomica;
use Illuminate\Http\Request;

class CategoriaEconomicaController extends Controller
{
    /**
     * Listar todas las categorías económicas con búsqueda
     */
    public function index(Request $request)
    {
        try {
            $query = CategoriaEconomica::query();

            // Búsqueda por código o descripción
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('codigo', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%");
                });
            }

            // Ordenamiento
            $sortField = $request->get('sort_field', 'codigo');
            $sortDirection = $request->get('sort_direction', 'asc');

            $categorias = $query->orderBy($sortField, $sortDirection)
                              ->paginate($request->get('per_page', 50));

            return response()->json([
                'data' => $categorias->items(),
                'pagination' => [
                    'total' => $categorias->total(),
                    'per_page' => $categorias->perPage(),
                    'current_page' => $categorias->currentPage(),
                    'last_page' => $categorias->lastPage(),
                    'from' => $categorias->firstItem(),
                    'to' => $categorias->lastItem(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener categorías económicas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener categoría económica por código
     */
    public function show($codigo)
    {
        try {
            $categoria = CategoriaEconomica::find($codigo);

            if (!$categoria) {
                return response()->json([
                    'error' => 'Categoría económica no encontrada'
                ], 404);
            }

            return response()->json([
                'data' => $categoria
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener la categoría económica',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Búsqueda rápida de categorías por descripción
     */
    public function buscar($termino)
    {
        try {
            $categorias = CategoriaEconomica::where('descripcion', 'like', "%{$termino}%")
                                           ->orWhere('codigo', 'like', "%{$termino}%")
                                           ->limit(20)
                                           ->get();

            return response()->json([
                'data' => $categorias,
                'total' => count($categorias)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en la búsqueda',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
