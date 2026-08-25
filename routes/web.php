<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Vendedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Web Principales del Sistema
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        return $user->esAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('vendedor.dashboard');
    }

    return view('welcome');
});

// Rutas de Autenticación
require __DIR__.'/auth.php';

// Rutas del Panel Administrador (Protegidas por Auth y Rol Administrador)
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Usuarios (Listar, Crear, Editar, Desactivar/Activar y Eliminar)
    Route::get('/usuarios', [Admin\UsuarioController::class, 'index'])->name('usuarios');
    Route::post('/usuarios', [Admin\UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [Admin\UsuarioController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/toggle', [Admin\UsuarioController::class, 'toggleStatus'])->name('usuarios.toggle');
    Route::delete('/usuarios/{usuario}', [Admin\UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    // Categorias CRUD
    Route::get('/categorias', [Admin\CategoriaController::class, 'index'])->name('categorias');
    Route::post('/categorias', [Admin\CategoriaController::class, 'store'])->name('categorias.store');
    Route::put('/categorias/{categoria}', [Admin\CategoriaController::class, 'update'])->name('categorias.update');
    Route::delete('/categorias/{categoria}', [Admin\CategoriaController::class, 'destroy'])->name('categorias.destroy');

    // Productos CRUD
    Route::get('/productos', [Admin\ProductoController::class, 'index'])->name('productos');
    Route::post('/productos', [Admin\ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{producto}', [Admin\ProductoController::class, 'update'])->name('productos.update');
    Route::patch('/productos/{producto}/toggle', [Admin\ProductoController::class, 'toggleStatus'])->name('productos.toggle');
    Route::delete('/productos/{producto}', [Admin\ProductoController::class, 'destroy'])->name('productos.destroy');

    // Compras (Historial, Registrar y Eliminar)
    Route::get('/compras', [Admin\CompraController::class, 'index'])->name('compras');
    Route::post('/compras', [Admin\CompraController::class, 'store'])->name('compras.store');
    Route::delete('/compras/{compra}', [Admin\CompraController::class, 'destroy'])->name('compras.destroy');

    // Ventas (Historial, Registrar, Eliminar y Factura POS)
    Route::get('/ventas', [Admin\VentaController::class, 'index'])->name('ventas');
    Route::post('/ventas', [Admin\VentaController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/{venta}/factura', [Admin\VentaController::class, 'factura'])->name('ventas.factura');
    Route::get('/ventas/{venta}/factura-pdf', [Admin\VentaController::class, 'facturaPdf'])->name('ventas.factura.pdf');
    Route::delete('/ventas/{venta}', [Admin\VentaController::class, 'destroy'])->name('ventas.destroy');

    // Ganancias y Rentabilidad
    Route::get('/ganancias', [Admin\GananciaController::class, 'index'])->name('ganancias');
});

// Rutas del Panel Vendedor (Protegidas por Auth y Rol Vendedor)
Route::middleware(['auth', 'role:vendedor'])->prefix('vendedor')->name('vendedor.')->group(function () {
    Route::get('/dashboard', [Vendedor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/productos', [Vendedor\ProductoController::class, 'index'])->name('productos');

    // Compras Vendedor
    Route::get('/compras', [Vendedor\CompraController::class, 'index'])->name('compras');
    Route::post('/compras', [Vendedor\CompraController::class, 'store'])->name('compras.store');
    Route::delete('/compras/{compra}', [Vendedor\CompraController::class, 'destroy'])->name('compras.destroy');

    // Ventas Vendedor
    Route::get('/ventas', [Vendedor\VentaController::class, 'index'])->name('ventas');
    Route::post('/ventas', [Vendedor\VentaController::class, 'store'])->name('ventas.store');
    Route::get('/ventas/{venta}/factura', [Vendedor\VentaController::class, 'factura'])->name('ventas.factura');
    Route::get('/ventas/{venta}/factura-pdf', [Vendedor\VentaController::class, 'facturaPdf'])->name('ventas.factura.pdf');
    Route::delete('/ventas/{venta}', [Vendedor\VentaController::class, 'destroy'])->name('ventas.destroy');

    // Ganancias Vendedor
    Route::get('/ganancias', [Vendedor\GananciaController::class, 'index'])->name('ganancias');
});
