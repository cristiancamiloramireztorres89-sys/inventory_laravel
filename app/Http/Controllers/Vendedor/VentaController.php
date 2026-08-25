<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Mail\FacturaVentaMail;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VentaController extends Controller
{
    /**
     * Listar única y exclusivamente las ventas realizadas por el Vendedor autenticado.
     */
    public function index(Request $request): View
    {
        $ventas = Venta::with(['cliente', 'detalles.producto'])
            ->where('id_usuario', Auth::id())
            ->orderBy('id_venta', 'desc')
            ->get();

        $totalVentas = $ventas->count();
        $totalDinero = $ventas->sum('total');

        $clientes  = Cliente::orderBy('nombre', 'asc')->get();
        $productos = Producto::activos()->orderBy('nombre', 'asc')->get();

        return view('vendedor.ventas', compact('ventas', 'totalVentas', 'totalDinero', 'clientes', 'productos'));
    }

    /**
     * Registrar una nueva venta en el mostrador (uno o múltiples productos).
     * Permite seleccionar un cliente existente o agregar uno nuevo con nombre, teléfono y correo.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Determinar el cliente (existente o nuevo)
        if ($request->input('id_cliente') === 'nuevo' || !empty($request->input('nuevo_cliente_nombre'))) {
            $request->validate([
                'nuevo_cliente_nombre'   => ['required', 'string', 'max:100'],
                'nuevo_cliente_telefono' => ['nullable', 'string', 'max:50'],
                'nuevo_cliente_correo'   => ['nullable', 'email', 'max:100'],
            ], [
                'nuevo_cliente_nombre.required' => 'Debes ingresar el nombre del cliente.',
                'nuevo_cliente_correo.email'    => 'Ingresa un formato de correo electrónico válido.',
            ]);

            $cliente = Cliente::create([
                'nombre'   => trim($request->input('nuevo_cliente_nombre')),
                'telefono' => $request->input('nuevo_cliente_telefono'),
                'correo'   => $request->input('nuevo_cliente_correo'),
            ]);

            $idCliente = $cliente->id_cliente;
        } else {
            $request->validate([
                'id_cliente' => ['required', 'integer', 'exists:clientes,id_cliente'],
            ], [
                'id_cliente.required' => 'Debes seleccionar un cliente o escribir uno nuevo.',
            ]);

            $idCliente = (int) $request->input('id_cliente');
        }

        // 2. Extraer y estructurar los artículos a vender (Multi-producto)
        $items = [];
        if ($request->has('items') && is_array($request->input('items'))) {
            $items = $request->input('items');
        } elseif ($request->filled('items_json')) {
            $items = json_decode($request->input('items_json'), true) ?? [];
        } elseif ($request->filled('id_producto')) {
            $items = [[
                'id_producto'     => $request->input('id_producto'),
                'cantidad'        => $request->input('cantidad', 1),
                'precio_unitario' => $request->input('precio_unitario'),
            ]];
        }

        if (empty($items)) {
            return back()->withInput()->with('error', 'Debes agregar al menos un producto a la venta.');
        }

        // 3. Validar productos, cantidades y stock disponible
        $itemsValidados = [];
        $totalVenta = 0;

        foreach ($items as $item) {
            $idProd = (int) ($item['id_producto'] ?? 0);
            $cant   = (int) ($item['cantidad'] ?? 0);
            $precio = (float) ($item['precio_unitario'] ?? 0);

            if ($idProd <= 0 || $cant <= 0 || $precio <= 0) {
                return back()->withInput()->with('error', 'Uno de los productos seleccionados tiene datos inválidos.');
            }

            $producto = Producto::find($idProd);
            if (!$producto) {
                return back()->withInput()->with('error', 'Uno de los productos seleccionados no existe o fue eliminado.');
            }

            if ($producto->stock_actual < $cant) {
                return back()->withInput()->with('error', "Stock insuficiente para '{$producto->nombre}'. Existencias disponibles: {$producto->stock_actual} unidades (solicitadas: {$cant}).");
            }

            $subtotalItem = $cant * $precio;
            $totalVenta += $subtotalItem;

            $itemsValidados[] = [
                'producto'        => $producto,
                'id_producto'     => $producto->id_producto,
                'cantidad'        => $cant,
                'precio_unitario' => $precio,
                'subtotal'        => $subtotalItem,
            ];
        }

        // 4. Registrar en Base de Datos de manera atómica
        $nuevaVenta = DB::transaction(function () use ($idCliente, $itemsValidados, $totalVenta) {
            $venta = Venta::create([
                'id_usuario' => Auth::id(),
                'id_cliente' => $idCliente,
                'fecha'      => now(),
                'subtotal'   => $totalVenta,
                'iva'        => 0,
                'total'      => $totalVenta,
            ]);

            foreach ($itemsValidados as $item) {
                $costoUnitario = \App\Models\DetalleCompra::where('id_producto', $item['id_producto'])
                    ->latest('id_detalle_compra')
                    ->value('precio_unitario') ?? 0.00;

                DetalleVenta::create([
                    'id_venta'        => $venta->id_venta,
                    'id_producto'     => $item['id_producto'],
                    'cantidad'        => $item['cantidad'],
                    'costo_unitario'  => $costoUnitario,
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal'        => $item['subtotal'],
                ]);

                $item['producto']->decrement('stock_actual', $item['cantidad']);
            }

            return $venta;
        });

        $nuevaVenta->load(['usuario', 'cliente', 'detalles.producto']);

        $totalArticulos = count($itemsValidados);
        $totalUnidades  = array_sum(array_column($itemsValidados, 'cantidad'));
        $mensajeExito   = "Venta registrada exitosamente con {$totalArticulos} producto(s) ({$totalUnidades} unidades) y stock actualizado.";

        // Si el cliente tiene un correo electrónico válido, enviar la factura automáticamente
        if ($nuevaVenta->cliente && !empty($nuevaVenta->cliente->correo) && filter_var($nuevaVenta->cliente->correo, FILTER_VALIDATE_EMAIL)) {
            try {
                $pdfContent = Pdf::loadView('ventas.factura_pos_pdf', ['venta' => $nuevaVenta])
                    ->setPaper([0, 0, 226.77, 550], 'portrait')
                    ->output();

                Mail::to($nuevaVenta->cliente->correo)
                    ->send(new FacturaVentaMail($nuevaVenta, $pdfContent));

                $mensajeExito .= " Factura POS enviada al correo {$nuevaVenta->cliente->correo}.";
            } catch (\Throwable $e) {
                // Si ocurre algún detalle de red, no bloqueamos la venta registrada
            }
        }

        return redirect()->route('vendedor.ventas')
            ->with('success', $mensajeExito)
            ->with('venta_creada_id', $nuevaVenta->id_venta);
    }

    /**
     * Mostrar la vista HTML del comprobante / ticket POS de 80mm listo para imprimir.
     */
    public function factura(Venta $venta): View
    {
        if ($venta->id_usuario !== Auth::id()) {
            abort(403, 'No tienes permiso para ver el comprobante de esta venta.');
        }

        $venta->load(['usuario', 'cliente', 'detalles.producto']);

        return view('ventas.factura_pos', compact('venta'));
    }

    /**
     * Generar y visualizar el ticket tipo POS en PDF con DomPDF.
     */
    public function facturaPdf(Venta $venta)
    {
        if ($venta->id_usuario !== Auth::id()) {
            abort(403, 'No tienes permiso para generar el PDF de esta venta.');
        }

        $venta->load(['usuario', 'cliente', 'detalles.producto']);

        $customPaper = [0, 0, 226.77, 550];
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ventas.factura_pos_pdf', compact('venta'))
            ->setPaper($customPaper, 'portrait');

        return $pdf->stream('Factura_POS_' . str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) . '.pdf');
    }

    /**
     * Eliminar una venta propia del vendedor y restaurar el stock al inventario.
     */
    public function destroy(Venta $venta): RedirectResponse
    {
        if ($venta->id_usuario !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar esta venta.');
        }

        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) {
                if ($detalle->producto) {
                    $detalle->producto->increment('stock_actual', $detalle->cantidad);
                }
            }

            $venta->delete();
        });

        return redirect()->route('vendedor.ventas')
            ->with('success', 'Venta eliminada exitosamente y stock devuelto al inventario.');
    }
}
