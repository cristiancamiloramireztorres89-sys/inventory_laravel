<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends BaseApiController
{
    /**
     * Listado o búsqueda de clientes en tiempo real.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Cliente::orderBy('nombre', 'asc');

        if ($request->filled('buscar')) {
            $busqueda = trim($request->buscar);
            $query->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('telefono', 'like', "%{$busqueda}%")
                  ->orWhere('correo', 'like', "%{$busqueda}%");
            });
        }

        $clientes = $query->take(30)->get();

        return $this->sendResponse(
            ClienteResource::collection($clientes),
            'Clientes obtenidos correctamente.'
        );
    }

    /**
     * Registrar un nuevo cliente desde la aplicación móvil.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:100'],
        ], [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'correo.email' => 'El formato del correo es inválido.',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Errores de validación', $validator->errors()->toArray(), 422);
        }

        $cliente = Cliente::create([
            'nombre' => trim($request->nombre),
            'telefono' => $request->telefono ? trim($request->telefono) : null,
            'correo' => $request->correo ? trim($request->correo) : null,
        ]);

        return $this->sendResponse(
            new ClienteResource($cliente),
            'Cliente registrado exitosamente.',
            201
        );
    }

    /**
     * Ver datos de un cliente.
     */
    public function show(int $id): JsonResponse
    {
        $cliente = Cliente::find($id);

        if (! $cliente) {
            return $this->sendError('El cliente no existe.', [], 404);
        }

        return $this->sendResponse(
            new ClienteResource($cliente),
            'Cliente encontrado.'
        );
    }
}
