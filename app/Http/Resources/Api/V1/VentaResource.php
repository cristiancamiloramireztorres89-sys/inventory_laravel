<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
{
    /**
     * Transformar el recurso a un arreglo para Flutter.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_venta' => (int) $this->id_venta,
            'fecha' => $this->fecha ? $this->fecha->format('Y-m-d H:i:s') : null,
            'subtotal' => (float) $this->subtotal,
            'iva' => (float) $this->iva,
            'total' => (float) $this->total,
            'vendedor' => $this->usuario ? [
                'id_usuario' => (int) $this->usuario->id_usuario,
                'nombre' => (string) $this->usuario->nombre,
            ] : null,
            'cliente' => $this->cliente ? [
                'id_cliente' => (int) $this->cliente->id_cliente,
                'nombre' => (string) $this->cliente->nombre,
                'telefono' => $this->cliente->telefono ? (string) $this->cliente->telefono : null,
                'correo' => $this->cliente->correo ? (string) $this->cliente->correo : null,
            ] : null,
            'detalles' => DetalleVentaResource::collection($this->whenLoaded('detalles')),
        ];
    }
}
