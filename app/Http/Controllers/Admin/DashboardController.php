<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Compra;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mostrar el panel de control del Administrador con métricas globales ejecutivas.
     */
    public function index(Request $request): View
    {
        // 1. Métricas de Usuarios y Catálogo
        $totalUsuarios      = Usuario::count();
        $totalProductos     = Producto::count();
        $totalStockUnidades = Producto::sum('stock_actual');
        $totalCategorias    = Categoria::count();

        // 2. Métricas Financieras de Ventas y Ganancias
        $ventas = Venta::with(['usuario', 'cliente', 'detalles.producto.detalleCompras'])->get();
        $totalVentas     = $ventas->count();
        $totalRecaudado  = (float) $ventas->sum('total');
        $totalCosto      = (float) $ventas->sum(fn ($v) => $v->costo_total);
        $gananciaTotal   = $totalRecaudado - $totalCosto;
        $margenGlobal    = $totalCosto > 0 ? round(($gananciaTotal / $totalCosto) * 100, 1) : ($totalRecaudado > 0 ? 100.0 : 0.0);

        // 3. Métricas de Compras
        $totalCompras   = Compra::count();
        $totalInvertido = (float) Compra::sum('total');

        // 4. Alertas de Stock Bajo
        $productosStockBajo = Producto::with('categoria')
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('stock_actual', 'asc')
            ->take(5)
            ->get();
        $stockBajoCount = $productosStockBajo->count();

        // 5. Últimas Ventas
        $ultimasVentas = Venta::with(['usuario', 'cliente', 'detalles.producto'])
            ->orderBy('id_venta', 'desc')
            ->take(5)
            ->get();

        // 6. Últimas Compras
        $ultimasCompras = Compra::with(['usuario', 'proveedor', 'detalles.producto'])
            ->orderBy('id_compra', 'desc')
            ->take(5)
            ->get();

        // 7. Top 5 Productos Más Rentables
        $detalles = DetalleVenta::with('producto.detalleCompras')->get();
        $topProductos = $detalles->groupBy('id_producto')->map(function ($items) {
            $primerItem = $items->first();
            $productoNombre = $primerItem->producto->nombre ?? 'Producto';
            $cantVendida = $items->sum('cantidad');
            $ingresos = $items->sum('subtotal');
            $costo = $items->sum(fn ($i) => $i->costo_total);
            $ganancia = $ingresos - $costo;

            return [
                'nombre'   => $productoNombre,
                'cantidad' => $cantVendida,
                'ingresos' => $ingresos,
                'costo'    => $costo,
                'ganancia' => $ganancia,
                'margen'   => $costo > 0 ? round(($ganancia / $costo) * 100, 1) : 100.0,
            ];
        })->sortByDesc('ganancia')->take(5);

        return view('admin.dashboardadmin', compact(
            'totalUsuarios',
            'totalProductos',
            'totalStockUnidades',
            'totalCategorias',
            'totalVentas',
            'totalRecaudado',
            'totalCosto',
            'gananciaTotal',
            'margenGlobal',
            'totalCompras',
            'totalInvertido',
            'stockBajoCount',
            'productosStockBajo',
            'ultimasVentas',
            'ultimasCompras',
            'topProductos'
        ));
    }
}
