# Lógica QA: Dashboard y Métricas Ejecutivas

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Métricas en tiempo real con Eloquent, cálculo de valor de inventario, costo de mercancía vendida, margen de ganancia global y alertas de stock bajo basadas en comparación de columnas (`whereColumn('stock_actual', '<=', 'stock_minimo')`).

---

## 🛣️ Rutas y Controladores
- **Administrador:** `GET /admin/dashboard` -> `Admin\DashboardController@index`
- **Vendedor:** `GET /vendedor/dashboard` -> `Vendedor\DashboardController@index`
- **Vistas:** `resources/views/admin/dashboardadmin.blade.php` y `resources/views/vendedor/dashboard.blade.php`

---

## 🔄 Indicadores Clave (KPIs) Calculados

1. **Total de Usuarios, Productos y Categorías:** Conteo con Eloquent (`Usuario::count()`, etc.).
2. **Unidades Totales en Existencia:** `Producto::sum('stock_actual')`.
3. **Métricas Financieras:**
   - Total Recaudado (Ventas): `$ventas->sum('total')`.
   - Costo Total de Ventas: `$ventas->sum(fn ($v) => $v->costo_total)`.
   - Ganancia Neta Total: `Total Recaudado - Costo Total`.
   - Margen Global: `(Ganancia Neta / Costo Total) * 100`.
4. **Alertas de Stock Bajo:**
   ```php
   $productosStockBajo = Producto::with('categoria')
       ->whereColumn('stock_actual', '<=', 'stock_minimo')
       ->orderBy('stock_actual', 'asc')
       ->get();
   ```
5. **Top 5 Productos Más Rentables:** Agrupación de `DetalleVenta` calculando `ingresos - costo`.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Condición en Base de Datos | Resultado Esperado en Dashboard |
|---|---|---|---|
| **TC-DASH-01** | Alerta de Stock Bajo activada | Producto con `stock_actual = 3` y `stock_minimo = 5` | El producto aparece en la tarjeta de Alertas de Stock Bajo con llamada de atención. |
| **TC-DASH-02** | Alerta de Stock Bajo ignorada | Producto con `stock_actual = 10` y `stock_minimo = 5` | El producto no entra en las alertas. |
| **TC-DASH-03** | Consistencia de Ganancias | Registrar venta de $100 con costo de $60 | El dashboard refleja Ganancia Neta de $40 y margen de 66.7%. |
