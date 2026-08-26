# Lógica QA: Eliminar Usuario

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Se verifica mediante relaciones Eloquent (`ventas()`, `compras()`) antes de intentar cualquier eliminación. Si el usuario tiene ventas o compras históricas registradas, la eliminación física se rechaza educadamente, recomendando la desactivación.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `DELETE /admin/usuarios/{usuario}` (Nombre: `admin.usuarios.destroy`)
- **Directiva Blade:** `@method('DELETE')` y `@csrf`
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\UsuarioController@destroy`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador confirma la eliminación en el modal de confirmación.
2. **Validación 1: Auto-eliminación prohibida:**
   ```php
   if ($usuario->id_usuario === Auth::id()) {
       return redirect()->route('admin.usuarios')
           ->with('error', 'No puedes eliminar tu propia cuenta de usuario en sesión activa.');
   }
   ```
3. **Validación 2: Integridad Transaccional:**
   ```php
   $totalVentas  = $usuario->ventas()->count();
   $totalCompras = $usuario->compras()->count();

   if ($totalVentas > 0 || $totalCompras > 0) {
       return redirect()->route('admin.usuarios')
           ->with('error', "No se puede eliminar a '{$usuario->nombre}' porque tiene {$detalle} asociadas en el historial. Puedes desactivar su cuenta para revocarle el acceso.");
   }
   ```
4. Si no tiene historial transaccional:
   ```php
   $usuario->delete();
   ```
5. Redirige con mensaje: *"El usuario '{nombre}' ha sido eliminado exitosamente."*.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Precondición | Resultado Esperado |
|---|---|---|---|
| **TC-DELU-01** | Eliminar usuario sin historial | Usuario recién creado sin compras ni ventas | Eliminación física exitosa en la base de datos y mensaje verde. |
| **TC-DELU-02** | Eliminar usuario con ventas | Vendedor con al menos 1 venta en el sistema | Acción rechazada con alerta descriptiva indicando la cantidad de ventas asociadas. |
| **TC-DELU-03** | Auto-eliminación | Administrador intenta eliminarse a sí mismo | Bloqueo inmediato con alerta: *"No puedes eliminar tu propia cuenta de usuario..."*. |
