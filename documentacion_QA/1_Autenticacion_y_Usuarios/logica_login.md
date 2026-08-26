# Lógica QA: Iniciar Sesión (Login)

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Se utiliza la clase `LoginRequest` con autenticación mediante **Bcrypt (`Hash::check`)**, limitación de tasa (**Rate Limiting** de 5 intentos máx. por IP/correo) y regeneración de ID de sesión para prevenir fijación de sesiones (*Session Fixation*).

---

## 🛣️ Ruta y Controladores
- **Ruta:** `POST /login` (Nombre: `login.post`)
- **Controlador:** `App\Http\Controllers\Auth\AuthenticatedSessionController@store`
- **Form Request:** `App\Http\Requests\Auth\LoginRequest`
- **Vista Blade:** `resources/views/auth/login.blade.php`

---

## 🔄 Flujo de Ejecución Paso a Paso

1. El usuario envía el formulario de login mediante `POST /login` con token `@csrf`.
2. Laravel ejecuta las validaciones de `LoginRequest`:
   - `correo`: `['required', 'string', 'email']`
   - `contrasena`: `['required', 'string']`
3. Se evalúa el **Rate Limiter** (`ensureIsNotRateLimited`):
   - Clave única de limitación: `Str::lower($correo) . '|' . $request->ip()`.
   - Si supera 5 intentos fallidos, se dispara el evento `Lockout` y se bloquea la solicitud con error `422 Unprocessable Entity`: *"Demasiados intentos de acceso. Por favor intenta de nuevo en X segundos."*
4. Se ejecuta el intento de autenticación:
   ```php
   Auth::attempt(['correo' => $correo, 'password' => $contrasena], $request->boolean('remember'))
   ```
   - Laravel busca al usuario en la tabla `usuarios` por el campo `correo`.
   - Verifica la contraseña mediante `Hash::check($contrasena, $usuario->contrasena)`.
   - Si no coincide, incrementa el contador de Rate Limiting y lanza error: *"El correo electrónico o la contraseña no coinciden con nuestros registros."*
5. Se valida el estado del usuario autenticado:
   ```php
   if (!$user->estaActivo()) {
       Auth::logout();
       throw ValidationException::withMessages(['correo' => 'Esta cuenta de usuario ha sido desactivada...']);
   }
   ```
6. Si pasa las validaciones:
   - Se limpia el contador del Rate Limiter (`RateLimiter::clear`).
   - Se regenera la sesión: `$request->session()->regenerate()`.
   - Se redirige según el rol:
     - `administrador`: `redirect()->intended(route('admin.dashboard'))->with('success', 'Bienvenido al panel de Administrador')`
     - `vendedor`: `redirect()->intended(route('vendedor.dashboard'))->with('success', 'Bienvenido al punto de venta')`

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Datos de Entrada | Resultado Esperado |
|---|---|---|---|
| **TC-AUTH-01** | Login exitoso como Administrador | `correo`: admin existente, `contrasena`: correcta | Redirección a `/admin/dashboard` con mensaje de bienvenida y sesión activa. |
| **TC-AUTH-02** | Login exitoso como Vendedor | `correo`: vendedor existente, `contrasena`: correcta | Redirección a `/vendedor/dashboard` con mensaje de bienvenida. |
| **TC-AUTH-03** | Contraseña incorrecta | `correo`: registrado, `contrasena`: incorrecta | Permanece en login, error en pantalla: *"El correo electrónico o la contraseña no coinciden..."*. |
| **TC-AUTH-04** | Usuario inexistente | `correo`: no registrado, `contrasena`: cualquiera | Mensaje genérico de credenciales incorrectas (no revela si el correo existe). |
| **TC-AUTH-05** | Usuario desactivado (`activo = 0`) | `correo`: usuario con `activo = 0`, `contrasena`: correcta | Sesión abortada inmediatamente y error: *"Esta cuenta de usuario ha sido desactivada..."*. |
| **TC-AUTH-06** | Formato de correo inválido | `correo`: `correo-invalido`, `contrasena`: `123456` | Error de validación: *"Por favor ingresa un correo electrónico válido."*. |
| **TC-AUTH-07** | Rate Limiting (Fuerza Bruta) | 5 intentos fallidos consecutivos | En el 6to intento la petición se bloquea con contador de tiempo regresivo. |
| **TC-AUTH-08** | Petición sin token CSRF | Envío directo vía cURL o Postman sin `@csrf` | Respuesta HTTP `419 Page Expired`. |
