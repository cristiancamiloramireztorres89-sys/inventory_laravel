<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * Transformar el recurso a un arreglo para Flutter.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_usuario' => (int) $this->id_usuario,
            'nombre' => (string) $this->nombre,
            'correo' => (string) $this->correo,
            'telefono' => $this->telefono ? (string) $this->telefono : null,
            'foto_url' => $this->foto_url,
            'activo' => (bool) $this->activo,
            'rol' => [
                'id_rol' => (int) ($this->rol?->id_rol ?? $this->id_rol),
                'nombre' => (string) ($this->rol?->nombre ?? 'Sin Rol'),
            ],
            'es_admin' => $this->esAdmin(),
            'es_vendedor' => $this->esVendedor(),
        ];
    }
}
