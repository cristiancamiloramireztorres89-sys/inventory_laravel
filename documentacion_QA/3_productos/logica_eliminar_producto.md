# Lógica QA: Eliminar Producto

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Validación defensiva con Eloquent (`exists()`). Si el producto tiene compras o ventas históricas, **no se permite la eliminación física** y se orienta al usuario a usar "Desactivar Producto". Si no tiene dependencias, se borra el registro y su archivo de imagen en disco.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `DELETE /admin/productos/{producto}` (Nombre: `admin.productos.destroy`)
- **Directiva Blade:** `@method('DELETE')` y `@csrf`
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\ProductoController@destroy`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador hace clic en eliminar producto y confirma en el modal.
2. **Validación de Dependencias Transaccionales:**
   ```php
   if ($producto->detalleVentas()->exists() || $producto->detalleCompras()->exists()) {
       return redirect()->route('admin.productos')
           ->with('error', 'No se puede eliminar físicamente el producto porque tiene compras o ventas registradas. Te recomendamos usar la opción de "Desactivar Producto".');
   }
   ```
3. **Limpieza de Imagen en Disco:**
   ```php
   if ($producto->imagen && file_exists(public_path('uploads/productos/' . $producto->imagen))) {
       @unlink(public_path('uploads/productos/' . $producto->imagen));
   }
   ```
4. Eliminación en BD:
   ```php
   $producto->delete();
   ```
5. Redirige con mensaje: *"Producto eliminado del inventario."*.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Precondición | Resultado Esperado |
|---|---|---|---|
| **TC-DELP-01** | Eliminar producto nuevo sin movimientos | Producto creado recientemente con 0 compras y 0 ventas | Eliminación exitosa de BD y eliminación de la imagen en `uploads/productos/`. |
| **TC-DELP-02** | Intentar eliminar producto con ventas | Producto con al menos 1 registro en `detalle_venta` | Acción rechazada, alerta roja sugiriendo desactivar el producto. El producto y su historial permanecen intactos. |
| **TC-DELP-03** | Intentar eliminar producto con compras | Producto con al menos 1 registro en `detalle_compra` | Acción rechazada con mensaje preventivo. |
