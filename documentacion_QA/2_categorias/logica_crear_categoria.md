# Lógica QA: Crear Categoría

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Validación nativa con regla `unique:categorias,nombre`, protección CSRF y redirección limpia con mensajes de error en español.

---

## 🛣️ Ruta y Controlador
- **Ruta:** `POST /admin/categorias` (Nombre: `admin.categorias.store`)
- **Middleware:** `auth`, `role:administrador`
- **Controlador:** `App\Http\Controllers\Admin\CategoriaController@store`
- **Vista Blade:** `resources/views/admin/categorias.blade.php`

---

## 🔄 Flujo de Ejecución y Validaciones

1. El Administrador llena el modal de creación de categoría.
2. Envío por POST con `@csrf`:
   - `nombre`: string obligatorio, máx 100 caracteres.
   - `descripcion`: string opcional, máx 255 caracteres.
3. Reglas de Validación:
   ```php
   $validated = $request->validate([
       'nombre'      => ['required', 'string', 'max:100', 'unique:categorias,nombre'],
       'descripcion' => ['nullable', 'string', 'max:255'],
   ], [
       'nombre.required' => 'El nombre de la categoría es obligatorio.',
       'nombre.unique'   => 'Ya existe una categoría con este nombre.',
   ]);
   ```
4. Inserción con Eloquent:
   ```php
   Categoria::create($validated);
   ```
5. Redirige a `admin.categorias` con alerta: *"Categoría creada correctamente."*.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Datos de Entrada | Resultado Esperado |
|---|---|---|---|
| **TC-CAT-01** | Categoría válida | `nombre`: "Laptops Gamer", `descripcion`: "Equipos de alto rendimiento" | Registro insertado en la tabla `categorias`. Alerta verde en pantalla. |
| **TC-CAT-02** | Nombre duplicado | `nombre`: categoría ya existente | Error de validación: *"Ya existe una categoría con este nombre."*. |
| **TC-CAT-03** | Nombre vacío | `nombre`: `""` | Error de validación: *"El nombre de la categoría es obligatorio."*. |
| **TC-CAT-04** | Nombre excede 100 caracteres | Cadena > 100 caracteres | Error de validación por longitud máxima. |
