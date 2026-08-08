<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CodigoRecuperacionMail;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordCodeController extends Controller
{
    /**
     * Paso 1: Mostrar el formulario para ingresar el correo electrónico.
     */
    public function showEmailForm(): View
    {
        return view('auth.forgot-code-email');
    }

    /**
     * Paso 1 (Procesar): Validar si el correo existe y enviar el código OTP de 6 dígitos por correo.
     */
    public function sendCode(Request $request): RedirectResponse
    {
        $request->validate([
            'correo' => ['required', 'string', 'email'],
        ], [
            'correo.required' => 'Debes ingresar tu correo electrónico.',
            'correo.email'    => 'Ingresa un formato de correo electrónico válido.',
        ]);

        $correo = strtolower(trim($request->input('correo')));
        $usuario = Usuario::where('correo', $correo)->first();

        // Si el correo NO existe en la base de datos, no permitir continuar
        if (!$usuario) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['correo' => 'El correo electrónico ingresado no se encuentra registrado en el sistema.']);
        }

        // Si el usuario está desactivado
        if (!$usuario->estaActivo()) {
            return back()
                ->withInput($request->only('correo'))
                ->withErrors(['correo' => 'Esta cuenta de usuario ha sido desactivada. Por favor, comunícate con el administrador.']);
        }

        // Generar código numérico aleatorio de 6 dígitos (ej: 839201)
        $codigo = (string) random_int(100000, 999999);

        // Guardar el código encriptado en password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $correo],
            [
                'token'      => Hash::make($codigo),
                'created_at' => now(),
            ]
        );

        // Enviar correo electrónico con el código
        $mailSent = true;
        try {
            Mail::to($correo)->send(new CodigoRecuperacionMail($codigo, $usuario->nombre));
        } catch (\Throwable $e) {
            $mailSent = false;
        }

        // Guardar el correo en sesión para el Paso 2
        session([
            'recovery_email'    => $correo,
            'recovery_verified' => false,
        ]);

        $mensaje = "Hemos enviado un código de seguridad de 6 dígitos a {$correo}.";

        return redirect()->route('password.code.verify')
            ->with('status', $mensaje);
    }

    /**
     * Paso 2: Mostrar la pantalla para ingresar el código de 6 dígitos.
     */
    public function showVerifyCode(): View|RedirectResponse
    {
        $correo = session('recovery_email');

        if (!$correo) {
            return redirect()->route('password.code.email')
                ->withErrors(['correo' => 'Por favor, ingresa tu correo para iniciar el proceso de recuperación.']);
        }

        return view('auth.verify-code', compact('correo'));
    }

    /**
     * Paso 2 (Procesar): Validar si el código de 6 dígitos es correcto y no ha expirado.
     */
    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo' => ['required', 'string', 'min:6', 'max:6'],
        ], [
            'codigo.required' => 'Debes ingresar el código de 6 dígitos.',
            'codigo.min'      => 'El código debe contener exactamente 6 dígitos.',
            'codigo.max'      => 'El código debe contener exactamente 6 dígitos.',
        ]);

        $correo = session('recovery_email');
        if (!$correo) {
            return redirect()->route('password.code.email')
                ->withErrors(['correo' => 'La sesión ha expirado. Por favor, ingresa tu correo nuevamente.']);
        }

        $registro = DB::table('password_reset_tokens')->where('email', $correo)->first();

        if (!$registro) {
            return back()->withErrors(['codigo' => 'No hay ninguna solicitud de recuperación activa para este correo.']);
        }

        // Validar expiración (15 minutos)
        if ($registro->created_at && Carbon::parse($registro->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors(['codigo' => 'El código de seguridad ha expirado (validez de 15 minutos). Solicita uno nuevo.']);
        }

        // Verificar el código ingresado contra el hash guardado
        $codigoIngresado = trim($request->input('codigo'));
        if (!Hash::check($codigoIngresado, $registro->token)) {
            return back()->withErrors(['codigo' => 'El código de verificación ingresado es incorrecto. Revisa tu correo e inténtalo nuevamente.']);
        }

        // Código validado exitosamente -> Autorizar paso 3
        session(['recovery_verified' => true]);

        return redirect()->route('password.code.reset')
            ->with('status', '¡Código verificado con éxito! Ahora puedes definir tu nueva contraseña.');
    }

    /**
     * Reenviar un nuevo código de seguridad al correo.
     */
    public function resendCode(): RedirectResponse
    {
        $correo = session('recovery_email');
        if (!$correo) {
            return redirect()->route('password.code.email');
        }

        $usuario = Usuario::where('correo', $correo)->first();
        if (!$usuario) {
            return redirect()->route('password.code.email');
        }

        $nuevoCodigo = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $correo],
            [
                'token'      => Hash::make($nuevoCodigo),
                'created_at' => now(),
            ]
        );

        try {
            Mail::to($correo)->send(new CodigoRecuperacionMail($nuevoCodigo, $usuario->nombre));
        } catch (\Throwable $e) {
            // Continúa para no bloquear flujo
        }

        return back()->with('status', "Se ha generado y enviado un nuevo código a {$correo}.");
    }

    /**
     * Paso 3: Mostrar la pantalla para ingresar la nueva contraseña.
     */
    public function showResetForm(): View|RedirectResponse
    {
        $correo = session('recovery_email');
        $verificado = session('recovery_verified');

        if (!$correo || !$verificado) {
            return redirect()->route('password.code.email')
                ->withErrors(['correo' => 'Debes verificar tu código de seguridad antes de cambiar la contraseña.']);
        }

        return view('auth.reset-code-password', compact('correo'));
    }

    /**
     * Paso 3 (Procesar): Guardar la nueva contraseña cifrada y finalizar el proceso.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $correo = session('recovery_email');
        $verificado = session('recovery_verified');

        if (!$correo || !$verificado) {
            return redirect()->route('password.code.email')
                ->withErrors(['correo' => 'Sesión de recuperación no válida.']);
        }

        $request->validate([
            'contrasena' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'contrasena.required'  => 'Debes ingresar una nueva contraseña.',
            'contrasena.min'       => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'contrasena.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $usuario = Usuario::where('correo', $correo)->first();
        if (!$usuario) {
            return redirect()->route('login')->withErrors(['correo' => 'Usuario no encontrado.']);
        }

        // Actualizar la contraseña con cifrado seguro
        $usuario->contrasena = Hash::make($request->input('contrasena'));
        $usuario->save();

        // Eliminar el token de la base de datos
        DB::table('password_reset_tokens')->where('email', $correo)->delete();

        // Limpiar variables de sesión
        session()->forget(['recovery_email', 'recovery_verified']);

        return redirect()->route('login')
            ->with('status', '¡Tu contraseña ha sido restablecida exitosamente! Ya puedes iniciar sesión con tu nueva clave.');
    }
}
