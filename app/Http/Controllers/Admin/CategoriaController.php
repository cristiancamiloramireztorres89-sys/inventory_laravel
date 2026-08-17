<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
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
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique'   => 'Ya existe una categoría con este nombre.',
        ]);

        Categoria::create($validated);

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
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.unique'   => 'Ya existe otra categoría con este nombre.',
        ]);

        $categoria->update($validated);

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

        $categoria->delete();

        return redirect()->route('admin.categorias')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
