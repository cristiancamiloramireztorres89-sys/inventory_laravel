<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\CategoriaResource;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoriaController extends BaseApiController
{
    /**
     * Listado de todas las categorías disponibles.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Categoria::withCount('productos')->orderBy('nombre', 'asc');

        if ($request->filled('buscar')) {
            $busqueda = trim($request->buscar);
            $query->where('nombre', 'like', "%{$busqueda}%");
        }

        $categorias = $query->get();

        return $this->sendResponse(
            CategoriaResource::collection($categorias),
            'Categorías obtenidas correctamente.'
        );
    }

    /**
     * Obtener detalle de una categoría específica.
     */
    public function show(int $id): JsonResponse
    {
        $categoria = Categoria::withCount('productos')->find($id);

        if (! $categoria) {
            return $this->sendError('La categoría solicitada no existe.', [], 404);
        }

        return $this->sendResponse(
            new CategoriaResource($categoria),
            'Categoría recuperada exitosamente.'
        );
    }

    /**
     * Crear una nueva categoría desde Flutter.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        $categoria = Categoria::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return $this->sendResponse(
            new CategoriaResource($categoria),
            'Categoría creada exitosamente.',
            201
        );
    }
}