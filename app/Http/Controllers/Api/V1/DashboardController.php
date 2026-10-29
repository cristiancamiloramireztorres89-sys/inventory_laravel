<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseApiController
{
    /**
     * Resumen de métricas y estadísticas clave para la pantalla inicial de Flutter.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $totalProductos = Producto::where('activo', true)->count();
        $productosBajoStock = Producto::where('activo', true)
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->count();
        $totalCategorias = Categoria::count();

        if ($usuario->esAdmin()) {
            $totalVentas = Venta::count();
            $ventasHoy = Venta::whereDate('fecha', today())->count();
            $ingresosTotales = (float) Venta::sum('total');
            $ingresosHoy = (float) Venta::whereDate('fecha', today())->sum('total');

            return $this->sendResponse([
                'rol' => 'administrador',
                'metricas' => [
                    'total_productos' => $totalProductos,
                    'productos_bajo_stock' => $productosBajoStock,
                    'total_categorias' => $totalCategorias,
                    'total_ventas' => $totalVentas,
                    'ventas_hoy' => $ventasHoy,
                    'ingresos_totales' => $ingresosTotales,
                    'ingresos_hoy' => $ingresosHoy,
                ],
            ], 'Resumen general del dashboard administrativo.');
        }

        // Métricas específicas para vendedor
        $ventasPropias = Venta::where('id_usuario', $usuario->id_usuario)->count();
        $ventasPropiasHoy = Venta::where('id_usuario', $usuario->id_usuario)
            ->whereDate('fecha', today())
            ->count();
        $ingresosPropios = (float) Venta::where('id_usuario', $usuario->id_usuario)->sum('total');
        $ingresosPropiosHoy = (float) Venta::where('id_usuario', $usuario->id_usuario)
            ->whereDate('fecha', today())
            ->sum('total');

        return $this->sendResponse([
            'rol' => 'vendedor',
            'metricas' => [
                'total_productos' => $totalProductos,
                'productos_bajo_stock' => $productosBajoStock,
                'total_categorias' => $totalCategorias,
                'mis_ventas_totales' => $ventasPropias,
                'mis_ventas_hoy' => $ventasPropiasHoy,
                'mis_ingresos_totales' => $ingresosPropios,
                'mis_ingresos_hoy' => $ingresosPropiosHoy,
            ],
        ], 'Resumen general del dashboard del vendedor.');
    }
}
