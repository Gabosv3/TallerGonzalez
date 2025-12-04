<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\FacturaController;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Swagger/OpenAPI spec and UI (públicos)
use App\Http\Controllers\ApiDocsController;
Route::get('/openapi.yaml', [ApiDocsController::class, 'spec']);
Route::get('/openapi-debug', [ApiDocsController::class, 'specDebug']);

// Todas las rutas protegidas requieren autenticación
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Productos
    Route::prefix('productos')->group(function () {
        Route::get('/', [ProductoController::class, 'index']);
        Route::get('/buscar/{codigo}', [ProductoController::class, 'buscarPorCodigo']);
        Route::get('/{id}', [ProductoController::class, 'show']);
        Route::get('/tipo/{tipo}', [ProductoController::class, 'porTipo']);
        Route::get('/stock/bajo', [ProductoController::class, 'stockBajo']);
    });

    // Clientes
    
    Route::prefix('clientes')->group(function () {
        Route::get('/', [ClienteController::class, 'index']);
        Route::get('/buscar/{documento}', [ClienteController::class, 'buscarPorDocumento']);
        Route::get('/{id}', [ClienteController::class, 'show']);
    });

    // Facturas
    
    Route::prefix('facturas')->group(function () {
        Route::post('/', [FacturaController::class, 'store']);
        Route::get('/', [FacturaController::class, 'index']);
        Route::get('/{factura}', [FacturaController::class, 'show']);
        Route::put('/{factura}', [FacturaController::class, 'update']);
    });

    // Autenticación
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/check-auth', [AuthController::class, 'checkAuth']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
});

