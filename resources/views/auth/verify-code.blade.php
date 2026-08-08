<!DOCTYPE html>
<html lang="es" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código de Seguridad | Inventory System</title>

    {{-- Favicon Oficial Inventory System --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <link rel="alternate icon" href="{{ asset('favicon.svg') }}?v=2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
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
                <span class="text-[10px] text-slate-500 font-bold tracking-wider uppercase mt-0.5">Verificación de Identidad</span>
            </div>
        </div>

        <!-- Contenido Central -->
        <div class="relative z-10 space-y-6">
            <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center text-white text-2xl shadow-inner">
                <i class="bi bi-envelope-check-fill"></i>
            </div>
            <div class="space-y-3">
                <h2 class="text-3xl xl:text-4xl font-extrabold text-white leading-tight tracking-tight">
                    Revisa tu correo<br>
                    e ingresa tu código<br>
                    <span class="text-slate-400">de seguridad.</span>
                </h2>
                <p class="text-slate-400 text-sm leading-relaxed max-w-xs">
                    El código de 6 dígitos ha sido enviado y caducará en 15 minutos para proteger la integridad de tu cuenta.
                </p>
            </div>

            <!-- Pasos visuales -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center gap-3 text-slate-400 text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-bold"><i class="bi bi-check"></i></span>
                    <span class="line-through">Ingresar correo registrado</span>
                </div>
                <div class="flex items-center gap-3 text-white text-sm font-semibold">
                    <span class="w-6 h-6 rounded-full bg-white text-slate-900 flex items-center justify-center text-xs font-black">2</span>
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
            <a href="{{ route('password.code.email') }}" class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">
                <i class="bi bi-arrow-left text-xs"></i> Cambiar correo
            </a>
            <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">
                Cancelar
            </a>
        </div>

        <!-- Formulario Centrado -->
        <div class="flex-1 flex items-center justify-center px-6 sm:px-12 py-8">
            <div class="w-full max-w-sm">

                <!-- Indicador de Paso -->
                <div class="flex items-center gap-2 mb-6">
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-900 text-white">Paso 2 de 3</span>
                    <span class="text-xs font-semibold text-slate-500">Validación de código</span>
                </div>

                <!-- Encabezado -->
                <div class="mb-6">
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Ingresa el código</h1>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1.5 leading-relaxed">
                        Hemos enviado un código de 6 dígitos al correo:<br>
                        <strong class="text-slate-900 font-bold break-all">{{ $correo }}</strong>
                    </p>
                </div>

                <!-- Alerta: Estado / Código enviado -->
                @if (session('status'))
                    <div class="mb-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 shadow-2xs">
                        <i class="bi bi-check-circle-fill text-emerald-600 text-lg flex-shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-emerald-900 font-bold text-xs">Código generado</p>
                            <p class="text-emerald-700 text-xs mt-0.5">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif


                <!-- Alerta: Errores -->
                @if ($errors->any())
                    <div class="mb-5 flex items-start gap-3 bg-rose-50 border border-rose-200 rounded-2xl p-4 shadow-2xs">
                        <i class="bi bi-exclamation-triangle-fill text-rose-600 text-lg flex-shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-rose-900 font-bold text-xs">Código no válido</p>
                            @foreach ($errors->all() as $error)
                                <p class="text-rose-700 text-xs mt-0.5">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Formulario Paso 2 -->
                <form action="{{ route('password.code.check') }}" method="POST" class="space-y-5" autocomplete="off" id="verifyForm">
                    @csrf

                    <!-- Campo de Código de 6 Dígitos -->
                    <div class="space-y-2">
                        <label for="codigo" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider text-center">
                            Código de Verificación (6 dígitos)
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                name="codigo"
                                id="codigo"
                                required
                                autofocus
                                maxlength="6"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder="000000"
                                value="{{ old('codigo') }}"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 text-center text-3xl sm:text-4xl font-extrabold tracking-[0.4em] font-mono text-slate-900 placeholder:text-slate-300 placeholder:tracking-[0.4em] hover:border-slate-300 focus:outline-none focus:bg-white focus:border-slate-900 focus:ring-4 focus:ring-slate-900/5 transition-all
                                {{ $errors->has('codigo') ? 'border-rose-300 bg-rose-50' : '' }}"
                            >
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 px-1 pt-1">
                            <span><i class="bi bi-clock-history"></i> Válido por 15 min</span>
                            <span id="charCount">0 / 6 dígitos</span>
                        </div>
                    </div>

                    <!-- Botón Verificar -->
                    <div class="pt-1">
                        <button
                            type="submit"
                            id="submitBtn"
                            class="w-full bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white font-bold text-sm py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2.5 cursor-pointer"
                        >
                            <svg id="spinner" class="hidden w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <i class="bi bi-check2-circle text-base" id="btnIcon"></i>
                            <span id="btnText">Verificar Código y Continuar</span>
                        </button>
                    </div>
                </form>

                <!-- Reenviar Código -->
                <div class="pt-4 text-center border-t border-slate-100 mt-5">
                    <p class="text-xs text-slate-500 mb-2">¿No recibiste el correo o se venció el tiempo?</p>
                    <form action="{{ route('password.code.resend') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-slate-900 hover:text-slate-700 underline decoration-slate-300 underline-offset-4 hover:decoration-slate-900 cursor-pointer">
                            <i class="bi bi-arrow-clockwise"></i> Reenviar un nuevo código
                        </button>
                    </form>
                </div>

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
        const input = document.getElementById('codigo');
        const charCount = document.getElementById('charCount');

        input?.addEventListener('input', function(e) {
            // Solo permitir números
            this.value = this.value.replace(/[^0-9]/g, '');
            if (charCount) {
                charCount.textContent = `${this.value.length} / 6 dígitos`;
            }
            if (this.value.length === 6) {
                // Auto focus al botón o submit opcional
            }
        });

        document.getElementById('verifyForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');

            if (btn && spinner && icon && text) {
                btn.disabled = true;
                btn.classList.add('opacity-80', 'cursor-not-allowed');
                spinner.classList.remove('hidden');
                icon.classList.add('hidden');
                text.textContent = 'Verificando código...';
            }
        });
    </script>

</body>
</html>
