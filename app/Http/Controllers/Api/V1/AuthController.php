<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\Api\V1\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseApiController
{
    /**
     * Autenticación de usuarios para la app móvil de Flutter.
     * Retorna el token de acceso Bearer y los datos del usuario.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'correo' => ['required', 'email'],
            'contrasena' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ], [
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo debe tener un formato válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Errores de validación', $validator->errors()->toArray(), 422);
        }

        $usuario = Usuario::with('rol')->where('correo', $request->correo)->first();

        if (! $usuario || ! Hash::check($request->contrasena, $usuario->contrasena)) {
            return $this->sendError('Las credenciales proporcionadas son incorrectas.', [], 401);
        }

        if (! $usuario->estaActivo()) {
            return $this->sendError('Esta cuenta se encuentra inactiva. Contacte al administrador.', [], 403);
        }

        $deviceName = $request->input('device_name', 'flutter-mobile');
        $token = $usuario->createToken($deviceName)->plainTextToken;

        return $this->sendResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'usuario' => new UsuarioResource($usuario),
        ], 'Inicio de sesión exitoso.');
    }

    /**
     * Cerrar sesión y revocar el token actual desde Flutter.
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user();
        if ($usuario && $usuario->currentAccessToken()) {
            $usuario->currentAccessToken()->delete();
        }

        return $this->sendResponse(null, 'Sesión cerrada exitosamente en este dispositivo.');
    }

    /**
     * Obtener los datos del perfil del usuario autenticado.
     */
    public function perfil(Request $request): JsonResponse
    {
        /** @var Usuario $usuario */
        $usuario = $request->user()->load('rol');

        return $this->sendResponse(new UsuarioResource($usuario), 'Perfil recuperado con éxito.');
    }

    /**
     * Listar todos los usuarios para la administración en Flutter.
     */
    public function usuarios(): JsonResponse
    {
        $usuarios = Usuario::with('rol')->orderBy('id_usuario', 'asc')->get();

        return $this->sendResponse(UsuarioResource::collection($usuarios), 'Usuarios recuperados con éxito.');
    }

    /**
     * Registrar un nuevo usuario desde Flutter.
     */
    public function crearUsuario(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:150'],
            'correo' => ['required', 'email', 'unique:usuarios,correo'],
            'contrasena' => ['required', 'string', 'min:6'],
            'id_rol' => ['required', 'integer'],
            'telefono' => ['nullable', 'string', 'max:30'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Errores de validación', $validator->errors()->toArray(), 422);
        }

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'contrasena' => $request->contrasena,
            'id_rol' => (int) $request->id_rol,
            'telefono' => $request->telefono,
            'activo' => true,
        ]);

        $usuario->load('rol');

        return $this->sendResponse(new UsuarioResource($usuario), 'Usuario creado exitosamente.');
    }

    /**
     * Actualizar usuario desde Flutter.
     */
    public function actualizarUsuario(Request $request, $id): JsonResponse
    {
        $usuario = Usuario::find($id);
        if (! $usuario) {
            return $this->sendError('Usuario no encontrado', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:150'],
            'correo' => ['required', 'email', 'unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario'],
            'id_rol' => ['required', 'integer'],
            'contrasena' => ['nullable', 'string', 'min:6'],
            'telefono' => ['nullable', 'string', 'max:30'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Errores de validación', $validator->errors()->toArray(), 422);
        }

        $data = [
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'id_rol' => (int) $request->id_rol,
        ];

        if ($request->filled('telefono')) {
            $data['telefono'] = $request->telefono;
        }

        if ($request->filled('contrasena')) {
            $data['contrasena'] = $request->contrasena;
        }

        $usuario->update($data);
        $usuario->load('rol');

        return $this->sendResponse(new UsuarioResource($usuario), 'Usuario actualizado exitosamente.');
    }

    /**
     * Alternar estado activo / inactivo de un usuario desde Flutter.
     */
    public function toggleUsuario($id): JsonResponse
    {
        $usuario = Usuario::find($id);
        if (! $usuario) {
            return $this->sendError('Usuario no encontrado', [], 404);
        }

        $currentId = auth('sanctum')->id() ?? auth()->id() ?? Auth::id();
        if ($currentId && (int)$usuario->id_usuario === (int)$currentId) {
            return $this->sendError('No puedes desactivar tu propia cuenta en sesión activa.', [], 400);
        }

        $usuario->activo = ! (bool) $usuario->activo;
        $usuario->save();
        $usuario->load('rol');

        $mensaje = $usuario->activo
            ? "Usuario '{$usuario->nombre}' activado exitosamente."
            : "Usuario '{$usuario->nombre}' desactivado exitosamente.";

        return $this->sendResponse(new UsuarioResource($usuario), $mensaje);
    }

    /**
     * Eliminar usuario desde Flutter.
     */
    public function eliminarUsuario($id): JsonResponse
    {
        $usuario = Usuario::find($id);
        if (! $usuario) {
            return $this->sendError('Usuario no encontrado', [], 404);
        }

        $currentId = auth('sanctum')->id() ?? auth()->id() ?? Auth::id();
        if ($currentId && (int)$usuario->id_usuario === (int)$currentId) {
            return $this->sendError('No puedes eliminar tu propia cuenta en sesión activa.', [], 400);
        }

        if ($usuario->ventas()->exists() || $usuario->compras()->exists()) {
            return $this->sendError("El usuario '{$usuario->nombre}' tiene registros en compras o ventas. Desactiva su cuenta en lugar de eliminarlo.", [], 400);
        }

        $nombre = $usuario->nombre;
        $usuario->delete();

        return $this->sendResponse([], "Usuario '$nombre' eliminado exitosamente.");
    }
}