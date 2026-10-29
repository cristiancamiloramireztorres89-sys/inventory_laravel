<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    /**
     * Transformar el recurso a un arreglo para Flutter.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $baseUrl = $request->getSchemeAndHttpHost();
        $imagenUrl = null;
        if ($this->imagen) {
            $imagenUrl = $baseUrl . '/uploads/productos/' . $this->imagen;
        }

        return [
            'id_producto' => (int) $this->id_producto,
            'nombre' => (string) $this->nombre,
            'marca' => $this->marca ? (string) $this->marca : null,
            'descripcion' => $this->descripcion ? (string) $this->descripcion : null,
            'id_categoria' => (int) $this->id_categoria,
            'categoria' => $this->categoria ? [
                'id_categoria' => (int) $this->categoria->id_categoria,
                'nombre' => (string) $this->categoria->nombre,
            ] : null,
            'precio_venta' => (float) $this->precio_venta,
            'stock_actual' => (int) $this->stock_actual,
            'stock_minimo' => (int) $this->stock_minimo,
            'esta_bajo_stock' => (int) $this->stock_actual <= (int) $this->stock_minimo,
            'imagen_url' => $imagenUrl,
            'activo' => (bool) $this->activo,
        ];
    }
}