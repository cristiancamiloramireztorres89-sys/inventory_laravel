# Lógica QA: Eliminar Compra

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel:
  1. **Control de Acceso Estricto:** El Administrador puede eliminar cualquier compra; el Vendedor **solo puede eliminar compras registradas por él mismo** (`$compra->id_usuario !== Auth::id()` dispara `abort(403)`).
  2. **Reversión Atómica de Stock:** `DB::transaction` ejecuta `$detalle->producto->decrement('stock_actual', $detalle->cantidad)` antes de borrar la compra.

---

## 🛣️ Rutas y Controladores
- **Administrador:** `DELETE /admin/compras/{compra}` -> `Admin\CompraController@destroy`
- **Vendedor:** `DELETE /vendedor/compras/{compra}` -> `Vendedor\CompraController@destroy`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El usuario confirma eliminar la compra en la tabla de historial.
2. Si es rol Vendedor:
   ```php
   if ($compra->id_usuario !== Auth::id()) {
       abort(403, 'No tienes permiso para eliminar esta compra.');
   }
   ```
3. Ejecución de la Transacción Atómica:
   ```php
   DB::transaction(function () use ($compra) {
       foreach ($compra->detalles as $detalle) {
           if ($detalle->producto) {
               $detalle->producto->decrement('stock_actual', $detalle->cantidad);
           }
       }
       $compra->delete();
   });
   ```
4. Redirige con mensaje: *"Compra eliminada exitosamente y stock ajustado en el inventario."*.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Precondición | Resultado Esperado |
|---|---|---|---|
| **TC-DELC-01** | Eliminar compra por Admin | Compra de 5 unidades registrada previamente | La compra se elimina y el stock del producto disminuye en 5 unidades. |
| **TC-DELC-02** | Vendedor elimina su propia compra | Compra realizada por el vendedor en sesión | Eliminación exitosa y descuento correspondiente de stock. |
| **TC-DELC-03** | Vendedor intenta eliminar compra ajena | Compra creada por otro vendedor o el admin | Respuesta HTTP `403 Forbidden` inmediata. |
