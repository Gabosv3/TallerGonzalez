<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Listar todos los clientes con filtros
     */
    public function index(Request $request)
    {
        try {
            $query = Cliente::query();

            // Búsqueda por nombre o razón social
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere('razon_social', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('telefono', 'like', "%{$search}%")
                      ->orWhere('dui', 'like', "%{$search}%")
                      ->orWhere('nit', 'like', "%{$search}%");
                });
            }

            // Filtro por tipo de cliente
            if ($request->has('tipo_cliente')) {
                $query->where('tipo_cliente', $request->tipo_cliente);
            }

            // Filtro por estado activo
            if ($request->has('activo')) {
                $query->where('activo', $request->activo);
            }

            // Filtro por crédito activo
            if ($request->has('credito_activo')) {
                $query->where('credito_activo', $request->credito_activo);
            }

            // Ordenamiento
            $sortField = $request->get('sort_field', 'nombre');
            $sortDirection = $request->get('sort_direction', 'asc');
            
            $clientes = $query->orderBy($sortField, $sortDirection)
                            ->paginate($request->get('per_page', 20));

            return ClienteResource::collection($clientes);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener clientes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener cliente por ID
     */
    public function show($id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'error' => 'Cliente no encontrado'
                ], 404);
            }

            return new ClienteResource($cliente);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener el cliente',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar cliente por DUI o NIT
     */
    public function buscarPorDocumento($documento)
    {
        try {
            $cliente = Cliente::where('dui', $documento)
                             ->orWhere('nit', $documento)
                             ->first();

            if (!$cliente) {
                return response()->json([
                    'error' => 'Cliente no encontrado',
                    'message' => 'No se encontró cliente con DUI o NIT: ' . $documento
                ], 404);
            }

            return new ClienteResource($cliente);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error en la búsqueda',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
