# Lógica QA: Análisis de Ganancias y Rentabilidad

## 📌 Contexto y Especificación Técnica
- **Controladores:**
  - Admin: `App\Http\Controllers\Admin\GananciaController@index`
  - Vendedor: `App\Http\Controllers\Vendedor\GananciaController@index`
- **Vistas:** `resources/views/admin/ganancias.blade.php` y `resources/views/vendedor/ganancias.blade.php`

---

## 🔄 Lógica Financiera de Rentabilidad

1. **Costo de Adquisición Unitario:** Al momento de cada venta, el sistema capturó el costo unitario de la última compra registrada para ese producto:
   ```php
   $costoUnitario = DetalleCompra::where('id_producto', $idProd)
       ->latest('id_detalle_compra')
       ->value('precio_unitario') ?? 0.00;
   ```
2. **Cálculo de Ganancia por Línea de Venta:**
   ```text
   Ganancia = Subtotal Venta - (Costo Unitario * Cantidad)
   ```
3. **Margen Porcentual de Ganancia:**
   ```text
   Margen (%) = (Ganancia / Costo Total) * 100
   ```
4. **Diferenciación por Roles:**
   - **Administrador:** Analiza todas las ventas históricas de la empresa, márgenes consolidados y los 5 productos más rentables de toda la tienda.
   - **Vendedor:** Analiza únicamente las ventas generadas por su usuario (`where('id_usuario', Auth::id())`), mostrando su rendimiento de comisiones/ganancias personal.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Condición | Resultado Esperado |
|---|---|---|---|
| **TC-GAN-01** | Cálculo de rentabilidad positivo | Venta de $200 de mercancía que costó $120 | Ingreso: $200, Costo: $120, Ganancia: $80, Margen: 66.7%. |
| **TC-GAN-02** | Vendedor consulta panel de ganancias | Usuario con rol Vendedor ingresa a `/vendedor/ganancias` | Visualiza únicamente sus números personales. No puede ver las ventas ni ganancias de otros vendedores ni del administrador. |
