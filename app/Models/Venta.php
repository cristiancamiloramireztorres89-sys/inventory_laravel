<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';

    protected $fillable = [
        'id_usuario',
        'id_cliente',
        'fecha',
        'subtotal',
        'iva',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'subtotal' => 'decimal:2',
            'iva' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Costo total de adquisición de los productos incluidos en esta venta.
     */
    public function getCostoTotalAttribute(): float
    {
        return (float) $this->detalles->sum(fn ($d) => $d->costo_total);
    }

    /**
     * Ganancia neta generada en esta venta (Ingreso Total - Costo Total de Compra).
     */
    public function getGananciaNetaAttribute(): float
    {
        return (float) $this->total - $this->costo_total;
    }

    /**
     * Porcentaje de margen de rentabilidad sobre el costo.
     */
    public function getMargenPorcentajeAttribute(): float
    {
        $costo = $this->costo_total;
        if ($costo <= 0) {
            return $this->total > 0 ? 100.0 : 0.0;
        }

        return round(($this->ganancia_neta / $costo) * 100, 1);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes de Trazabilidad y Aislamiento por Usuario
    |--------------------------------------------------------------------------
    */

    /**
     * Alcance para filtrar las ventas según el rol del usuario autenticado.
     * Administradores ven todas; Vendedores solo ven las suyas.
     */
    public function scopeForUser(Builder $query, Usuario $user): Builder
    {
        if ($user->esAdmin()) {
            return $query;
        }

        return $query->where('id_usuario', $user->id_usuario);
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |--------------------------------------------------------------------------
    */

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }
}