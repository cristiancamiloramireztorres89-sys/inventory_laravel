# Lógica QA: Desactivar / Activar Producto (Toggle Estado)

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Se implementó el campo booleano `activo` y la ruta de toggle. El catálogo del Vendedor utiliza el scope de Eloquent `Producto::activos()`, garantizando que productos discontinuados u obsoletos no aparezcan para venta.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `PATCH /admin/productos/{producto}/toggle` (Nombre: `admin.productos.toggle`)
- **Directiva Blade:** `@method('PATCH')` y `@csrf`
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\ProductoController@toggleStatus`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador presiona el botón de Desactivar/Activar en la tabla de productos.
2. Inversión del valor:
   ```php
   $producto->activo = ! (bool) $producto->activo;
   $producto->save();
   ```
3. Generación de mensaje descriptivo:
   - Si se activa: *"El producto '{nombre}' ha sido activado y volverá a ser visible para los vendedores."*
   - Si se desactiva: *"El producto '{nombre}' ha sido desactivado. Ya no aparecerá en el catálogo del vendedor."*
4. **Impacto en el sistema:**
   - En el panel de Administrador: Se sigue listando con badge indicador ("Activo" / "Inactivo").
   - En el panel del Vendedor (`Vendedor\ProductoController` y `Vendedor\VentaController`): `Producto::activos()` filtra y excluye el producto.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Acción Realizada | Resultado Esperado |
|---|---|---|---|
| **TC-TOGP-01** | Desactivar producto | Presionar toggle sobre producto activo | Pasa a inactivo. Iniciar sesión como vendedor y verificar que el producto ya no aparece disponible para la venta. |
| **TC-TOGP-02** | Reactivar producto | Presionar toggle sobre producto inactivo | Pasa a activo. Vuelve a ser visible y seleccionable para los vendedores. |
