# Lógica QA: Toggle Estado Categoría

## 📌 Estado de la Funcionalidad y Especificación Técnica
- **Comportamiento en el Sistema:**
  A diferencia de los **usuarios** y los **productos** (que cuentan con el campo booleano `activo` en base de datos), la tabla `categorias` no posee campo de estado activo/inactivo.
- **Estructura de la tabla `categorias`:**
  - `id_categoria` (PK, auto-increment)
  - `nombre` (varchar 100, unique)
  - `descripcion` (varchar 255, nullable)
  - `created_at`, `updated_at` (timestamps)
- **Criterio de Aceptación QA:**
  No existe botón de activación/desactivación en la vista `admin/categorias.blade.php`. Toda categoría creada está disponible inmediatamente para clasificar productos.
