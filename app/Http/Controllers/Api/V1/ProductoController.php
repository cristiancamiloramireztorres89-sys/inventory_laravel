<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends BaseApiController
{
    /**
     * Listado de productos para el catálogo de Flutter.
     * Permite filtrado por búsqueda predictiva, categoría y stock.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Producto::with('categoria')->where('activo', true);

        // Búsqueda por texto (nombre, marca o descripción)
        if ($request->filled('buscar')) {
            $busqueda = trim($request->buscar);
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('marca', 'like', "%{$busqueda}%")
                  ->orWhere('descripcion', 'like', "%{$busqueda}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('id_categoria')) {
            $query->where('id_categoria', $request->id_categoria);
        }

        // Filtro por productos con stock crítico/bajo
        if ($request->boolean('bajo_stock')) {
            $query->whereColumn('stock_actual', '<=', 'stock_minimo');
        }

        // Ordenamiento
        $orden = $request->input('orden', 'nombre');
        $direccion = $request->input('direccion', 'asc');
        if (in_array($orden, ['nombre', 'precio_venta', 'stock_actual'])) {
            $query->orderBy($orden, $direccion === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('nombre', 'asc');
        }

        // Paginación o lista completa según parámetro per_page
        if ($request->has('page')) {
            $perPage = min((int) $request->input('per_page', 15), 100);
            $productos = $query->paginate($perPage);

            return $this->sendResponse([
                'items' => ProductoResource::collection($productos->items()),
                'paginacion' => [
                    'total' => $productos->total(),
                    'por_pagina' => $productos->perPage(),
                    'pagina_actual' => $productos->currentPage(),
                    'ultima_pagina' => $productos->lastPage(),
                    'tiene_mas_paginas' => $productos->hasMorePages(),
                ],
            ], 'Listado de productos paginado.');
        }

        $productos = $query->get();

        return $this->sendResponse(
            ProductoResource::collection($productos),
            'Catálogo de productos obtenido correctamente.'
        );
    }

    /**
     * Detalle de un producto individual por ID.
     */
    public function show(int $id): JsonResponse
    {
        $producto = Producto::with('categoria')->find($id);

        if (! $producto) {
            return $this->sendError('El producto solicitado no fue encontrado.', [], 404);
        }

        return $this->sendResponse(
            new ProductoResource($producto),
            'Detalle del producto obtenido con éxito.'
        );
    }

    /**
     * Verificar disponibilidad de stock para el carrito de compras en Flutter.
     */
    public function verificarStock(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_producto' => ['required', 'integer', 'exists:productos,id_producto'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
        ]);

        $resultados = [];
        $hayDisponibilidad = true;

        foreach ($request->items as $item) {
            $producto = Producto::find($item['id_producto']);
            $suficiente = $producto && ($producto->stock_actual >= $item['cantidad']);

            if (! $suficiente) {
                $hayDisponibilidad = false;
            }

            $resultados[] = [
                'id_producto' => $producto->id_producto,
                'nombre' => $producto->nombre,
                'stock_disponible' => $producto->stock_actual,
                'cantidad_solicitada' => (int) $item['cantidad'],
                'disponible' => $suficiente,
            ];
        }

        return $this->sendResponse([
            'disponibilidad_total' => $hayDisponibilidad,
            'items' => $resultados,
        ], $hayDisponibilidad ? 'Todos los productos tienen existencias suficientes.' : 'Uno o más productos no cuentan con stock suficiente.');
    }
}
