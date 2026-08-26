# Módulo QA: Envío Automatizado de Factura por Correo Electrónico

## 📌 Especificación del Módulo
Al completar el registro de una venta en el punto de venta (POS), el sistema verifica si el cliente asignado tiene una dirección de correo electrónico válida. En caso afirmativo, genera automáticamente el comprobante en PDF en memoria y lo despacha como archivo adjunto a través del servicio de correos de Laravel.

---

## 🛣️ Componentes Involucrados
- **Controladores:**
  - `App\Http\Controllers\Admin\VentaController@store`
  - `App\Http\Controllers\Vendedor\VentaController@store`
- **Mailable:** `App\Mail\FacturaVentaMail`
- **Plantilla de Correo:** `resources/views/emails/factura_venta.blade.php`
- **Fachada de Mail:** `Illuminate\Support\Facades\Mail`

---

## 🔄 Flujo de Ejecución y Validaciones

1. Se confirma la transacción de la venta en base de datos (`DB::transaction`).
2. Se evalúa si el cliente posee correo registrado y con formato válido:
   ```php
   if ($nuevaVenta->cliente && !empty($nuevaVenta->cliente->correo) && filter_var($nuevaVenta->cliente->correo, FILTER_VALIDATE_EMAIL)) {
       try {
           // Generar el PDF en memoria sin guardarlo en disco
           $pdfContent = Pdf::loadView('ventas.factura_pos_pdf', ['venta' => $nuevaVenta])
               ->setPaper([0, 0, 226.77, 550], 'portrait')
               ->output();

           // Enviar el correo electrónico con el PDF adjunto
           Mail::to($nuevaVenta->cliente->correo)
               ->send(new FacturaVentaMail($nuevaVenta, $pdfContent));

           $mensajeExito .= " Factura POS enviada al correo {$nuevaVenta->cliente->correo}.";
       } catch (\Throwable $e) {
           // Si ocurre una contingencia de conexión o SMTP, la venta NO se revierte
       }
   }
   ```
3. **Estructura del Mailable (`FacturaVentaMail`):**
   - **Asunto Dinámico:** `"Tu Factura de Compra #VEN-0000X | Inventory System"`.
   - **Cuerpo del Mensaje:** Renderiza `emails.factura_venta.blade.php`, con diseño corporativo responsive, saludo personalizado, tabla de ítems adquiridos y valor total.
   - **Adjunto en Memoria:**
     ```php
     Attachment::fromData(fn () => $this->pdfContent, "Factura_POS_VEN-{$numeroFactura}.pdf")
         ->withMime('application/pdf')
     ```

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Datos del Cliente | Resultado Esperado |
|---|---|---|---|
| **TC-MAIL-01** | Venta a cliente con correo válido | Cliente con `correo`: `cliente@empresa.com` | Se genera el PDF, se envía el correo al cliente y la alerta de confirmación incluye: *"Factura POS enviada al correo cliente@empresa.com"*. |
| **TC-MAIL-02** | Venta a cliente sin correo (General) | Cliente sin correo o campo vacío | La venta se registra con normalidad, no se dispara el servicio de correo y no se arroja ninguna advertencia. |
| **TC-MAIL-03** | Falla del servidor de correo / Sin conexión | Simular desconexión SMTP | La venta se completa con éxito en la base de datos y la transacción de inventario permanece intacta (el bloque `try-catch` previene que una caída del servidor de correo aborte la venta). |
