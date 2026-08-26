# Lógica QA: Crear Usuario (Panel Administrador)

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Se valida mediante las reglas automáticas de Laravel (`unique:usuarios,correo`), el rol se valida con `exists:roles,id_rol`, y la contraseña se cifra inmediatamente con `Hash::make`.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `POST /admin/usuarios` (Nombre: `admin.usuarios.store`)
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\UsuarioController@store`
- **Vista Blade:** `resources/views/admin/listarusuario.blade.php`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador abre el modal de registro de usuario en `/admin/usuarios`.
2. El formulario envía por POST con token `@csrf`:
   - `nombre` (string)
   - `correo` (email)
   - `id_rol` (entero de rol)
   - `contrasena` (texto)
3. Reglas de Validación en el Controlador:
   ```php
   $validated = $request->validate([
       'nombre'     => ['required', 'string', 'max:100'],
       'correo'     => ['required', 'string', 'email', 'max:100', 'unique:usuarios,correo'],
       'id_rol'     => ['required', 'integer', 'exists:roles,id_rol'],
       'contrasena' => ['required', 'string', 'min:6'],
   ], [
       'correo.unique'  => 'Este correo ya está registrado por otro usuario.',
       'contrasena.min' => 'La contraseña debe tener al menos 6 caracteres.',
   ]);
   ```
4. Creación del Usuario en Base de Datos:
   ```php
   Usuario::create([
       'nombre'     => $validated['nombre'],
       'correo'     => $validated['correo'],
       'id_rol'     => $validated['id_rol'],
       'contrasena' => Hash::make($validated['contrasena']),
       'activo'     => true,
   ]);
   ```
5. Redirección a `route('admin.usuarios')` con flash: `with('success', 'Usuario creado correctamente.')`.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Datos de Entrada | Resultado Esperado |
|---|---|---|---|
| **TC-USER-01** | Creación exitosa de Vendedor | Nombre válido, correo nuevo, rol Vendedor, clave >= 6 | Usuario insertado en BD con `activo = 1`, clave encriptada (hash Bcrypt). Alerta verde de éxito. |
| **TC-USER-02** | Correo duplicado | Correo que ya existe en la tabla `usuarios` | Formulario se rechaza, mensaje: *"Este correo ya está registrado por otro usuario."*. |
| **TC-USER-03** | Contraseña demasiado corta | `contrasena`: `12345` (5 caracteres) | Error: *"La contraseña debe tener al menos 6 caracteres."*. |
| **TC-USER-04** | Rol inexistente | `id_rol`: `999` | Error de validación: el rol seleccionado debe existir en la tabla `roles`. |
| **TC-USER-05** | Intento de creación por un Vendedor | Usuario con rol `vendedor` envía POST a `/admin/usuarios` | Error HTTP `403 Forbidden` por middleware `role:administrador`. |
