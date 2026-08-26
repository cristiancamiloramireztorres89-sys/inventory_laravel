# Lógica QA: Ver Detalle de Venta

## 📌 Contexto y Especificación Técnica
- **Vistas:** `resources/views/admin/ventas.blade.php` y `resources/views/vendedor/ventas.blade.php`.
- **Eager Loading:** Las ventas se cargan con `with(['usuario', 'cliente', 'detalles.producto'])`.
- **Regla de Negocio:**
  - Administrador: Visualiza todas las ventas del negocio con el nombre del vendedor que la realizó.
  - Vendedor: Visualiza exclusivamente sus propias ventas (`where('id_usuario', Auth::id())`).
