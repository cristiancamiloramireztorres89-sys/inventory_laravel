<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
    /**
     * Listar todos los productos del inventario para el Administrador (activos e inactivos).
     */
    public function index(Request $request): View
    {
        $productos  = Producto::with('categoria')->orderBy('id_producto', 'desc')->get();
        $categorias = Categoria::orderBy('nombre', 'asc')->get();

        return view('admin.productos', compact('productos', 'categorias'));
    }

    /**
     * Guardar un nuevo producto en el inventario con imagen opcional.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'        => ['required', 'string', 'max:100'],
            'id_categoria'  => ['required', 'integer', 'exists:categorias,id_categoria'],
            'marca'         => ['nullable', 'string', 'max:100'],
            'stock_actual'  => ['required', 'integer', 'min:0'],
            'stock_minimo'  => ['required', 'integer', 'min:0'],
            'precio_venta'  => ['required', 'numeric', 'min:0'],
            'descripcion'   => ['nullable', 'string', 'max:500'],
            'imagen'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ], [
            'nombre.required'       => 'El nombre del producto es obligatorio.',
            'id_categoria.required' => 'Debes seleccionar una categoría.',
            'stock_actual.required' => 'El stock actual es obligatorio.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'imagen.image'          => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.mimes'          => 'Formatos de imagen permitidos: JPG, JPEG, PNG, WEBP, GIF.',
            'imagen.max'            => 'La imagen no debe superar los 2MB.',
        ]);

        $nombreImagen = null;
        if ($request->hasFile('imagen')) {
            $destino = public_path('uploads/productos');
            if (! file_exists($destino)) {
                mkdir($destino, 0755, true);
            }

            $file = $request->file('imagen');
            $nombreImagen = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destino, $nombreImagen);
        }

        Producto::create([
            'nombre'        => $validated['nombre'],
            'id_categoria'  => $validated['id_categoria'],
            'marca'         => $validated['marca'],
            'stock_actual'  => $validated['stock_actual'],
            'stock_minimo'  => $validated['stock_minimo'],
            'precio_venta'  => $validated['precio_venta'],
            'descripcion'   => $validated['descripcion'],
            'imagen'        => $nombreImagen,
            'activo'        => true,
        ]);

        return redirect()->route('admin.productos')
            ->with('success', 'Producto registrado exitosamente en el catálogo.');
    }

    /**
     * Actualizar los datos de un producto y su imagen.
     */
    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'        => ['required', 'string', 'max:100'],
            'id_categoria'  => ['required', 'integer', 'exists:categorias,id_categoria'],
            'marca'         => ['nullable', 'string', 'max:100'],
            'stock_actual'  => ['required', 'integer', 'min:0'],
            'stock_minimo'  => ['required', 'integer', 'min:0'],
            'precio_venta'  => ['required', 'numeric', 'min:0'],
            'descripcion'   => ['nullable', 'string', 'max:500'],
            'imagen'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ], [
            'nombre.required'       => 'El nombre del producto es obligatorio.',
            'id_categoria.required' => 'Debes seleccionar una categoría.',
            'stock_actual.required' => 'El stock actual es obligatorio.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'imagen.image'          => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.mimes'          => 'Formatos de imagen permitidos: JPG, JPEG, PNG, WEBP, GIF.',
            'imagen.max'            => 'La imagen no debe superar los 2MB.',
        ]);

        $data = [
            'nombre'        => $validated['nombre'],
            'id_categoria'  => $validated['id_categoria'],
            'marca'         => $validated['marca'],
            'stock_actual'  => $validated['stock_actual'],
            'stock_minimo'  => $validated['stock_minimo'],
            'precio_venta'  => $validated['precio_venta'],
            'descripcion'   => $validated['descripcion'],
        ];

        if ($request->hasFile('imagen')) {
            $destino = public_path('uploads/productos');
            if (! file_exists($destino)) {
                mkdir($destino, 0755, true);
            }

            if ($producto->imagen && file_exists($destino . '/' . $producto->imagen)) {
                @unlink($destino . '/' . $producto->imagen);
            }

            $file = $request->file('imagen');
            $nombreImagen = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($destino, $nombreImagen);
            $data['imagen'] = $nombreImagen;
        }

        $producto->update($data);

        return redirect()->route('admin.productos')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Alternar el estado activo / inactivo de un producto (Desactivar / Activar).
     */
    public function toggleStatus(Producto $producto): RedirectResponse
    {
        $producto->activo = ! (bool) $producto->activo;
        $producto->save();

        $estado = $producto->activo ? 'activado' : 'desactivado';
        $mensaje = $producto->activo
            ? "El producto '{$producto->nombre}' ha sido activado y volverá a ser visible para los vendedores."
            : "El producto '{$producto->nombre}' ha sido desactivado. Ya no aparecerá en el catálogo del vendedor.";

        return redirect()->route('admin.productos')
            ->with('success', $mensaje);
    }

    /**
     * Eliminar un producto del inventario (si no tiene transacciones asociadas).
     */
    public function destroy(Producto $producto): RedirectResponse
    {
        if ($producto->detalleVentas()->exists() || $producto->detalleCompras()->exists()) {
            return redirect()->route('admin.productos')
                ->with('error', 'No se puede eliminar físicamente el producto porque tiene compras o ventas registradas. Te recomendamos usar la opción de "Desactivar Producto".');
        }

        if ($producto->imagen && file_exists(public_path('uploads/productos/' . $producto->imagen))) {
            @unlink(public_path('uploads/productos/' . $producto->imagen));
        }

        $producto->delete();

        return redirect()->route('admin.productos')
            ->with('success', 'Producto eliminado del inventario.');
    }
}
