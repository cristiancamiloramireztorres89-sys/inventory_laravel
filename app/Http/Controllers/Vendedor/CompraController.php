<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompraController extends Controller
{
    /**
     * Listar única y exclusivamente las compras registradas por el Vendedor autenticado.
     */
    public function index(Request $request): View
    {
        $compras = Compra::with(['proveedor', 'detalles.producto'])
            ->where('id_usuario', Auth::id())
            ->orderBy('id_compra', 'desc')
            ->get();

        $totalCompras   = $compras->count();
        $totalInvertido = $compras->sum('total');

        $proveedores = Proveedor::orderBy('nombre', 'asc')->get();
        $productos   = Producto::orderBy('nombre', 'asc')->get();

        return view('vendedor.compras', compact('compras', 'totalCompras', 'totalInvertido', 'proveedores', 'productos'));
    }

    /**
     * Registrar una compra por el Vendedor.
     * Permite seleccionar un proveedor existente o registrar uno nuevo al instante con nombre, teléfono y correo.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Determinar el proveedor (existente o nuevo)
        if ($request->input('id_proveedor') === 'nuevo' || !empty($request->input('nuevo_proveedor_nombre'))) {
            $request->validate([
                'nuevo_proveedor_nombre'   => ['required', 'string', 'max:100'],
                'nuevo_proveedor_telefono' => ['nullable', 'string', 'max:50'],
                'nuevo_proveedor_correo'   => ['nullable', 'email', 'max:100'],
            ], [
                'nuevo_proveedor_nombre.required' => 'Debes ingresar el nombre del proveedor.',
                'nuevo_proveedor_correo.email'    => 'Ingresa un formato de correo electrónico válido.',
            ]);

            $proveedor = Proveedor::create([
                'nombre'   => trim($request->input('nuevo_proveedor_nombre')),
                'telefono' => $request->input('nuevo_proveedor_telefono'),
                'correo'   => $request->input('nuevo_proveedor_correo'),
            ]);

            $idProveedor = $proveedor->id_proveedor;
        } else {
            $request->validate([
                'id_proveedor' => ['required', 'integer', 'exists:proveedores,id_proveedor'],
            ], [
                'id_proveedor.required' => 'Debes seleccionar un proveedor o escribir uno nuevo.',
            ]);

            $idProveedor = (int) $request->input('id_proveedor');
        }

        // 2. Validar producto, cantidad y costo unitario
        $validated = $request->validate([
            'id_producto'     => ['required', 'integer', 'exists:productos,id_producto'],
            'cantidad'        => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['required', 'numeric', 'min:0.01'],
        ], [
            'id_producto.required'     => 'Debes seleccionar un producto.',
            'cantidad.required'        => 'Ingresa la cantidad a comprar.',
            'cantidad.min'             => 'La cantidad mínima es 1 unidad.',
            'precio_unitario.required' => 'Ingresa el costo unitario.',
            'precio_unitario.min'      => 'El costo unitario debe ser mayor a 0.',
        ]);

        $subtotal = $validated['cantidad'] * $validated['precio_unitario'];

        DB::transaction(function () use ($idProveedor, $validated, $subtotal) {
            $compra = Compra::create([
                'id_usuario'   => Auth::id(),
                'id_proveedor' => $idProveedor,
                'fecha'        => now(),
                'subtotal'     => $subtotal,
                'iva'          => 0,
                'total'        => $subtotal,
            ]);

            DetalleCompra::create([
                'id_compra'       => $compra->id_compra,
                'id_producto'     => $validated['id_producto'],
                'cantidad'        => $validated['cantidad'],
                'precio_unitario' => $validated['precio_unitario'],
                'subtotal'        => $subtotal,
            ]);

            $producto = Producto::find($validated['id_producto']);
            $producto->increment('stock_actual', $validated['cantidad']);
        });

        return redirect()->route('vendedor.compras')
            ->with('success', 'Compra registrada exitosamente.');
    }

    /**
     * Eliminar una compra propia del vendedor y descontar las existencias añadidas.
     */
    public function destroy(Compra $compra): RedirectResponse
    {
        if ($compra->id_usuario !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar esta compra.');
        }

        DB::transaction(function () use ($compra) {
            foreach ($compra->detalles as $detalle) {
                if ($detalle->producto) {
                    $detalle->producto->decrement('stock_actual', $detalle->cantidad);
                }
            }

            $compra->delete();
        });

        return redirect()->route('vendedor.compras')
            ->with('success', 'Compra eliminada exitosamente y stock ajustado en el inventario.');
    }
}
