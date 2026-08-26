# Lógica QA: Desactivar / Activar Usuario (Toggle Estado)

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Existe una **validación de seguridad estricta** que impide que el usuario actualmente autenticado desactive su propia cuenta en sesión activa.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `PATCH /admin/usuarios/{usuario}/toggle` (Nombre: `admin.usuarios.toggle`)
- **Directiva Blade:** `@method('PATCH')` y `@csrf`
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\UsuarioController@toggleStatus`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador presiona el botón de Activar / Desactivar en la tabla de usuarios.
2. **Validación de Auto-Bloqueo:**
   ```php
   if ($usuario->id_usuario === Auth::id()) {
       return redirect()->route('admin.usuarios')
           ->with('error', 'No puedes desactivar tu propia cuenta en sesión activa.');
   }
   ```
3. Inversión del estado booleano:
   ```php
   $usuario->activo = ! (bool) $usuario->activo;
   $usuario->save();
   ```
4. Generación de mensaje dinámico según el nuevo estado:
   - Si quedó activo: *"El usuario '{nombre}' ha sido activado correctamente."*
   - Si quedó inactivo: *"El usuario '{nombre}' ha sido desactivado. Ya no podrá iniciar sesión."*
5. **Efecto en el Sistema:** Si el usuario desactivado intenta iniciar sesión, `LoginRequest` rechazará su acceso inmediatamente.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Acción Realizada | Resultado Esperado |
|---|---|---|---|
| **TC-TOGU-01** | Desactivar vendedor | Presionar toggle sobre un vendedor activo | El usuario pasa a `activo = 0` (badge rojo en vista). El vendedor ya no puede loguearse. |
| **TC-TOGU-02** | Reactivar vendedor | Presionar toggle sobre vendedor inactivo | El usuario pasa a `activo = 1` (badge verde). Puede volver a iniciar sesión normalmente. |
| **TC-TOGU-03** | Auto-desactivación de Admin | El administrador intenta desactivar su propio usuario | La acción se bloquea con alerta roja: *"No puedes desactivar tu propia cuenta en sesión activa."*. |
