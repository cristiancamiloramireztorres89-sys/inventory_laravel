<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = true;

    protected $fillable = [
        'id_rol',
        'nombre',
        'correo',
        'contrasena',
        'activo',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    /**
     * Obtener los atributos que deben ser convertidos (casteados).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'contrasena' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    /**
     * Obtener el password para la autenticación de Laravel.
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    /**
     * Obtener el identificador único para la autenticación.
     */
    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    /**
     * Obtener el correo electrónico para el restablecimiento de contraseña.
     */
    public function getEmailForPasswordReset(): string
    {
        return (string) $this->correo;
    }

    /**
     * Canal de entrega para notificaciones de correo.
     */
    public function routeNotificationForMail(): string
    {
        return (string) $this->correo;
    }

    /**
     * Enviar la notificación de restablecimiento de contraseña.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\RestablecerContrasenaNotification($token));
    }

    /**
     * Determinar si el usuario tiene rol de administrador.
     */
    public function esAdmin(): bool
    {
        return strtolower(trim($this->rol?->nombre ?? '')) === 'administrador';
    }

    /**
     * Determinar si el usuario tiene rol de vendedor.
     */
    public function esVendedor(): bool
    {
        return strtolower(trim($this->rol?->nombre ?? '')) === 'vendedor';
    }

    /**
     * Determinar si el usuario está activo.
     */
    public function estaActivo(): bool
    {
        return (bool) $this->activo;
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |--------------------------------------------------------------------------
    */

    public function rol()
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_rol');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_usuario', 'id_usuario');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_usuario', 'id_usuario');
    }
}