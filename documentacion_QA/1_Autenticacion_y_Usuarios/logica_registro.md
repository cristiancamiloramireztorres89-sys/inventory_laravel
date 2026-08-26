# Lógica QA: Registro Público de Usuarios (Estado en el Sistema)

## 📌 Estado de la Funcionalidad y Especificación Técnica
- **Comportamiento Actual del Sistema:**
  El sistema de inventarios **no cuenta con auto-registro público de usuarios**. Por razones de seguridad empresarial y control de inventario, el acceso es privado y restringido.
- **Flujo Oficial Vigente:**
  La creación de cuentas de usuario se realiza de forma centralizada por el **Administrador** desde el panel administrativo:
  - Ruta: POST /admin/usuarios (dmin.usuarios.store)
  - Controlador: App\\Http\\Controllers\\Admin\\UsuarioController@store
  - Ver documentación completa en: [logica_crear_usuario.md](./logica_crear_usuario.md).

---

## 🛠️ Especificaciones para Implementación Futura (si se requiere)
En caso de requerirse auto-registro público en Laravel:
1. **Ruta:** GET|POST /register protegida por middleware guest.
2. **Controlador:** Auth\\RegisteredUserController o Auth\\RegisterController.
3. **Validación:**
   - 
ombre: ['required', 'string', 'max:100']
   - correo: ['required', 'string', 'email', 'max:100', 'unique:usuarios,correo']
   - contrasena: ['required', 'string', 'min:6', 'confirmed']
4. **Asignación de Rol por Defecto:** Asignar automáticamente el id_rol correspondiente a endedor (nunca asignar dministrador por auto-registro).
5. **Cifrado:** Guardar con Hash::make(->input('contrasena')).
6. **Autenticación Inmediata:** Disparar evento Registered y llamar a Auth::login().
