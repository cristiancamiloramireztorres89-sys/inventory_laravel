<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProveedorResource extends JsonResource
{
    /**
     * Transformar el recurso a un arreglo para Flutter.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_proveedor' => (int) $this->id_proveedor,
            'nombre' => (string) $this->nombre,
            'telefono' => $this->telefono ? (string) $this->telefono : null,
            'correo' => $this->correo ? (string) $this->correo : null,
        ];
    }
}