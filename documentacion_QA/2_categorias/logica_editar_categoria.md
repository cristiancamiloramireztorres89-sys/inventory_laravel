# Lógica QA: Editar Categoría

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Se usa la sintaxis extendida de validación de Laravel `unique:categorias,nombre,{$id},id_categoria` para permitir guardar sin alterar el nombre o modificarlo sin conflictos.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `PUT /admin/categorias/{categoria}` (Nombre: `admin.categorias.update`)
- **Directiva Blade:** `@method('PUT')` y `@csrf`
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\CategoriaController@update`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador edita el nombre o descripción en el modal.
2. Validación en el Controlador:
   ```php
   $validated = $request->validate([
       'nombre'      => ['required', 'string', 'max:100', 'unique:categorias,nombre,' . $categoria->id_categoria . ',id_categoria'],
       'descripcion' => ['nullable', 'string', 'max:255'],
   ], [
       'nombre.required' => 'El nombre de la categoría es obligatorio.',
       'nombre.unique'   => 'Ya existe otra categoría con este nombre.',
   ]);
   ```
3. Actualización:
   ```php
   $categoria->update($validated);
   ```
4. Redirección con mensaje: *"Categoría actualizada correctamente."*.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Datos de Entrada | Resultado Esperado |
|---|---|---|---|
| **TC-EDITCAT-01** | Modificar descripción | Mantener mismo nombre, cambiar texto de descripción | Se actualiza correctamente sin conflicto de unicidad. |
| **TC-EDITCAT-02** | Cambiar a nombre ya usado por otra categoría | Nombre de otra categoría existente | Bloqueo con error: *"Ya existe otra categoría con este nombre."*. |
