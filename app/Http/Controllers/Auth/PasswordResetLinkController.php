<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Mostrar la vista para solicitar el enlace de restablecimiento de contraseña.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Procesar la solicitud del enlace de restablecimiento de contraseña.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'correo' => ['required', 'email'],
        ], [
            'correo.required' => 'Debes ingresar tu correo electrónico.',
            'correo.email'    => 'Ingresa un formato de correo válido.',
        ]);

        $usuario = Usuario::where('correo', trim($request->input('correo')))->first();

        if (!$usuario) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['correo' => 'No encontramos ningún usuario registrado con ese correo electrónico.']);
        }

        if (!$usuario->estaActivo()) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['correo' => 'Tu cuenta se encuentra desactivada. Contacta al administrador.']);
        }

        // Generar token seguro
        $token = Str::random(64);

        // Guardar token en la tabla password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $usuario->correo],
            [
                'token'      => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Enviar la notificación por correo oficial de Laravel
        try {
            $usuario->sendPasswordResetNotification($token);
        } catch (\Throwable $e) {
            // Continuar incluso si el servidor SMTP local no está configurado
        }

        $directUrl = route('password.reset', [
            'token'  => $token,
            'correo' => $usuario->correo,
        ]);

        return back()->with('status', 'Hemos enviado el enlace para restablecer tu contraseña a tu correo electrónico.')
            ->with('direct_reset_url', $directUrl);
    }
}
