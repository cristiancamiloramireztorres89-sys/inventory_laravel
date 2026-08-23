<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mostrar el panel de control del Vendedor con sus propias métricas y accesos rápidos.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        // 1. Métricas de Ventas del Vendedor
        $ventas = Venta::with(['cliente', 'detalles.producto.detalleCompras'])
            ->where('id_usuario', $userId)
            ->get();

        $misVentas    = $ventas->count();
        $miFacturado  = (float) $ventas->sum('total');
        $miCosto      = (float) $ventas->sum(fn ($v) => $v->costo_total);
        $miGanancia   = $miFacturado - $miCosto;
        $miMargen     = $miCosto > 0 ? round(($miGanancia / $miCosto) * 100, 1) : ($miFacturado > 0 ? 100.0 : 0.0);

        // 2. Compras registradas por el vendedor
        $misCompras      = Compra::where('id_usuario', $userId)->count();
        $miTotalCompras  = (float) Compra::where('id_usuario', $userId)->sum('total');

        // 3. Catálogo disponible
        $totalProductos     = Producto::activos()->count();
        $totalStockUnidades = Producto::activos()->sum('stock_actual');

        // 4. Últimas ventas propias
        $ultimasVentas = Venta::with(['cliente', 'detalles.producto'])
            ->where('id_usuario', $userId)
            ->orderBy('id_venta', 'desc')
            ->take(5)
            ->get();

        // 5. Productos activos destacados
        $productosDestacados = Producto::activos()
            ->with('categoria')
            ->orderBy('stock_actual', 'desc')
            ->take(5)
            ->get();

        return view('vendedor.dashboard', compact(
            'misVentas',
            'miFacturado',
            'miGanancia',
            'miMargen',
            'misCompras',
            'miTotalCompras',
            'totalProductos',
            'totalStockUnidades',
            'ultimasVentas',
            'productosDestacados'
        ));
    }
}
