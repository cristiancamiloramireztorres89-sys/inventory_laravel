# Lógica QA: Editar Usuario

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Se utiliza la regla de unicidad ignorando el ID del usuario en edición (`unique:usuarios,correo,{$id},id_usuario`) y el campo `contrasena` es `nullable|min:6`. Si no se envía contraseña, la contraseña existente se preserva intacta.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `PUT /admin/usuarios/{usuario}` (Nombre: `admin.usuarios.update`)
- **Directiva Blade:** `@method('PUT')` y `@csrf`
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\UsuarioController@update`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador abre el modal de edición de un usuario.
2. Validación en el Controlador:
   ```php
   $validated = $request->validate([
       'nombre'     => ['required', 'string', 'max:100'],
       'correo'     => ['required', 'string', 'email', 'max:100', 'unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario'],
       'id_rol'     => ['required', 'integer', 'exists:roles,id_rol'],
       'contrasena' => ['nullable', 'string', 'min:6'],
   ]);
   ```
3. Lógica de Actualización de Contraseña:
   ```php
   $data = [
       'nombre' => $validated['nombre'],
       'correo' => $validated['correo'],
       'id_rol' => $validated['id_rol'],
   ];

   if (!empty($validated['contrasena'])) {
       $data['contrasena'] = Hash::make($validated['contrasena']);
   }

   $usuario->update($data);
   ```
4. Redirige a `admin.usuarios` con mensaje flash: *"Usuario actualizado correctamente."*.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Datos de Entrada | Resultado Esperado |
|---|---|---|---|
| **TC-EDITU-01** | Editar datos manteniendo contraseña | Cambiar nombre/rol, dejar campo contraseña vacío | Los datos se actualizan y la contraseña anterior sigue funcionando sin alteración. |
| **TC-EDITU-02** | Cambio voluntario de contraseña | Enviar nueva contraseña válida (ej: `nueva123`) | La contraseña se cifra con nuevo hash Bcrypt y el usuario puede ingresar con su nueva clave. |
| **TC-EDITU-03** | Mantener el mismo correo actual | Dejar el correo del propio usuario | Pasa la validación exitosamente (la regla `unique` no choca contra sí misma). |
| **TC-EDITU-04** | Usar correo de otro usuario | Ingresar el correo de otro usuario registrado | Error de validación: *"Este correo ya pertenece a otro usuario."*. |
