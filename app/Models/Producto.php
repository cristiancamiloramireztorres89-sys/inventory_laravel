<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'id_categoria',
        'nombre',
        'marca',
        'stock_actual',
        'stock_minimo',
        'precio_venta',
        'descripcion',
        'imagen',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'precio_venta' => 'decimal:2',
            'stock_actual' => 'integer',
            'stock_minimo' => 'integer',
        ];
    }

    /**
     * Scope para filtrar solo los productos activos (visibles para vendedores).
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /**
     * Determinar si el producto se encuentra activo.
     */
    public function estaActivo(): bool
    {
        return (bool) $this->activo;
    }

    /**
     * Obtener la URL pública de la imagen del producto (si existe).
     */
    public function getImagenUrlAttribute(): ?string
    {
        if ($this->imagen && file_exists(public_path('uploads/productos/' . $this->imagen))) {
            return asset('uploads/productos/' . $this->imagen);
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |--------------------------------------------------------------------------
    */

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function detalleCompras()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto', 'id_producto');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto', 'id_producto');
    }
}