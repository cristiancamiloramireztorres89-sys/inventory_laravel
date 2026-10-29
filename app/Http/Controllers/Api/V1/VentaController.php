<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\VentaResource;
use App\Models\Cliente;
use App\Models\DetalleCompra;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VentaController extends BaseApiController
{
    /**
     * Listado del historial de ventas para la app de Flutter.
     * Vendedor solo ve las suyas; Administrador ve todas.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $query = Venta::with(['cliente', 'usuario', 'detalles.producto'])
            ->orderBy('fecha', 'desc');

        // Trazabilidad y aislamiento por rol
        if (! $usuario->esAdmin()) {
            $query->where('id_usuario', $usuario->id_usuario);
        } elseif ($request->filled('id_usuario')) {
            $query->where('id_usuario', $request->id_usuario);
        }

        // Filtro por fecha (opcional)
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        // Paginación si se especifica
        if ($request->has('page')) {
            $perPage = min((int) $request->input('per_page', 15), 100);
            $ventas = $query->paginate($perPage);

            return $this->sendResponse([
                'items' => VentaResource::collection($ventas->items()),
                'paginacion' => [
                    'total' => $ventas->total(),
                    'por_pagina' => $ventas->perPage(),
                    'pagina_actual' => $ventas->currentPage(),
                    'ultima_pagina' => $ventas->lastPage(),
                    'tiene_mas_paginas' => $ventas->hasMorePages(),
                ],
            ], 'Historial de ventas paginado.');
        }

        $ventas = $query->take(50)->get();

        return $this->sendResponse(
            VentaResource::collection($ventas),
            'Historial de ventas recuperado exitosamente.'
        );
    }

    /**
     * Detalle completo de una venta específica.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $venta = Venta::with(['cliente', 'usuario', 'detalles.producto'])->find($id);

        if (! $venta) {
            return $this->sendError('La venta solicitada no existe.', [], 404);
        }

        // Control de acceso: un vendedor no puede espiar ventas ajenas
        if (! $usuario->esAdmin() && $venta->id_usuario !== $usuario->id_usuario) {
            return $this->sendError('No tienes permiso para ver esta venta.', [], 403);
        }

        return $this->sendResponse(
            new VentaResource($venta),
            'Detalle de la venta recuperado.'
        );
    }

    /**
     * Registrar una nueva venta desde la app Flutter (Multi-producto POS).
     */
    public function store(Request $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        // 1. Validación de la solicitud
        $validator = Validator::make($request->all(), [
            'id_cliente' => ['nullable', 'integer', 'exists:clientes,id_cliente'],
            'nuevo_cliente' => ['nullable', 'array'],
            'nuevo_cliente.nombre' => ['required_with:nuevo_cliente', 'string', 'max:100'],
            'nuevo_cliente.telefono' => ['nullable', 'string', 'max:20'],
            'nuevo_cliente.correo' => ['nullable', 'email', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_producto' => ['required', 'integer', 'exists:productos,id_producto'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'items.*.precio_unitario' => ['nullable', 'numeric', 'min:0'],
        ], [
            'items.required' => 'Debes enviar al menos un producto para registrar la venta.',
            'items.*.id_producto.required' => 'Cada artículo debe tener un ID de producto válido.',
            'items.*.cantidad.min' => 'La cantidad mínima por producto es 1.',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Errores de validación', $validator->errors()->toArray(), 422);
        }

        // 2. Resolver el cliente (existente o nuevo)
        $idCliente = $request->input('id_cliente');

        if (! $idCliente && $request->filled('nuevo_cliente.nombre')) {
            $clienteData = $request->input('nuevo_cliente');
            $cliente = Cliente::create([
                'nombre' => trim($clienteData['nombre']),
                'telefono' => ! empty($clienteData['telefono']) ? trim($clienteData['telefono']) : null,
                'correo' => ! empty($clienteData['correo']) ? trim($clienteData['correo']) : null,
            ]);
            $idCliente = $cliente->id_cliente;
        }

        if (! $idCliente) {
            return $this->sendError('Debes seleccionar un cliente existente o suministrar datos de un cliente nuevo.', [], 422);
        }

        // 3. Validar disponibilidad de stock de todos los artículos antes de tocar la BD
        $itemsValidados = [];
        $totalVenta = 0.0;

        foreach ($request->items as $item) {
            $producto = Producto::find($item['id_producto']);

            if (! $producto || ! $producto->estaActivo()) {
                return $this->sendError("El producto ID {$item['id_producto']} no se encuentra activo para venta.", [], 422);
            }

            $cantidad = (int) $item['cantidad'];
            if ($producto->stock_actual < $cantidad) {
                return $this->sendError("Stock insuficiente para '{$producto->nombre}'. Disponibles: {$producto->stock_actual}, Solicitadas: {$cantidad}.", [], 422);
            }

            // Usar el precio unitario suministrado o el precio_venta registrado del catálogo
            $precioUnitario = isset($item['precio_unitario']) && (float) $item['precio_unitario'] > 0
                ? (float) $item['precio_unitario']
                : (float) $producto->precio_venta;

            $subtotalItem = round($cantidad * $precioUnitario, 2);
            $totalVenta += $subtotalItem;

            $itemsValidados[] = [
                'producto' => $producto,
                'id_producto' => $producto->id_producto,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotalItem,
            ];
        }

        // 4. Ejecución atómica de la transacción
        try {
            $nuevaVenta = DB::transaction(function () use ($usuario, $idCliente, $totalVenta, $itemsValidados) {
                $venta = Venta::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_cliente' => $idCliente,
                    'fecha' => now(),
                    'subtotal' => $totalVenta,
                    'iva' => 0.0,
                    'total' => $totalVenta,
                ]);

                foreach ($itemsValidados as $item) {
                    $costoUnitario = DetalleCompra::where('id_producto', $item['id_producto'])
                        ->latest('id_detalle_compra')
                        ->value('precio_unitario') ?? 0.00;

                    DetalleVenta::create([
                        'id_venta' => $venta->id_venta,
                        'id_producto' => $item['id_producto'],
                        'cantidad' => $item['cantidad'],
                        'costo_unitario' => $costoUnitario,
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // Descontar inventario en tiempo real
                    $item['producto']->decrement('stock_actual', $item['cantidad']);
                }

                return $venta;
            });

            $nuevaVenta->load(['usuario', 'cliente', 'detalles.producto']);

            return $this->sendResponse(
                new VentaResource($nuevaVenta),
                'Venta registrada exitosamente con stock actualizado.',
                201
            );
        } catch (\Throwable $e) {
            return $this->sendError('Ocurrió un error al procesar la venta: ' . $e->getMessage(), [], 500);
        }
    }
}
