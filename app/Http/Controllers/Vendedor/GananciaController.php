<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GananciaController extends Controller
{
    /**
     * Mostrar el panel de análisis de ganancias generadas exclusivamente por el Vendedor autenticado.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        $ventas = Venta::with(['cliente', 'detalles.producto.detalleCompras'])
            ->where('id_usuario', $userId)
            ->orderBy('id_venta', 'desc')
            ->get();

        $totalIngresos = (float) $ventas->sum('total');
        $totalCosto    = (float) $ventas->sum(fn ($v) => $v->costo_total);
        $gananciaTotal = $totalIngresos - $totalCosto;
        $margenGlobal  = $totalCosto > 0 ? round(($gananciaTotal / $totalCosto) * 100, 1) : ($totalIngresos > 0 ? 100.0 : 0.0);

        // Top productos vendidos por este usuario con mayor ganancia
        $detalles = DetalleVenta::whereHas('venta', fn ($q) => $q->where('id_usuario', $userId))
            ->with('producto.detalleCompras')
            ->get();

        $topProductos = $detalles->groupBy('id_producto')->map(function ($items) {
            $primerItem = $items->first();
            $productoNombre = $primerItem->producto->nombre ?? 'Producto';
            $cantVendida = $items->sum('cantidad');
            $ingresosTotales = $items->sum('subtotal');
            $costoTotal = $items->sum(fn ($i) => $i->costo_total);
            $ganancia = $ingresosTotales - $costoTotal;

            return [
                'nombre'           => $productoNombre,
                'cantidad'         => $cantVendida,
                'ingresos'         => $ingresosTotales,
                'costo'            => $costoTotal,
                'ganancia'         => $ganancia,
                'margen'           => $costoTotal > 0 ? round(($ganancia / $costoTotal) * 100, 1) : 100.0,
            ];
        })->sortByDesc('ganancia')->take(5);

        return view('vendedor.ganancias', compact(
            'ventas',
            'totalIngresos',
            'totalCosto',
            'gananciaTotal',
            'margenGlobal',
            'topProductos'
        ));
    }
}
