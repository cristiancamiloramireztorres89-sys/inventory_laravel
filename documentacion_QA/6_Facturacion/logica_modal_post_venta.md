# Módulo QA: Modal Post-Venta y Acciones Inmediatas de Factura

## 📌 Especificación del Módulo
Provee una ventana modal emergente interactiva que se activa inmediatamente después de registrar exitosamente una venta. Ofrece acceso directo con un clic para imprimir el ticket térmico o descargar el archivo PDF.

---

## 🛣️ Componentes Involucrados
- **Disparador en Controlador:** Variable flash de sesión `venta_creada_id`:
  ```php
  return redirect()->route('admin.ventas')
      ->with('success', $mensajeExito)
      ->with('venta_creada_id', $nuevaVenta->id_venta);
  ```
- **Vistas Parciales Blade:**
  - `resources/views/admin/ventas_partials/modal_exito.blade.php`
  - `resources/views/vendedor/ventas_partials/modal_exito.blade.php`

---

## 🔄 Flujo de Ejecución y Validaciones

1. Una vez guardada la venta, la redirección transporta en la sesión flash el identificador de la venta: `session('venta_creada_id')`.
2. La directiva condicional de Blade detecta la variable en la carga de la página:
   ```blade
   @if(session('venta_creada_id'))
   <div id="modalVentaCreadaExito" class="fixed inset-0 z-50 ...">
       ...
       <!-- Botón Imprimir Ticket POS -->
       <a href="{{ route('admin.ventas.factura', session('venta_creada_id')) }}" target="_blank">
           Imprimir Ticket POS
       </a>
       <!-- Botón Descargar PDF -->
       <a href="{{ route('admin.ventas.factura.pdf', session('venta_creada_id')) }}" target="_blank">
           Descargar PDF
       </a>
       <!-- Botón Cerrar -->
       <button onclick="document.getElementById('modalVentaCreadaExito').remove()">
           Cerrar y Continuar
       </button>
   </div>
   @endif
   ```
3. Los enlaces abren en una nueva pestaña (`target="_blank"`), permitiendo enviar el ticket a la impresora sin perder la vista actual del listado de ventas.
4. Al hacer clic en "Cerrar y Continuar", se remueve el modal del DOM y el usuario puede continuar operando.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Acción / Precondición | Resultado Esperado |
|---|---|---|---|
| **TC-MODAL-01** | Despliegue automático tras venta | Registrar una venta exitosamente | El modal se abre en primer plano con el número de factura `#VEN-0000X`. |
| **TC-MODAL-02** | Botón "Imprimir Ticket POS" | Clic en el botón negro de impresión | Se abre en nueva pestaña el ticket térmico listo para `window.print()`. |
| **TC-MODAL-03** | Botón "Descargar PDF" | Clic en el botón rojo de PDF | Se abre en nueva pestaña el documento PDF generado. |
| **TC-MODAL-04** | Cerrar modal | Clic en "Cerrar y Continuar" | El modal se retira de pantalla y el listado de ventas queda visible. |
| **TC-MODAL-05** | Recarga de página | Presionar F5 en el navegador tras cerrar modal | El modal no vuelve a aparecer (la variable de sesión flash se consume en la primera petición). |
