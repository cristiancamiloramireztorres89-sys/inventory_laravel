<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    /**
     * Transformar el recurso a un arreglo para Flutter.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_categoria' => (int) $this->id_categoria,
            'nombre' => (string) $this->nombre,
            'descripcion' => $this->descripcion ? (string) $this->descripcion : null,
            'imagen_url' => $this->imagen_url,
            'total_productos' => $this->whenCounted('productos'),
        ];
    }
}
