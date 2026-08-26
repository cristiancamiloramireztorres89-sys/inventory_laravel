# Lógica QA: Cerrar Sesión (Logout)

## 📌 Especificación Técnica del Módulo en Laravel
### Implementación en Laravel: Se utiliza el guard de autenticación web de Laravel, invalidando la sesión de almacenamiento y regenerando el token CSRF para evitar vulnerabilidades de reutilización de sesión.

---

## 🛣️ Ruta y Controladores
- **Ruta:** `POST /logout` (Nombre: `logout`)
- **Middleware:** `auth` (Solo usuarios con sesión activa)
- **Controlador:** `App\Http\Controllers\Auth\AuthenticatedSessionController@destroy`

---

## 🔄 Flujo de Ejecución Paso a Paso

1. El usuario hace clic en "Cerrar sesión" en el menú o barra lateral.
2. La vista envía un formulario mediante método `POST` con directiva `@csrf`:
   ```blade
   <form method="POST" action="{{ route('logout') }}">
       @csrf
       <button type="submit">Cerrar sesión</button>
   </form>
   ```
3. El controlador ejecuta el protocolo seguro de cierre de sesión:
   ```php
   Auth::guard('web')->logout();
   $request->session()->invalidate();
   $request->session()->regenerateToken();
   ```
4. Se destruyen las variables de sesión del usuario en el servidor.
5. Se redirige a la página principal (`/`) con mensaje flash:
   ```php
   return redirect('/')->with('status', 'Has cerrado sesión correctamente.');
   ```

---

## 🧪 Casos de Prueba QA (Test Cases)

| ID | Escenario de Prueba | Acción / Precondición | Resultado Esperado |
|---|---|---|---|
| **TC-LOGOUT-01** | Logout exitoso | Usuario logueado presiona "Cerrar Sesión" | Redirige a `/`, sesión cerrada. Si intenta volver atrás con el navegador, no debe poder acceder a rutas protegidas. |
| **TC-LOGOUT-02** | Intento de logout sin sesión | Usuario no autenticado envía `POST /logout` | Redirección automática a `/login` por middleware `auth`. |
| **TC-LOGOUT-03** | Intento de logout por GET | Ingresar `http://localhost/logout` directamente | Error HTTP `405 Method Not Allowed` (la ruta solo acepta POST). |
