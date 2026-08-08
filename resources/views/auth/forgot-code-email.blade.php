<!DOCTYPE html>
<html lang="es" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña (Paso 1 de 3) | Inventory System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="bg-white text-slate-800 font-sans antialiased selection:bg-slate-900 selection:text-white min-h-screen flex">

    {{-- ── Panel Izquierdo Corporativo ── --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-[52%] bg-slate-900 flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute -top-40 -left-40 w-[480px] h-[480px] rounded-full bg-white opacity-[0.03] pointer-events-none"></div>
        <div class="absolute bottom-[-120px] right-[-80px] w-96 h-96 rounded-full bg-white opacity-[0.04] pointer-events-none"></div>

        <!-- Logo -->
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center shadow-sm">
                <i class="bi bi-box-seam-fill text-white text-lg"></i>
            </div>
            <div class="flex flex-col leading-none">
                <span class="text-white font-extrabold text-lg tracking-tight">Inventory <span class="text-slate-400">System</span></span>
                <span class="text-[10px] text-slate-500 font-bold tracking-wider uppercase mt-0.5">Seguridad y Verificación</span>
            </div>
        </div>

        <!-- Contenido Central -->
        <div class="relative z-10 space-y-6">
            <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center text-white text-2xl shadow-inner">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div class="space-y-3">
                <h2 class="text-3xl xl:text-4xl font-extrabold text-white leading-tight tracking-tight">
                    Recuperación segura<br>
                    por código de<br>
                    <span class="text-slate-400">verificación OTP.</span>
                </h2>
                <p class="text-slate-400 text-sm leading-relaxed max-w-xs">
                    Te enviaremos un código numérico temporal de 6 dígitos a tu correo electrónico para garantizar que eres el propietario de la cuenta.
                </p>
            </div>

            <!-- Pasos visuales -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center gap-3 text-white text-sm font-semibold">
                    <span class="w-6 h-6 rounded-full bg-white text-slate-900 flex items-center justify-center text-xs font-black">1</span>
                    <span>Ingresar correo registrado</span>
                </div>
                <div class="flex items-center gap-3 text-slate-400 text-sm">
                    <span class="w-6 h-6 rounded-full bg-white/10 text-slate-400 flex items-center justify-center text-xs font-bold">2</span>
                    <span>Verificar código de 6 dígitos</span>
                </div>
                <div class="flex items-center gap-3 text-slate-400 text-sm">
                    <span class="w-6 h-6 rounded-full bg-white/10 text-slate-400 flex items-center justify-center text-xs font-bold">3</span>
                    <span>Establecer nueva contraseña</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="relative z-10">
            <p class="text-slate-500 text-xs">&copy; {{ date('Y') }} Inventory System. Todos los derechos reservados.</p>
        </div>
    </div>

    {{-- ── Panel Derecho: Formulario ── --}}
    <div class="w-full lg:w-1/2 xl:w-[48%] flex flex-col justify-between bg-white">

        <!-- Top Bar -->
        <div class="flex items-center justify-between px-6 sm:px-10 py-5 border-b border-slate-100 lg:border-none">
            <a href="{{ route('login') }}" class="flex items-center gap-2.5 lg:hidden">
                <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <span class="text-slate-900 font-bold text-base">Inventory <span class="text-slate-500">System</span></span>
            </a>
            <a href="{{ route('login') }}" class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors ml-auto">
                <i class="bi bi-arrow-left text-xs"></i> Volver a Iniciar Sesión
            </a>
        </div>

        <!-- Formulario Centrado -->
        <div class="flex-1 flex items-center justify-center px-6 sm:px-12 py-8">
            <div class="w-full max-w-sm">

                <!-- Indicador de Paso -->
                <div class="flex items-center gap-2 mb-6">
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-900 text-white">Paso 1 de 3</span>
                    <span class="text-xs font-semibold text-slate-500">Ingreso de correo</span>
                </div>

                <!-- Encabezado -->
                <div class="mb-7">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">¿Olvidaste tu contraseña?</h1>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1.5">Ingresa tu correo electrónico registrado. Te enviaremos un código de seguridad para continuar.</p>
                </div>

                <!-- Alerta: Errores -->
                @if ($errors->any())
                    <div class="mb-6 flex items-start gap-3 bg-rose-50 border border-rose-200 rounded-2xl p-4 shadow-2xs">
                        <i class="bi bi-exclamation-triangle-fill text-rose-600 text-lg flex-shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-rose-900 font-bold text-xs">No fue posible continuar</p>
                            @foreach ($errors->all() as $error)
                                <p class="text-rose-700 text-xs mt-0.5">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Formulario Paso 1 -->
                <form action="{{ route('password.code.send') }}" method="POST" class="space-y-4" autocomplete="off" id="sendCodeForm">
                    @csrf

                    <!-- Correo Electrónico -->
                    <div class="space-y-1.5">
                        <label for="correo" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                            Correo Electrónico Registrado
                        </label>
                        <div class="relative flex items-center">
                            <i class="bi bi-envelope absolute left-4 text-slate-400 pointer-events-none text-base flex-shrink-0"></i>
                            <input
                                type="email"
                                name="correo"
                                id="correo"
                                value="{{ old('correo') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="tu@correo.com"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 hover:border-slate-300 focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all
                                {{ $errors->has('correo') ? 'border-rose-300 bg-rose-50' : '' }}"
                            >
                        </div>
                        @error('correo')
                            <p class="text-rose-600 text-xs flex items-center gap-1.5 font-medium mt-1">
                                <i class="bi bi-exclamation-circle-fill text-[10px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Botón Enviar Código -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            id="submitBtn"
                            class="w-full bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white font-bold text-sm py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2.5 cursor-pointer"
                        >
                            <svg id="spinner" class="hidden w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <i class="bi bi-send-fill text-sm" id="btnIcon"></i>
                            <span id="btnText">Enviar Código de Seguridad</span>
                        </button>
                    </div>

                    <div class="pt-3 text-center">
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">
                            ¿Recordaste tu contraseña? <span class="text-slate-900 font-bold underline decoration-slate-300 underline-offset-4 hover:decoration-slate-900">Iniciar Sesión</span>
                        </a>
                    </div>
                </form>

            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 sm:px-10 py-4 border-t border-slate-100">
            <p class="text-center text-[11px] font-medium text-slate-400">
                &copy; {{ date('Y') }} Inventory System. Todos los derechos reservados.
            </p>
        </div>

    </div>

    <script>
        document.getElementById('sendCodeForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');

            if (btn && spinner && icon && text) {
                btn.disabled = true;
                btn.classList.add('opacity-80', 'cursor-not-allowed');
                spinner.classList.remove('hidden');
                icon.classList.add('hidden');
                text.textContent = 'Enviando código...';
            }
        });
    </script>

</body>
</html>
