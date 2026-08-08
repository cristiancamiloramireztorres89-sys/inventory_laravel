<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Mostrar la vista para ingresar la nueva contraseña.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            'token'  => $request->route('token') ?? $request->input('token'),
            'correo' => $request->input('correo'),
        ]);
    }

    /**
     * Procesar el cambio y restablecimiento de contraseña.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token'                 => ['required'],
            'correo'                => ['required', 'email'],
            'contrasena'            => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'correo.required'       => 'El correo electrónico es obligatorio.',
            'correo.email'          => 'Ingresa un correo electrónico válido.',
            'contrasena.required'   => 'Debes ingresar una nueva contraseña.',
            'contrasena.min'        => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'contrasena.confirmed'  => 'La confirmación de la contraseña no coincide.',
        ]);

        $correo = trim($request->input('correo'));
        $token = $request->input('token');

        // Buscar el token en la tabla password_reset_tokens
        $registro = DB::table('password_reset_tokens')
            ->where('email', $correo)
            ->first();

        if (!$registro) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['correo' => 'El enlace de recuperación es inválido o no existe.']);
        }

        // Validar expiración del token (60 minutos por defecto)
        if ($registro->created_at && Carbon::parse($registro->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $correo)->delete();

            return redirect()->route('password.request')
                ->withErrors(['correo' => 'El enlace de recuperación ha expirado. Por favor, solicita uno nuevo.']);
        }

        // Verificar validez del token
        if (!Hash::check($token, $registro->token)) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['correo' => 'El token de seguridad no es válido o ha caducado.']);
        }

        // Buscar al usuario y actualizar su contraseña
        $usuario = Usuario::where('correo', $correo)->first();

        if (!$usuario) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['correo' => 'No se encontró el usuario asociado a este correo.']);
        }

        $usuario->contrasena = Hash::make($request->input('contrasena'));
        $usuario->save();

        // Eliminar el token usado para que no se pueda reutilizar
        DB::table('password_reset_tokens')->where('email', $correo)->delete();

        return redirect()->route('login')
            ->with('status', '¡Tu contraseña ha sido restablecida exitosamente! Ya puedes iniciar sesión.');
    }
}
