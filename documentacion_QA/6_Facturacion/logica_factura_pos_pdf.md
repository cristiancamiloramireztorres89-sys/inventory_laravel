# Módulo QA: Facturación POS en PDF (DomPDF)

## 📌 Especificación del Módulo
Permite compilar el ticket de venta en un documento digital PDF con formato específico de bobina térmica (80mm), apto para descarga local, archivo digital o reimpresión. Utiliza el paquete oficial `barryvdh/laravel-dompdf`.

---

## 🛣️ Rutas y Controladores
- **Administrador:** `GET /admin/ventas/{venta}/factura-pdf` (Nombre: `admin.ventas.factura.pdf`) -> `App\Http\Controllers\Admin\VentaController@facturaPdf`
- **Vendedor:** `GET /vendedor/ventas/{venta}/factura-pdf` (Nombre: `vendedor.ventas.factura.pdf`) -> `App\Http\Controllers\Vendedor\VentaController@facturaPdf`
- **Middleware:** `auth`, `role:administrador` o `role:vendedor`
- **Plantilla Blade:** `resources/views/ventas/factura_pos_pdf.blade.php`

---

## 🔄 Flujo de Ejecución y Generación de PDF

1. El usuario solicita la descarga o visualización del PDF.
2. **Validación de Acceso:** Se verifica que el usuario autenticado tenga el rol de administrador o sea el propietario de la venta.
3. **Carga de Relaciones:** Se cargan los datos del cliente, usuario y detalles de productos.
4. **Configuración del Lienzo (Paper Size):**
   ```php
   // Ancho: 80mm (~226.77 puntos PostScript), Alto: 550 puntos
   $customPaper = [0, 0, 226.77, 550];

   $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('ventas.factura_pos_pdf', compact('venta'))
       ->setPaper($customPaper, 'portrait');
   ```
5. **Streaming HTTP del Documento:**
   ```php
   return $pdf->stream('Factura_POS_' . str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) . '.pdf');
   ```
   El archivo se devuelve con cabeceras `Content-Type: application/pdf` para apertura directa en el visor del navegador.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Acción / Precondición | Resultado Esperado |
|---|---|---|---|
| **TC-PDF-01** | Generación de PDF por Administrador | Clic en botón "PDF" en la tabla de ventas | El navegador abre el stream del archivo PDF con nombre estructurado `Factura_POS_0000X.pdf`. |
| **TC-PDF-02** | Dimensiones correctas del PDF | Inspeccionar propiedades del documento en el visor PDF | El ancho de página coincide con la proporción de bobina de 80mm. |
| **TC-PDF-03** | Bloqueo a Vendedor sobre venta ajena | Vendedor intenta descargar PDF de venta ajena | Retorna error HTTP `403 Forbidden`. |
