# Lógica QA: Eliminar Venta y Restaurar Stock

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel:
  1. **Restauración Automática de Stock:** Mediante `DB::transaction`, itera por cada detalle de la venta y ejecuta `$detalle->producto->increment('stock_actual', $detalle->cantidad)` devolviendo las existencias exactas al catálogo.
  2. **Permisos:** El Administrador puede anular cualquier venta; el Vendedor **solo puede anular sus propias ventas** (`abort(403)` en caso contrario).

---

## 🛣️ Rutas y Controladores
- **Administrador:** `DELETE /admin/ventas/{venta}` -> `Admin\VentaController@destroy`
- **Vendedor:** `DELETE /vendedor/ventas/{venta}` -> `Vendedor\VentaController@destroy`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El usuario solicita eliminar una venta en el listado y confirma en el modal de alerta.
2. Si es rol Vendedor:
   ```php
   if ($venta->id_usuario !== Auth::id()) {
       abort(403, 'No tienes permiso para eliminar esta venta.');
   }
   ```
3. Ejecución de la Restauración Atómica:
   ```php
   DB::transaction(function () use ($venta) {
       foreach ($venta->detalles as $detalle) {
           if ($detalle->producto) {
               $detalle->producto->increment('stock_actual', $detalle->cantidad);
           }
       }
       $venta->delete();
   });
   ```
4. Redirige con mensaje: *"Venta eliminada exitosamente y stock restaurado en el inventario."*.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Precondición | Resultado Esperado |
|---|---|---|---|
| **TC-DELV-01** | Eliminación de venta multi-producto | Venta con 2 teclados y 1 monitor | Venta eliminada. El stock de teclados sube en +2 y el de monitores sube en +1. |
| **TC-DELV-02** | Vendedor intenta eliminar venta ajena | Vendedor B intenta enviar DELETE a venta de Vendedor A | Error HTTP `403 Forbidden`. |
