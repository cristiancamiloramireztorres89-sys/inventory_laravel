<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_venta';
    protected $primaryKey = 'id_detalle_venta';
    public $timestamps = false;

    protected $fillable = [
        'id_venta',
        'id_producto',
        'cantidad',
        'costo_unitario',
        'precio_unitario',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'costo_unitario' => 'decimal:2',
            'precio_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Costo unitario real de compra al proveedor.
     */
    public function getCostoUnitarioRealAttribute(): float
    {
        $costo = (float) $this->costo_unitario;

        if ($costo <= 0 && $this->producto) {
            $costo = (float) ($this->producto->detalleCompras()->latest('id_detalle_compra')->value('precio_unitario') ?? 0);
        }

        return $costo;
    }

    /**
     * Costo total de adquisición de este artículo (Costo Unitario x Cantidad).
     */
    public function getCostoTotalAttribute(): float
    {
        return $this->costo_unitario_real * (int) $this->cantidad;
    }

    /**
     * Ganancia obtenida por cada unidad individual vendida (Precio Venta - Precio Compra).
     */
    public function getGananciaUnitariaAttribute(): float
    {
        return (float) $this->precio_unitario - $this->costo_unitario_real;
    }

    /**
     * Ganancia neta total generada por este artículo en la venta.
     */
    public function getGananciaAttribute(): float
    {
        return (float) $this->subtotal - $this->costo_total;
    }

    /**
     * Porcentaje de margen de rentabilidad sobre el costo.
     */
    public function getMargenAttribute(): float
    {
        $costo = $this->costo_total;
        if ($costo <= 0) {
            return $this->subtotal > 0 ? 100.0 : 0.0;
        }

        return round(($this->ganancia / $costo) * 100, 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |--------------------------------------------------------------------------
    */

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
