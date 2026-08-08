<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Mostrar la vista del formulario de inicio de sesión.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            /** @var \App\Models\Usuario $user */
            $user = Auth::user();

            return $user->esAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('vendedor.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Procesar una solicitud de autenticación e inicio de sesión.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if ($user->esAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Bienvenido al panel de Administrador');
        }

        return redirect()->intended(route('vendedor.dashboard'))
            ->with('success', 'Bienvenido al punto de venta');
    }

    /**
     * Cerrar la sesión autenticada del usuario.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Has cerrado sesión correctamente.');
    }
}
