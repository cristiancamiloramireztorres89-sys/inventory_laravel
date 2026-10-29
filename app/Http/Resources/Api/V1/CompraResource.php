<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompraResource extends JsonResource
{
    /**
     * Transformar el recurso a un arreglo para Flutter.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_compra' => (int) $this->id_compra,
            'fecha' => $this->fecha ? $this->fecha->format('Y-m-d H:i:s') : null,
            'subtotal' => (float) $this->subtotal,
            'iva' => (float) $this->iva,
            'total' => (float) $this->total,
            'usuario' => $this->usuario ? [
                'id_usuario' => (int) $this->usuario->id_usuario,
                'nombre' => (string) $this->usuario->nombre,
            ] : null,
            'proveedor' => $this->proveedor ? [
                'id_proveedor' => (int) $this->proveedor->id_proveedor,
                'nombre' => (string) $this->proveedor->nombre,
                'telefono' => $this->proveedor->telefono ? (string) $this->proveedor->telefono : null,
                'correo' => $this->proveedor->correo ? (string) $this->proveedor->correo : null,
            ] : null,
            'detalles' => DetalleCompraResource::collection($this->whenLoaded('detalles')),
        ];
    }
}