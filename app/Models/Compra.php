<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';
    protected $primaryKey = 'id_compra';

    protected $fillable = [
        'id_usuario',
        'id_proveedor',
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

    /*
    |--------------------------------------------------------------------------
    | Scopes de Trazabilidad y Aislamiento por Usuario
    |--------------------------------------------------------------------------
    */

    /**
     * Alcance para filtrar las compras según el rol del usuario autenticado.
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

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor', 'id_proveedor');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra', 'id_compra');
    }
}