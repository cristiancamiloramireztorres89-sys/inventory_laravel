<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtener las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'correo' => ['required', 'string', 'email'],
            'contrasena' => ['required', 'string'],
        ];
    }

    /**
     * Obtener los nombres personalizados de los atributos para los errores de validación.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'correo' => 'correo electrónico',
            'contrasena' => 'contraseña',
        ];
    }

    /**
     * Obtener los mensajes personalizados para los errores de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Por favor ingresa un correo electrónico válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ];
    }

    /**
     * Intentar autenticar las credenciales de la solicitud.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            'correo' => $this->input('correo'),
            'password' => $this->input('contrasena'),
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'correo' => 'El correo electrónico o la contraseña no coinciden con nuestros registros.',
            ]);
        }

        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        if (! $user->estaActivo()) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'correo' => 'Esta cuenta de usuario ha sido desactivada. Por favor, comunícate con el administrador.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Asegurar que la solicitud de inicio de sesión no exceda el límite de intentos (Rate Limit).
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'correo' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]) ?? "Demasiados intentos de acceso. Por favor intenta de nuevo en {$seconds} segundos.",
        ]);
    }

    /**
     * Obtener la clave única de limitación de intentos para la solicitud.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('correo')).'|'.$this->ip());
    }
}
