<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    /**
     * Listar categorías con conteo de productos vinculados.
     */
    public function index(Request $request): View
    {
        $categorias = Categoria::withCount('productos')->orderBy('nombre', 'asc')->get();

        return view('admin.categorias', compact('categorias'));
    }

    /**
     * Guardar una nueva categoría.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'      => ['required', 'string', 'max:100', 'unique:categorias,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'imagen'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique'   => 'Ya existe una categoría con este nombre.',
            'imagen.image'    => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.mimes'    => 'Formatos de imagen permitidos: JPG, JPEG, PNG, WEBP, GIF.',
            'imagen.max'      => 'La imagen no debe superar los 2MB.',
        ]);

        $nombreImagen = null;
        if ($request->hasFile('imagen')) {
            $nombreImagen = ImageService::processToBase64($request->file('imagen'), 600, 600, 82);
        }

        Categoria::create([
            'nombre'      => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
            'imagen'      => $nombreImagen,
        ]);

        return redirect()->route('admin.categorias')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Actualizar categoría existente.
     */
    public function update(Request $request, Categoria $categoria): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'      => ['required', 'string', 'max:100', 'unique:categorias,nombre,' . $categoria->id_categoria . ',id_categoria'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'imagen'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique'   => 'Ya existe otra categoría con este nombre.',
            'imagen.image'    => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.mimes'    => 'Formatos de imagen permitidos: JPG, JPEG, PNG, WEBP, GIF.',
            'imagen.max'      => 'La imagen no debe superar los 2MB.',
        ]);

        $data = [
            'nombre'      => $validated['nombre'],
            'descripcion' => $validated['descripcion'] ?? null,
        ];

        if ($request->hasFile('imagen')) {
            if ($categoria->imagen && ! str_starts_with($categoria->imagen, 'data:image')) {
                $archivoPrevio = public_path('uploads/categorias/' . $categoria->imagen);
                if (file_exists($archivoPrevio)) {
                    @unlink($archivoPrevio);
                }
            }

            $data['imagen'] = ImageService::processToBase64($request->file('imagen'), 600, 600, 82);
        }

        $categoria->update($data);

        return redirect()->route('admin.categorias')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Eliminar categoría.
     */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        if ($categoria->productos()->exists()) {
            return redirect()->route('admin.categorias')
                ->with('error', 'No se puede eliminar la categoría porque contiene productos asociados.');
        }

        if ($categoria->imagen && ! str_starts_with($categoria->imagen, 'data:image')) {
            $archivo = public_path('uploads/categorias/' . $categoria->imagen);
            if (file_exists($archivo)) {
                @unlink($archivo);
            }
        }

        $categoria->delete();

        return redirect()->route('admin.categorias')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
