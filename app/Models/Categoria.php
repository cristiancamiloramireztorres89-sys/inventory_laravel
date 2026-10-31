<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen',
    ];

    /**
     * Obtener la URL pública de la imagen de la categoría (si existe).
     */
    public function getImagenUrlAttribute(): ?string
    {
        if (! $this->imagen) {
            return null;
        }

        if (str_starts_with($this->imagen, 'data:image') || filter_var($this->imagen, FILTER_VALIDATE_URL)) {
            return $this->imagen;
        }

        if (file_exists(public_path('uploads/categorias/' . $this->imagen))) {
            return asset('uploads/categorias/' . $this->imagen);
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |--------------------------------------------------------------------------
    */

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
    }
}