<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Listar todos los usuarios con su rol, estado y conteo de transacciones.
     */
    public function index(): View
    {
        $usuarios = Usuario::with('rol')
            ->withCount(['ventas', 'compras'])
            ->orderBy('id_usuario', 'asc')
            ->get();

        $roles = Role::orderBy('nombre')->get();

        return view('admin.listarusuario', compact('usuarios', 'roles'));
    }

    /**
     * Guardar un nuevo usuario (activo por defecto).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'     => ['required', 'string', 'max:100'],
            'correo'     => ['required', 'string', 'email', 'max:100', 'unique:usuarios,correo'],
            'id_rol'     => ['required', 'integer', 'exists:roles,id_rol'],
            'contrasena' => ['required', 'string', 'min:6'],
        ], [
            'nombre.required'     => 'El nombre es obligatorio.',
            'correo.required'     => 'El correo electrónico es obligatorio.',
            'correo.unique'       => 'Este correo ya está registrado por otro usuario.',
            'id_rol.required'     => 'Debes seleccionar un rol.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.min'      => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        Usuario::create([
            'nombre'     => $validated['nombre'],
            'correo'     => $validated['correo'],
            'id_rol'     => $validated['id_rol'],
            'contrasena' => Hash::make($validated['contrasena']),
            'activo'     => true,
        ]);

        return redirect()->route('admin.usuarios')
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Actualizar los datos de un usuario existente.
     */
    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $validated = $request->validate([
            'nombre'     => ['required', 'string', 'max:100'],
            'correo'     => ['required', 'string', 'email', 'max:100', 'unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario'],
            'id_rol'     => ['required', 'integer', 'exists:roles,id_rol'],
            'contrasena' => ['nullable', 'string', 'min:6'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.unique'   => 'Este correo ya pertenece a otro usuario.',
            'id_rol.required' => 'Debes seleccionar un rol.',
            'contrasena.min'  => 'La nueva contraseña debe tener al menos 6 caracteres.',
        ]);

        $data = [
            'nombre' => $validated['nombre'],
            'correo' => $validated['correo'],
            'id_rol' => $validated['id_rol'],
        ];

        if (!empty($validated['contrasena'])) {
            $data['contrasena'] = Hash::make($validated['contrasena']);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Alternar el estado activo / inactivo de un usuario (Desactivar o Activar).
     */
    public function toggleStatus(Usuario $usuario): RedirectResponse
    {
        // 1. Evitar que el administrador desactive su propia cuenta en sesión
        if ($usuario->id_usuario === Auth::id()) {
            return redirect()->route('admin.usuarios')
                ->with('error', 'No puedes desactivar tu propia cuenta en sesión activa.');
        }

        $usuario->activo = ! (bool) $usuario->activo;
        $usuario->save();

        $mensaje = $usuario->activo
            ? "El usuario '{$usuario->nombre}' ha sido activado correctamente."
            : "El usuario '{$usuario->nombre}' ha sido desactivado. Ya no podrá iniciar sesión.";

        return redirect()->route('admin.usuarios')
            ->with('success', $mensaje);
    }

    /**
     * Eliminar un usuario permanentemente solo si no tiene ventas ni compras asociadas.
     */
    public function destroy(Usuario $usuario): RedirectResponse
    {
        // 1. Evitar que el administrador se elimine a sí mismo
        if ($usuario->id_usuario === Auth::id()) {
            return redirect()->route('admin.usuarios')
                ->with('error', 'No puedes eliminar tu propia cuenta de usuario en sesión activa.');
        }

        // 2. Verificar si tiene compras o ventas registradas en el historial
        $totalVentas  = $usuario->ventas()->count();
        $totalCompras = $usuario->compras()->count();

        if ($totalVentas > 0 || $totalCompras > 0) {
            $motivos = [];
            if ($totalVentas > 0) {
                $motivos[] = "{$totalVentas} venta(s)";
            }
            if ($totalCompras > 0) {
                $motivos[] = "{$totalCompras} compra(s)";
            }
            $detalle = implode(' y ', $motivos);

            return redirect()->route('admin.usuarios')
                ->with('error', "No se puede eliminar a '{$usuario->nombre}' porque tiene {$detalle} asociadas en el historial. Puedes desactivar su cuenta para revocarle el acceso.");
        }

        // 3. Eliminar usuario de la base de datos
        $nombre = $usuario->nombre;
        $usuario->delete();

        return redirect()->route('admin.usuarios')
            ->with('success', "El usuario '{$nombre}' ha sido eliminado exitosamente.");
    }
}
