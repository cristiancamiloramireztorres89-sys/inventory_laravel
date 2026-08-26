# Lógica QA: Gestión de Fotos y Multimedia de Productos

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel:
  - Validación de seguridad MIME estricta: `image|mimes:jpeg,png,jpg,webp,gif|max:2048`.
  - Carpeta de destino estándar: `public/uploads/productos/`.
  - Nombres únicos que evitan colisiones de caché: `time() . '_' . uniqid() . '.' . $extension`.
  - Ciclo de vida completo: Si se cambia la foto o se elimina el producto, el archivo viejo en disco se destruye con `@unlink`.

---

## 🛣️ Archivos y Ubicaciones
- **Directorio de Almacenamiento:** `public/uploads/productos/`
- **Controlador:** `App\Http\Controllers\Admin\ProductoController` (métodos `store`, `update`, `destroy`)
- **Acceso en Vistas Blade:** `asset('uploads/productos/' . $producto->imagen)` con imagen por defecto si es nulo.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Archivo de Prueba | Resultado Esperado |
|---|---|---|---|
| **TC-FOTO-01** | Subida de formato válido | Archivo `.webp` de 500KB | Subida exitosa, se genera nombre único y la imagen se muestra en el catálogo. |
| **TC-FOTO-02** | Subida de archivo malicioso o ejecutable | Archivo `script.php` renombrado o `.exe` | Rechazado por validación `image|mimes`. No se guarda ningún archivo en el servidor. |
| **TC-FOTO-03** | Eliminación física al actualizar | Producto con imagen `A.jpg`, se le sube `B.png` | El archivo `A.jpg` desaparece de `public/uploads/productos/` y queda únicamente `B.png`. |
| **TC-FOTO-04** | Eliminación física al borrar producto | Eliminar producto que tenía imagen | El archivo de imagen asociado se borra de disco automáticamente. |
