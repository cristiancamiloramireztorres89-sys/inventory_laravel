# Lógica QA: Crear Producto

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Validación de archivo con `image|mimes:jpeg,png,jpg,webp,gif|max:2048`, almacenamiento estructurado en `public/uploads/productos/`, nombrado criptográfico único (`time() . '_' . uniqid()`), y asignación por defecto de `activo = true`.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `POST /admin/productos` (Nombre: `admin.productos.store`)
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\ProductoController@store`
- **Vista Blade:** `resources/views/admin/productos.blade.php` (formulario con `enctype="multipart/form-data"` y `@csrf`)

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador llena el formulario modal de producto.
2. Validación en el Controlador:
   ```php
   $validated = $request->validate([
       'nombre'        => ['required', 'string', 'max:100'],
       'id_categoria'  => ['required', 'integer', 'exists:categorias,id_categoria'],
       'marca'         => ['nullable', 'string', 'max:100'],
       'stock_actual'  => ['required', 'integer', 'min:0'],
       'stock_minimo'  => ['required', 'integer', 'min:0'],
       'precio_venta'  => ['required', 'numeric', 'min:0'],
       'descripcion'   => ['nullable', 'string', 'max:500'],
       'imagen'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
   ], [
       'nombre.required'       => 'El nombre del producto es obligatorio.',
       'id_categoria.required' => 'Debes seleccionar una categoría.',
       'stock_actual.required' => 'El stock actual es obligatorio.',
       'stock_minimo.required' => 'El stock mínimo es obligatorio.',
       'precio_venta.required' => 'El precio de venta es obligatorio.',
       'imagen.image'          => 'El archivo seleccionado debe ser una imagen válida.',
       'imagen.mimes'          => 'Formatos de imagen permitidos: JPG, JPEG, PNG, WEBP, GIF.',
       'imagen.max'            => 'La imagen no debe superar los 2MB.',
   ]);
   ```
3. Lógica de Imagen:
   - Si se adjunta archivo, se asegura que exista la carpeta `public/uploads/productos`.
   - Se renombra a `time() . '_' . uniqid() . '.' . $extension`.
   - Se mueve físicamente a `public/uploads/productos/`.
4. Inserción con Eloquent:
   ```php
   Producto::create([
       'nombre'        => $validated['nombre'],
       'id_categoria'  => $validated['id_categoria'],
       'marca'         => $validated['marca'],
       'stock_actual'  => $validated['stock_actual'],
       'stock_minimo'  => $validated['stock_minimo'],
       'precio_venta'  => $validated['precio_venta'],
       'descripcion'   => $validated['descripcion'],
       'imagen'        => $nombreImagen,
       'activo'        => true,
   ]);
   ```
5. Redirección con flash: *"Producto registrado exitosamente en el catálogo."*.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Datos de Entrada | Resultado Esperado |
|---|---|---|---|
| **TC-PROD-01** | Creación completa con imagen | Campos obligatorios válidos + imagen PNG de 1MB | Registro creado en tabla `productos`, archivo guardado en `uploads/productos/`, badge activo visible. |
| **TC-PROD-02** | Creación sin imagen (opcional) | Campos obligatorios válidos, sin subir imagen | Registro creado con `imagen = null`, muestra placeholder por defecto en la vista. |
| **TC-PROD-03** | Categoría inexistente | `id_categoria`: `9999` | Error de validación: categoría no existe en base de datos. |
| **TC-PROD-04** | Precio de venta negativo | `precio_venta`: `-10` | Error de validación: *"El precio de venta debe ser mínimo 0."*. |
| **TC-PROD-05** | Imagen supera 2MB | Archivo de imagen de 5MB | Error de validación: *"La imagen no debe superar los 2MB."*. |
| **TC-PROD-06** | Archivo no soportado (ej. PDF o EXE) | Archivo `.pdf` | Error de validación: *"Formatos de imagen permitidos: JPG, JPEG, PNG, WEBP, GIF."*. |
