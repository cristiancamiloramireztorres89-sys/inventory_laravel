# Lógica QA: Editar Producto

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Se verifica si el producto ya tenía una imagen previa en `public/uploads/productos/` y se elimina físicamente con `@unlink` antes de guardar la nueva imagen.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `PUT /admin/productos/{producto}` (Nombre: `admin.productos.update`)
- **Directiva Blade:** `@method('PUT')` y `@csrf`
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\ProductoController@update`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador abre el modal de edición de un producto existente.
2. Validación de campos:
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
   ]);
   ```
3. Lógica de Reemplazo de Imagen:
   ```php
   if ($request->hasFile('imagen')) {
       $destino = public_path('uploads/productos');

       // Borrar imagen anterior de disco si existía
       if ($producto->imagen && file_exists($destino . '/' . $producto->imagen)) {
           @unlink($destino . '/' . $producto->imagen);
       }

       $file = $request->file('imagen');
       $nombreImagen = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
       $file->move($destino, $nombreImagen);
       $data['imagen'] = $nombreImagen;
   }
   ```
4. Se ejecuta `$producto->update($data)`.
5. Redirección con mensaje: *"Producto actualizado correctamente."*.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Acción Realizada | Resultado Esperado |
|---|---|---|---|
| **TC-EDITP-01** | Editar datos sin cambiar foto | Modificar precio y nombre, sin seleccionar nuevo archivo | Datos actualizados, la foto anterior se mantiene intacta. |
| **TC-EDITP-02** | Reemplazo de foto | Subir una nueva foto válida | La foto anterior se elimina físicamente de `uploads/productos/` y la nueva foto queda asociada. |
| **TC-EDITP-03** | Stock negativo | `stock_actual`: `-5` | Rechazo por validación `min:0`. |
