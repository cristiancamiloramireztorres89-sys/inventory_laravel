# Lógica QA: Eliminar Categoría

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Se realiza una verificación de relación mediante `$categoria->productos()->exists()` antes de proceder. Si existen productos en la categoría, la eliminación es rechazada con un mensaje claro.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `DELETE /admin/categorias/{categoria}` (Nombre: `admin.categorias.destroy`)
- **Directiva Blade:** `@method('DELETE')` y `@csrf`
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\CategoriaController@destroy`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador hace clic en eliminar categoría en la lista.
2. **Validación de Integridad Referencial:**
   ```php
   if ($categoria->productos()->exists()) {
       return redirect()->route('admin.categorias')
           ->with('error', 'No se puede eliminar la categoría porque contiene productos asociados.');
   }
   ```
3. Si no tiene productos vinculados:
   ```php
   $categoria->delete();
   ```
4. Redirige con éxito: *"Categoría eliminada correctamente."*.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Precondición | Resultado Esperado |
|---|---|---|---|
| **TC-DELCAT-01** | Categoría con productos | La categoría tiene al menos 1 producto vinculado | Error en pantalla: *"No se puede eliminar la categoría porque contiene productos asociados."*. La categoría permanece intacta. |
| **TC-DELCAT-02** | Categoría vacía | La categoría tiene 0 productos | Se elimina físicamente de la BD y desaparece de la vista. |
