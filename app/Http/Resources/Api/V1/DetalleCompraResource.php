<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleCompraResource extends JsonResource
{
    /**
     * Transformar el recurso a un arreglo para Flutter.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_detalle_compra' => (int) $this->id_detalle_compra,
            'id_producto' => (int) $this->id_producto,
            'producto_nombre' => (string) ($this->producto?->nombre ?? 'Producto'),
            'producto_imagen' => $this->producto?->imagen_url,
            'cantidad' => (int) $this->cantidad,
            'precio_unitario' => (float) $this->precio_unitario,
            'subtotal' => (float) $this->subtotal,
        ];
    }
}