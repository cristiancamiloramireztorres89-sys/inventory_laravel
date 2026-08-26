# Lógica QA: Registrar Compra / Abastecimiento

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel:
  1. Permite **crear un nuevo proveedor al vuelo (inline)** desde el mismo formulario modal sin salir del flujo de compra.
  2. Utiliza una transacción atómica `DB::transaction(...)`: inserta encabezado de compra, inserta detalle y ejecuta `$producto->increment('stock_actual', $cantidad)`.

---

## 🛣️ Rutas y Controladores
- **Administrador:** `POST /admin/compras` (Nombre: `admin.compras.store`) -> `Admin\CompraController@store`
- **Vendedor:** `POST /vendedor/compras` (Nombre: `vendedor.compras.store`) -> `Vendedor\CompraController@store`
- **Vistas:** `resources/views/admin/compras.blade.php` y `resources/views/vendedor/compras.blade.php`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El usuario abre el modal "Registrar Abastecimiento".
2. **Manejo Dinámico del Proveedor:**
   - **Caso A (Proveedor Existente):** Selecciona de la lista (`id_proveedor: exists:proveedores,id_proveedor`).
   - **Caso B (Nuevo Proveedor al vuelo):** Si `id_proveedor === 'nuevo'` o llena `nuevo_proveedor_nombre`:
     ```php
     $request->validate([
         'nuevo_proveedor_nombre'   => ['required', 'string', 'max:100'],
         'nuevo_proveedor_telefono' => ['nullable', 'string', 'max:50'],
         'nuevo_proveedor_correo'   => ['nullable', 'email', 'max:100'],
     ]);
     $proveedor = Proveedor::create([...]);
     $idProveedor = $proveedor->id_proveedor;
     ```
3. **Validación de Producto y Costos:**
   ```php
   $validated = $request->validate([
       'id_producto'     => ['required', 'integer', 'exists:productos,id_producto'],
       'cantidad'        => ['required', 'integer', 'min:1'],
       'precio_unitario' => ['required', 'numeric', 'min:0.01'],
   ]);
   ```
4. **Transacción Atómica de Base de Datos:**
   ```php
   DB::transaction(function () use ($idProveedor, $validated, $subtotal) {
       $compra = Compra::create([
           'id_usuario'   => Auth::id(),
           'id_proveedor' => $idProveedor,
           'fecha'        => now(),
           'subtotal'     => $subtotal,
           'iva'          => 0,
           'total'        => $subtotal,
       ]);

       DetalleCompra::create([
           'id_compra'       => $compra->id_compra,
           'id_producto'     => $validated['id_producto'],
           'cantidad'        => $validated['cantidad'],
           'precio_unitario' => $validated['precio_unitario'],
           'subtotal'        => $subtotal,
       ]);

       $producto = Producto::find($validated['id_producto']);
       $producto->increment('stock_actual', $validated['cantidad']);
   });
   ```
5. Redirección con mensaje de confirmación y stock abastecido.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Acción / Datos | Resultado Esperado |
|---|---|---|---|
| **TC-COMP-01** | Compra con proveedor existente | Proveedor seleccionado, 10 unidades a $50 c/u | Compra registrada con total $500, stock del producto incrementado en exactamente 10 unidades. |
| **TC-COMP-02** | Compra con proveedor nuevo al vuelo | Escribir nombre "Distribuidora Los Andes", tel y correo | Proveedor creado en tabla `proveedores`, compra ligada al nuevo ID y stock incrementado. |
| **TC-COMP-03** | Cantidad cero o negativa | `cantidad`: `0` o `-5` | Rechazado por validación `min:1`. |
| **TC-COMP-04** | Costo unitario cero | `precio_unitario`: `0` | Rechazado por validación `min:0.01`. |
| **TC-COMP-05** | Simulación de fallo en BD | Interrupción en la creación del detalle | `DB::transaction` hace Rollback: ni la compra se registra ni el stock se incrementa. |
