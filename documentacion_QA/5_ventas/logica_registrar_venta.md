# Lógica QA: Registrar Venta POS (Multi-producto)

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel:
  1. **Multi-Producto Completo:** Admite carritos con múltiples productos a la vez (vía array `items` o payload JSON `items_json`).
  2. **Cliente al Vuelo (Inline):** Permite elegir un cliente existente o registrar uno nuevo instantáneamente con nombre, teléfono y correo.
  3. **Verificación Previa de Stock:** Valida las existencias de todos los productos antes de iniciar la transacción. Si uno falla, la venta no se inicia.
  4. **Captura de Costo Unitario Histórico:** Obtiene el costo de adquisición de la última compra para calcular ganancias reales exactas.
  5. **Facturación Automática:** Genera el ticket PDF POS (80mm) con DomPDF y lo envía por correo electrónico al cliente si tiene correo válido.

---

## 🛣️ Rutas y Controladores
- **Administrador:** `POST /admin/ventas` (Nombre: `admin.ventas.store`) -> `Admin\VentaController@store`
- **Vendedor:** `POST /vendedor/ventas` (Nombre: `vendedor.ventas.store`) -> `Vendedor\VentaController@store`
- **Vistas:** `resources/views/admin/ventas.blade.php` y `resources/views/vendedor/ventas.blade.php`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El usuario agrega uno o más productos al carrito de venta en la vista.
2. **Determinación del Cliente:**
   - Existente: `id_cliente: exists:clientes,id_cliente`.
   - Nuevo: Si `id_cliente === 'nuevo'` o llena `nuevo_cliente_nombre`:
     ```php
     $request->validate([
         'nuevo_cliente_nombre'   => ['required', 'string', 'max:100'],
         'nuevo_cliente_telefono' => ['nullable', 'string', 'max:50'],
         'nuevo_cliente_correo'   => ['nullable', 'email', 'max:100'],
     ]);
     $cliente = Cliente::create([...]);
     $idCliente = $cliente->id_cliente;
     ```
3. **Validación Exhaustiva de Artículos y Stock Disponible:**
   ```php
   foreach ($items as $item) {
       $producto = Producto::find($idProd);
       if (!$producto) {
           return back()->withInput()->with('error', 'Uno de los productos seleccionados no existe...');
       }
       if ($producto->stock_actual < $cant) {
           return back()->withInput()->with('error', "Stock insuficiente para '{$producto->nombre}'. Existencias disponibles: {$producto->stock_actual} unidades (solicitadas: {$cant}).");
       }
       ...
   }
   ```
4. **Transacción Atómica de Venta:**
   ```php
   $nuevaVenta = DB::transaction(function () use ($idCliente, $itemsValidados, $totalVenta) {
       $venta = Venta::create([
           'id_usuario' => Auth::id(),
           'id_cliente' => $idCliente,
           'fecha'      => now(),
           'subtotal'   => $totalVenta,
           'iva'        => 0,
           'total'      => $totalVenta,
       ]);

       foreach ($itemsValidados as $item) {
           $costoUnitario = DetalleCompra::where('id_producto', $item['id_producto'])
               ->latest('id_detalle_compra')
               ->value('precio_unitario') ?? 0.00;

           DetalleVenta::create([
               'id_venta'        => $venta->id_venta,
               'id_producto'     => $item['id_producto'],
               'cantidad'        => $item['cantidad'],
               'costo_unitario'  => $costoUnitario,
               'precio_unitario' => $item['precio_unitario'],
               'subtotal'        => $item['subtotal'],
           ]);

           $item['producto']->decrement('stock_actual', $item['cantidad']);
       }

       return $venta;
   });
   ```
5. **Envío de Correo con PDF Adjunto:**
   Si el cliente tiene correo válido, se compila la vista `ventas.factura_pos_pdf` en PDF (ancho 80mm: `[0, 0, 226.77, 550]`) y se envía vía `Mail::to()->send(new FacturaVentaMail)`.
6. Redirige a la lista con mensaje de éxito y abre el modal/alerta de factura lista.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Acción / Datos | Resultado Esperado |
|---|---|---|---|
| **TC-VENT-01** | Venta multi-producto exitosa | 2 Laptops y 3 Mouses para un cliente existente | Venta guardada, `stock_actual` de laptops baja en 2 y de mouses baja en 3. Alerta verde con ID de venta. |
| **TC-VENT-02** | Venta con cliente nuevo al vuelo | Datos de nuevo cliente (Nombre, tel, email) | Cliente guardado en tabla `clientes`, ligado a la venta. Factura PDF enviada a su correo. |
| **TC-VENT-03** | Stock insuficiente | Producto con stock = 2, se solicitan 5 unidades | La venta se rechaza inmediatamente sin alterar la BD: *"Stock insuficiente para '{nombre}'. Existencias disponibles: 2 unidades..."*. |
| **TC-VENT-04** | Carrito vacío | Enviar formulario sin productos | Error: *"Debes agregar al menos un producto a la venta."*. |
| **TC-VENT-05** | Falla de conexión de correo | Cliente con correo pero sin internet/SMTP configurado | La venta se registra exitosamente y no se bloquea por la excepción del correo (capturada con `try/catch`). |
