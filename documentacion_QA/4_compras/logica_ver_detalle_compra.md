# Lógica QA: Ver Detalle de Compra

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Uso de **Eager Loading** de Eloquent (`with(['usuario', 'proveedor', 'detalles.producto'])`) para optimizar rendimiento (evita el problema de consultas N+1) y renderizado directo en vistas Blade o modales de detalle.

---

## 🛣️ Rutas y Vistas
- **Vistas:** `resources/views/admin/compras.blade.php` y `resources/views/vendedor/compras.blade.php`
- **Consulta Eloquent:**
  - Admin: `Compra::with(['usuario', 'proveedor', 'detalles.producto'])->orderBy('id_compra', 'desc')->get();`
  - Vendedor: `Compra::with(['proveedor', 'detalles.producto'])->where('id_usuario', Auth::id())->orderBy('id_compra', 'desc')->get();`

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Precondición | Resultado Esperado |
|---|---|---|---|
| **TC-DETC-01** | Visualizar compra con proveedor y usuario | Compra registrada | Muestra fecha, nombre del proveedor, nombre del usuario que registró, producto, cantidad, costo unitario y total. |
| **TC-DETC-02** | Filtrado por Vendedor | Iniciar sesión como Vendedor A | Solo aparecen las compras de Vendedor A. Las compras de Vendedor B o del Administrador están excluidas. |
