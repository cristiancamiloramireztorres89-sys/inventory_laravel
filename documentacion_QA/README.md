# Documentación QA - Sistema de Inventario y Facturación POS (Laravel 11)

Esta documentación técnica de aseguramiento de calidad (QA) describe la arquitectura, lógica de negocio, flujos de ejecución, reglas de validación, transacciones atómicas y casos de prueba del **Sistema de Inventario y Facturación desarrollado en Laravel**.

---

## 🏛️ Arquitectura del Sistema Laravel

El sistema está construido siguiendo los estándares de **Laravel 11**:
- **Enrutamiento:** Rutas RESTful organizadas en `routes/web.php` y `routes/auth.php` con verbos HTTP (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`).
- **Seguridad:** Middleware `VerifyCsrfToken` con directiva `@csrf` en todos los formularios, limitación de tasa de autenticación (**Rate Limiting** de 5 intentos máx.) y contraseñas cifradas con **Bcrypt**.
- **Modelos y Persistencia:** **Eloquent ORM** con relaciones (`belongsTo`, `hasMany`), scopes de consulta (`Producto::activos()`), accessors y transacciones atómicas mediante `DB::transaction(...)`.
- **Manejo de Formularios y Validación:** Solicitudes tipadas (`LoginRequest`) y validación nativa `$request->validate([...])` con mensajes personalizados en español.
- **Vistas y Componentes:** Motor de plantillas **Blade** con componentes modulares, layout maestro (`layouts/app.blade.php`) y alertas de sesión flash (`with('success', ...)`).
- **Facturación y Reportes:** Emisión de comprobantes térmicos POS de 80mm en HTML y PDF mediante `Barryvdh\DomPDF`, con envío automatizado de correos vía Mailables (`FacturaVentaMail`).

---

## 📁 Módulos del Sistema Documentados para QA

```text
documentacion_QA/
├── README.md                              # Índice maestro, arquitectura y matriz de roles
│
├── 1_Autenticacion_y_Usuarios/
│   ├── logica_login.md                    # Autenticación, Rate Limiting (5 intentos máx.) y sesión
│   ├── logica_logout.md                   # Invalidación de guard web y regeneración de token
│   ├── logica_recuperacion_contrasena.md  # Flujo OTP de 3 pasos por correo con código de 6 dígitos
│   ├── logica_crear_usuario.md            # Registro con validación unique y hash Bcrypt
│   ├── logica_editar_usuario.md           # Actualización con exclusión de ID y clave opcional
│   ├── logica_toggle_estado_usuario.md    # Desactivación y bloqueo de auto-desactivación en sesión
│   ├── logica_eliminar_usuario.md         # Restricción por compras/ventas previas y auto-eliminación
│   └── logica_registro.md                 # Política de creación de cuentas administrativas
│
├── 2_categorias/
│   ├── logica_crear_categoria.md          # Registro con regla de unicidad en tabla categorias
│   ├── logica_editar_categoria.md         # Edición con validación unique ignorando ID propio
│   ├── logica_eliminar_categoria.md       # Verificación referencial con productos asociados
│   └── logica_toggle_estado_categoria.md  # Especificación de esquema de categorías
│
├── 3_productos/
│   ├── logica_crear_producto.md          # Catálogo con categoría, stock e imagen (uploads/productos)
│   ├── logica_editar_producto.md          # Actualización y reemplazo de imagen con borrado de disco
│   ├── logica_toggle_estado_producto.md   # Desactivación y filtro automático para catálogo de ventas
│   └── logica_eliminar_producto.md        # Bloqueo por transacciones previas y limpieza física
│
├── 4_compras/
│   ├── logica_registrar_compra.md         # Abastecimiento atómico (DB::transaction) y proveedor al vuelo
│   ├── logica_eliminar_compra.md          # Reversión de compras y decremento de existencias
│   └── logica_ver_detalle_compra.md       # Eager loading con relaciones Eloquent
│
├── 5_ventas/
│   ├── logica_registrar_venta.md          # Venta multi-producto, cliente al vuelo y descuento de stock
│   ├── logica_eliminar_venta.md           # Reversión de venta y restauración de existencias
│   └── logica_detalle_venta.md            # Visualización por roles (global admin vs. propias vendedor)
│
├── 6_Facturacion/
│   ├── logica_factura_pos_termica.md      # Comprobante térmico POS 80mm con CSS @media print
│   ├── logica_factura_pos_pdf.md          # Compilación a PDF con Barryvdh DomPDF y streaming
│   ├── logica_envio_factura_correo.md     # Despacho automático de PDF adjunto con FacturaVentaMail
│   └── logica_modal_post_venta.md         # Modal de confirmación inmediata con accesos rápidos
│
├── 7_Ganancias_y_Dashboard/
│   ├── logica_dashboard_metricas.md       # KPIs ejecutivos, inventario valorizado y alertas de reposición
│   └── logica_analisis_ganancias.md       # Margen de rentabilidad, costo histórico y Top 5 productos
│
└── 8_Fotos_y_Multimedia/
    └── logica_subir_fotos_productos.md    # Validación MIME, nombrado criptográfico y ciclo de vida
```

---

## 🗄️ Modelos Eloquent y Estructura de Datos

| Modelo (`app/Models/`) | Tabla | Relaciones y Métricas Clave |
|---|---|---|
| `Usuario` | `usuarios` | `belongsTo(Role)`, `hasMany(Venta)`, `hasMany(Compra)`. Control de estado `activo`. |
| `Role` | `roles` | Roles del sistema: `administrador` y `vendedor`. |
| `Categoria` | `categorias` | `hasMany(Producto)`. Conteo de productos vinculados (`withCount`). |
| `Producto` | `productos` | `belongsTo(Categoria)`, `hasMany(DetalleVenta)`, `hasMany(DetalleCompra)`. Scope `activos()`. |
| `Proveedor` | `proveedores` | `hasMany(Compra)`. Soporte de creación al vuelo en abastecimiento. |
| `Cliente` | `clientes` | `hasMany(Venta)`. Soporte de creación al vuelo en venta POS. |
| `Compra` | `compras` | `belongsTo(Usuario)`, `belongsTo(Proveedor)`, `hasMany(DetalleCompra)`. |
| `DetalleCompra` | `detalle_compra` | Ítems de compras. Actualiza existencias vía `increment('stock_actual')`. |
| `Venta` | `ventas` | `belongsTo(Usuario)`, `belongsTo(Cliente)`, `hasMany(DetalleVenta)`. |
| `DetalleVenta` | `detalle_venta` | Ítems de ventas con `costo_unitario` y `precio_unitario`. Descuenta con `decrement`. |

---

## 🛡️ Matriz de Control de Acceso (Roles y Middleware)

| Módulo / Funcionalidad | Administrador (`role:administrador`) | Vendedor (`role:vendedor`) |
|---|---|---|
| **Gestión de Usuarios** | Acceso total (Crear, Editar, Activar/Desactivar, Eliminar) | ❌ Sin acceso (`403 Forbidden`) |
| **Categorías** | Acceso total (Crear, Editar, Eliminar) | ❌ Sin acceso |
| **Productos** | Acceso total (Crear, Editar, Activar/Desactivar, Eliminar) | 👁️ Solo lectura de productos activos |
| **Compras** | Consulta global y registro; anulación de cualquier compra | Registra compras y anula únicamente sus compras propias |
| **Ventas Multi-Producto** | Consulta global y registro; anulación de cualquier venta | Registra ventas y anula únicamente sus ventas propias |
| **Factura POS & PDF** | Emisión, impresión y descarga de cualquier factura | Solo emite, imprime y descarga sus propias facturas |
| **Ganancias** | Análisis financiero de toda la empresa y Top 5 general | Análisis de rendimiento y comisiones personales |
| **Dashboard** | Métricas consolidadas, valor de inventario y stock crítico | Resumen de actividad del punto de venta |
