<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Manejar la solicitud entrante y verificar los roles permitidos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Nombres de roles autorizados
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\Usuario $user */
        $user = Auth::user();
        $userRole = (int) $user->id_rol === 1 ? 'administrador' : ((int) $user->id_rol === 2 ? 'vendedor' : strtolower(trim($user->rol?->nombre ?? '')));

        $allowedRoles = array_map(fn($r) => strtolower(trim($r)), $roles);

        if (! in_array($userRole, $allowedRoles, true)) {
            if ($user->esAdmin()) {
                if ($request->routeIs('admin.*')) {
                    abort(403, 'No tienes permiso para acceder a esta sección.');
                }
                return redirect()->route('admin.dashboard')->with('error', 'No tienes permiso para acceder a esa sección.');
            }

            if ($request->routeIs('vendedor.*')) {
                abort(403, 'No tienes permiso para acceder a esta sección.');
            }
            return redirect()->route('vendedor.dashboard')->with('error', 'No tienes permiso para acceder a esa sección.');
        }

        return $next($request);
    }
}
