<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\CompraResource;
use App\Http\Resources\Api\V1\ProveedorResource;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompraController extends BaseApiController
{
    /**
     * Listado del historial de compras.
     * Vendedor ve solo sus compras; Administrador ve todas.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $query = Compra::with(['proveedor', 'usuario', 'detalles.producto'])
            ->orderBy('id_compra', 'desc');

        if (! $usuario->esAdmin()) {
            $query->where('id_usuario', $usuario->id_usuario);
        } elseif ($request->filled('id_usuario')) {
            $query->where('id_usuario', $request->id_usuario);
        }

        $compras = $query->take(50)->get();

        return $this->sendResponse(
            CompraResource::collection($compras),
            'Historial de compras recuperado exitosamente.'
        );
    }

    /**
     * Detalle de una compra específica.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $compra = Compra::with(['proveedor', 'usuario', 'detalles.producto'])->find($id);

        if (! $compra) {
            return $this->sendError('La orden de compra solicitada no existe.', [], 404);
        }

        if (! $usuario->esAdmin() && $compra->id_usuario !== $usuario->id_usuario) {
            return $this->sendError('No tienes permiso para ver esta orden de compra.', [], 403);
        }

        return $this->sendResponse(
            new CompraResource($compra),
            'Detalle de la compra recuperado.'
        );
    }

    /**
     * Registrar una nueva orden de compra desde la app Flutter.
     */
    public function store(Request $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();

        $validator = Validator::make($request->all(), [
            'id_proveedor' => ['nullable'],
            'nuevo_proveedor' => ['nullable', 'array'],
            'nuevo_proveedor.nombre' => ['nullable', 'string', 'max:100'],
            'nuevo_proveedor.telefono' => ['nullable', 'string', 'max:50'],
            'nuevo_proveedor.correo' => ['nullable', 'email', 'max:100'],
            'proveedor_nombre' => ['nullable', 'string', 'max:100'],
            'id_producto' => ['required_without:items', 'nullable', 'integer', 'exists:productos,id_producto'],
            'cantidad' => ['required_without:items', 'nullable', 'integer', 'min:1'],
            'precio_unitario' => ['required_without:items', 'nullable', 'numeric', 'min:0'],
            'items' => ['nullable', 'array'],
            'items.*.id_producto' => ['required_with:items', 'integer', 'exists:productos,id_producto'],
            'items.*.cantidad' => ['required_with:items', 'integer', 'min:1'],
            'items.*.precio_unitario' => ['required_with:items', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Errores de validación', $validator->errors()->toArray(), 422);
        }

        // Resolver Proveedor (existente o nuevo)
        $idProveedor = null;
        if ($request->filled('id_proveedor') && is_numeric($request->input('id_proveedor'))) {
            $idProveedor = (int) $request->input('id_proveedor');
        } elseif ($request->filled('nuevo_proveedor.nombre')) {
            $provData = $request->input('nuevo_proveedor');
            $proveedor = Proveedor::firstOrCreate(
                ['nombre' => trim($provData['nombre'])],
                [
                    'telefono' => ! empty($provData['telefono']) ? trim($provData['telefono']) : null,
                    'correo' => ! empty($provData['correo']) ? trim($provData['correo']) : null,
                ]
            );
            $idProveedor = $proveedor->id_proveedor;
        } elseif ($request->filled('proveedor_nombre')) {
            $proveedor = Proveedor::firstOrCreate(
                ['nombre' => trim($request->input('proveedor_nombre'))],
                ['telefono' => null, 'correo' => null]
            );
            $idProveedor = $proveedor->id_proveedor;
        }

        if (! $idProveedor) {
            $primerProveedor = Proveedor::first();
            $idProveedor = $primerProveedor ? $primerProveedor->id_proveedor : 1;
        }

        // Normalizar items a comprar
        $items = [];
        if ($request->has('items') && is_array($request->items) && count($request->items) > 0) {
            $items = $request->items;
        } else {
            $items[] = [
                'id_producto' => (int) $request->input('id_producto'),
                'cantidad' => (int) $request->input('cantidad'),
                'precio_unitario' => (float) $request->input('precio_unitario'),
            ];
        }

        try {
            $compra = DB::transaction(function () use ($usuario, $idProveedor, $items) {
                $subtotalTotal = 0;
                foreach ($items as $item) {
                    $subtotalTotal += round($item['cantidad'] * $item['precio_unitario'], 2);
                }

                $compra = Compra::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_proveedor' => $idProveedor,
                    'fecha' => now(),
                    'subtotal' => $subtotalTotal,
                    'iva' => 0,
                    'total' => $subtotalTotal,
                ]);

                foreach ($items as $item) {
                    $cant = (int) $item['cantidad'];
                    $costo = (float) $item['precio_unitario'];
                    $sub = round($cant * $costo, 2);

                    DetalleCompra::create([
                        'id_compra' => $compra->id_compra,
                        'id_producto' => $item['id_producto'],
                        'cantidad' => $cant,
                        'precio_unitario' => $costo,
                        'subtotal' => $sub,
                    ]);

                    $producto = Producto::find($item['id_producto']);
                    if ($producto) {
                        $producto->increment('stock_actual', $cant);
                    }
                }

                return $compra->load(['proveedor', 'detalles.producto']);
            });

            return $this->sendResponse(
                new CompraResource($compra),
                'Compra registrada exitosamente en el sistema.'
            );
        } catch (\Exception $e) {
            return $this->sendError('Error al registrar la compra: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Listado de proveedores disponibles para abastecimiento.
     */
    public function proveedores(): JsonResponse
    {
        $proveedores = Proveedor::orderBy('nombre', 'asc')->get();
        return $this->sendResponse(
            ProveedorResource::collection($proveedores),
            'Proveedores recuperados exitosamente.'
        );
    }
}