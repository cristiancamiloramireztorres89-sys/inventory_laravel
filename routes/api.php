<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoriaController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\CompraController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProductoController;
use App\Http\Controllers\Api\V1\VentaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Versión 1 (V1)
|--------------------------------------------------------------------------
| Rutas estructuradas y versionadas para la integración con Flutter.
| Proporcionan respuestas JSON estandarizadas y autenticación Bearer Token.
*/

Route::prefix('v1')->group(function () {

    // ==========================================
    // 1. Rutas Públicas (Sin Token)
    // ==========================================
    Route::post('/login', [AuthController::class, 'login']);

    // ==========================================
    // 2. Rutas Protegidas (Requieren Token Sanctum)
    // ==========================================
    Route::middleware('auth:sanctum')->group(function () {

        // --- Autenticación y Perfil de Usuario ---
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/perfil', [AuthController::class, 'perfil']);
        Route::get('/usuarios', [AuthController::class, 'usuarios']);
        Route::post('/usuarios', [AuthController::class, 'crearUsuario']);

        // --- Métricas del Dashboard ---
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // --- Categorías ---
        Route::get('/categorias', [CategoriaController::class, 'index']);
        Route::get('/categorias/{id}', [CategoriaController::class, 'show']);
        Route::post('/categorias', [CategoriaController::class, 'store']);

        // --- Productos y Verificación de Stock ---
        Route::get('/productos', [ProductoController::class, 'index']);
        Route::get('/productos/{id}', [ProductoController::class, 'show']);
        Route::post('/productos/verificar-stock', [ProductoController::class, 'verificarStock']);

        // --- Clientes ---
        Route::get('/clientes', [ClienteController::class, 'index']);
        Route::post('/clientes', [ClienteController::class, 'store']);
        Route::get('/clientes/{id}', [ClienteController::class, 'show']);

        // --- Ventas (POS Móvil) ---
        Route::get('/ventas', [VentaController::class, 'index']);
        Route::post('/ventas', [VentaController::class, 'store']);
        Route::get('/ventas/{id}', [VentaController::class, 'show']);

        // --- Compras y Proveedores (Reabastecimiento Móvil) ---
        Route::get('/compras', [CompraController::class, 'index']);
        Route::post('/compras', [CompraController::class, 'store']);
        Route::get('/compras/{id}', [CompraController::class, 'show']);
        Route::get('/proveedores', [CompraController::class, 'proveedores']);
    });
});

// Alias directo para compatibilidad rápida /api/login, /api/perfil, etc.
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/perfil', [AuthController::class, 'perfil']);
        Route::get('/usuarios', [AuthController::class, 'usuarios']);
        Route::post('/usuarios', [AuthController::class, 'crearUsuario']);
    Route::get('/user', [AuthController::class, 'perfil']);
});

Route::get('/v1/usuarios', [AuthController::class, 'usuarios']);
Route::get('/usuarios', [AuthController::class, 'usuarios']);

Route::get('/v1/categorias', [CategoriaController::class, 'index']);
Route::post('/v1/categorias', [CategoriaController::class, 'store']);
Route::get('/categorias', [CategoriaController::class, 'index']);


Route::put('/v1/usuarios/{id}', [AuthController::class, 'actualizarUsuario']);
Route::patch('/v1/usuarios/{id}/toggle', [AuthController::class, 'toggleUsuario']);
Route::delete('/v1/usuarios/{id}', [AuthController::class, 'eliminarUsuario']);

Route::put('/usuarios/{id}', [AuthController::class, 'actualizarUsuario']);
Route::patch('/usuarios/{id}/toggle', [AuthController::class, 'toggleUsuario']);
Route::delete('/usuarios/{id}', [AuthController::class, 'eliminarUsuario']);