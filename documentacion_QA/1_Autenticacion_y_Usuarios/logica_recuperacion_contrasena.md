# Lógica QA: Recuperación de Contraseña por Código OTP

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Se implementó un flujo completo y seguro de recuperación mediante **Código OTP de 6 dígitos numéricos enviado por correo electrónico**, con expiración temporal de 15 minutos y verificación mediante `Hash::check`.

---

## 🛣️ Rutas y Controlador
- **Controlador:** `App\Http\Controllers\Auth\PasswordCodeController`
- **Rutas (agrupadas en middleware `guest`):**
  1. `GET  /recuperar-contrasena` (`password.code.email`) -> Muestra formulario para solicitar correo.
  2. `POST /recuperar-contrasena/enviar-codigo` (`password.code.send`) -> Genera código y envía correo.
  3. `GET  /recuperar-contrasena/verificar-codigo` (`password.code.verify`) -> Pantalla para ingresar el código.
  4. `POST /recuperar-contrasena/verificar-codigo` (`password.code.check`) -> Valida el código de 6 dígitos.
  5. `POST /recuperar-contrasena/reenviar-codigo` (`password.code.resend`) -> Reenvía un nuevo código OTP.
  6. `GET  /recuperar-contrasena/nueva-clave` (`password.code.reset`) -> Formulario de nueva contraseña.
  7. `POST /recuperar-contrasena/guardar-clave` (`password.code.update`) -> Guarda la nueva clave cifrada.

---

## 🔄 Flujo de Ejecución Paso a Paso

### Paso 1: Solicitud de Código (`sendCode`)
1. El usuario ingresa su correo electrónico registrado.
2. Se valida que el correo sea válido y exista en la tabla `usuarios`.
3. Si el usuario está desactivado (`activo = 0`), el proceso se interrumpe indicando que debe contactar al administrador.
4. Se genera un código aleatorio de 6 dígitos: `$codigo = (string) random_int(100000, 999999)`.
5. Se almacena en la tabla `password_reset_tokens`:
   ```php
   DB::table('password_reset_tokens')->updateOrInsert(
       ['email' => $correo],
       ['token' => Hash::make($codigo), 'created_at' => now()]
   );
   ```
6. Se envía el correo electrónico usando `Mail::to($correo)->send(new CodigoRecuperacionMail($codigo, $nombre))`.
7. Se guarda en sesión temporal `session(['recovery_email' => $correo, 'recovery_verified' => false])`.
8. Redirige a la pantalla de verificación (`password.code.verify`).

### Paso 2: Validación del Código OTP (`verifyCode`)
1. El usuario ingresa los 6 dígitos recibidos en su bandeja de entrada.
2. Validación: `codigo: ['required', 'string', 'min:6', 'max:6']`.
3. Se consulta el registro en `password_reset_tokens` para el correo en sesión.
4. **Verificación de Expiración:** Se valida que `created_at` no supere los **15 minutos**:
   ```php
   if (Carbon::parse($registro->created_at)->addMinutes(15)->isPast()) { ... }
   ```
5. **Verificación de Hash:** Se comprueba el código ingresado con `Hash::check($codigoIngresado, $registro->token)`.
6. Si es correcto, se actualiza la sesión: `session(['recovery_verified' => true])` y redirige a definir nueva clave.

### Paso 3: Cambio de Contraseña (`updatePassword`)
1. Se valida que la sesión tenga `recovery_email` y `recovery_verified == true` (evita saltarse pasos directamente por URL).
2. Se valida la nueva contraseña:
   ```php
   'contrasena' => ['required', 'string', 'min:6', 'confirmed']
   ```
3. Se actualiza la contraseña del usuario con **Bcrypt**:
   ```php
   $usuario->contrasena = Hash::make($request->input('contrasena'));
   $usuario->save();
   ```
4. Se elimina el registro de `password_reset_tokens` y se limpian las variables de sesión.
5. Redirige a `/login` con mensaje de éxito.

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Datos de Entrada | Resultado Esperado |
|---|---|---|---|
| **TC-REC-01** | Flujo completo exitoso | Correo válido -> Código de 6 dígitos correcto -> Clave >= 6 chars coincidente | Contraseña actualizada con éxito y redirección a login lista para ingresar. |
| **TC-REC-02** | Correo no registrado | `correo`: `inexistente@correo.com` | Error: *"El correo electrónico ingresado no se encuentra registrado..."*. |
| **TC-REC-03** | Código incorrecto | Código erróneo (ej. `111111`) | Error: *"El código de verificación ingresado es incorrecto..."*. |
| **TC-REC-04** | Código expirado (>15 minutos) | Código ingresado pasados 15 minutos | Error: *"El código de seguridad ha expirado... Solicita uno nuevo."*. |
| **TC-REC-05** | Confirmación de clave dispar | `contrasena`: `123456`, `contrasena_confirmation`: `654321` | Error: *"La confirmación de la contraseña no coincide."*. |
| **TC-REC-06** | Acceso forzado a Paso 3 sin verificar | Navegar directo a `/recuperar-contrasena/nueva-clave` | Redirige al Paso 1 con error: *"Debes verificar tu código de seguridad..."*. |
