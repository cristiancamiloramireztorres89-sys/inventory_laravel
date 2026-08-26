# Módulo QA: Facturación POS Térmica (80mm)

## 📌 Especificación del Módulo
El sistema cuenta con un submódulo de facturación tipo punto de venta (POS) diseñado específicamente para emitir tickets de compra estándar de 80mm de ancho. Provee una interfaz HTML optimizada para impresión directa con cualquier impresora térmica o miniprinter de tickets.

---

## 🛣️ Rutas y Controladores
- **Administrador:** `GET /admin/ventas/{venta}/factura` (Nombre: `admin.ventas.factura`) -> `App\Http\Controllers\Admin\VentaController@factura`
- **Vendedor:** `GET /vendedor/ventas/{venta}/factura` (Nombre: `vendedor.ventas.factura`) -> `App\Http\Controllers\Vendedor\VentaController@factura`
- **Middleware:** `auth`, `role:administrador` o `role:vendedor`
- **Vista Blade:** `resources/views/ventas/factura_pos.blade.php`

---

## 🔄 Flujo de Ejecución y Reglas de Negocio

1. El usuario solicita visualizar el ticket de venta desde el listado de ventas o desde el modal automático post-venta.
2. **Validación de Seguridad por Rol:**
   - En el controlador del Vendedor:
     ```php
     if ($venta->id_usuario !== Auth::id()) {
         abort(403, 'No tienes permiso para ver el comprobante de esta venta.');
     }
     ```
     Un vendedor no puede visualizar facturas correspondientes a transacciones de otros usuarios.
3. **Carga Eficiente de Datos (Eager Loading):**
   ```php
   $venta->load(['usuario', 'cliente', 'detalles.producto']);
   ```
4. **Composición del Ticket Térmico:**
   - **Encabezado Comercial:** Razón social ("INVENTORY SYSTEM"), NIT, dirección del local y teléfonos.
   - **Metadatos de la Venta:** Número de factura formateado a 5 dígitos (`#VEN-00001`), fecha/hora con `Carbon::format('d/m/Y h:i A')`, y cajero/a responsable.
   - **Información del Cliente:** Nombre del cliente, teléfono y correo electrónico.
   - **Tabla Detalle de Productos:**
     - Cantidad adquirida.
     - Descripción del producto y precio unitario aplicado (`$detalle->cantidad . ' x $' . number_format($detalle->precio_unitario, 2)`).
     - Subtotal por renglón.
   - **Cálculo de Totales:** Total de artículos (sumatoria de unidades), subtotal, IVA desglosado y Total a pagar en formato moneda.
   - **Pie Legal:** Mensaje de cortesía, código de barras simulado y autorización fiscal DIAN.
5. **Estilos de Impresión CSS (`@media print`):**
   ```css
   @page {
       size: 80mm auto;
       margin: 0;
   }
   @media print {
       .no-print { display: none !important; }
       body { background: transparent; }
   }
   ```
   Al presionar el botón "Imprimir", el navegador invoca `window.print()`, enviando únicamente el cuerpo del ticket a la impresora configurada sin márgenes residuales ni elementos de interfaz.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Acción / Precondición | Resultado Esperado |
|---|---|---|---|
| **TC-FAC-01** | Visualización de ticket térmico por Administrador | Admin abre cualquier venta registrada | Renderiza el ticket completo con datos de comercio, cliente, ítems, totales y botones de acción ("Volver", "PDF", "Imprimir"). |
| **TC-FAC-02** | Impresión directa con botón | Hacer clic en "Imprimir" | Se dispara el cuadro de diálogo de impresión del sistema (`window.print()`) con ancho fijo de 80mm. |
| **TC-FAC-03** | Visualización permitida para Vendedor | Vendedor abre una venta registrada por él | Visualización exitosa de su propio comprobante. |
| **TC-FAC-04** | Bloqueo de visualización cruzada | Vendedor intenta acceder a una venta de otro usuario por URL | Se bloquea el acceso con respuesta HTTP `403 Forbidden`. |
