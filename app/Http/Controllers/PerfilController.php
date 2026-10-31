<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PerfilController extends Controller
{
    /**
     * Mostrar la pantalla de perfil del usuario en sesión.
     */
    public function index(): View
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        return view('perfil.index', compact('usuario'));
    }

    /**
     * Actualizar la información personal y foto de perfil.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $validated = $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'correo'   => ['required', 'string', 'email', 'max:100', 'unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario'],
            'telefono' => ['nullable', 'string', 'max:25'],
            'foto'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ], [
            'nombre.required' => 'El nombre completo es obligatorio.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email'    => 'Debes ingresar un correo electrónico válido.',
            'correo.unique'   => 'Este correo ya pertenece a otro usuario.',
            'foto.image'      => 'El archivo seleccionado debe ser una imagen válida.',
            'foto.mimes'      => 'Formatos de imagen permitidos: JPG, JPEG, PNG, WEBP, GIF.',
            'foto.max'        => 'La foto de perfil no debe superar los 2MB de peso.',
        ]);

        $data = [
            'nombre'   => $validated['nombre'],
            'correo'   => $validated['correo'],
            'telefono' => $validated['telefono'] ?? null,
        ];

        // Procesar subida de foto de perfil
        if ($request->hasFile('foto')) {
            // Eliminar archivo local previo si era un archivo físico
            if ($usuario->foto && ! str_starts_with($usuario->foto, 'data:image')) {
                $archivoPrevio = public_path('uploads/usuarios/' . $usuario->foto);
                if (file_exists($archivoPrevio)) {
                    @unlink($archivoPrevio);
                }
            }

            // Procesar y comprimir la imagen en Data URI Base64 persistente
            $data['foto'] = ImageService::processToBase64($request->file('foto'), 400, 400, 82);
        }

        $usuario->update($data);

        return redirect()->route('perfil')
            ->with('success', 'Tu información de perfil ha sido actualizada exitosamente.');
    }

    /**
     * Actualizar la contraseña de seguridad.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $validated = $request->validate([
            'contrasena_actual' => ['required', 'string'],
            'nueva_contrasena'  => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'contrasena_actual.required' => 'Debes ingresar tu contraseña actual para confirmar el cambio.',
            'nueva_contrasena.required'  => 'Debes ingresar tu nueva contraseña.',
            'nueva_contrasena.min'       => 'La nueva contraseña debe tener mínimo 6 caracteres.',
            'nueva_contrasena.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
        ]);

        // Verificar si la contraseña actual coincide
        if (! Hash::check($validated['contrasena_actual'], $usuario->contrasena)) {
            throw ValidationException::withMessages([
                'contrasena_actual' => 'La contraseña actual ingresada es incorrecta.',
            ]);
        }

        $usuario->update([
            'contrasena' => Hash::make($validated['nueva_contrasena']),
        ]);

        return redirect()->route('perfil')
            ->with('success', 'Tu contraseña ha sido actualizada con éxito.');
    }

    /**
     * Eliminar la foto de perfil actual y volver a la inicial predeterminada.
     */
    public function destroyPhoto(): RedirectResponse
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        if ($usuario->foto) {
            if (! str_starts_with($usuario->foto, 'data:image')) {
                $archivo = public_path('uploads/usuarios/' . $usuario->foto);
                if (file_exists($archivo)) {
                    @unlink($archivo);
                }
            }

            $usuario->update(['foto' => null]);
        }

        return redirect()->route('perfil')
            ->with('success', 'Tu foto de perfil ha sido eliminada.');
    }
}
