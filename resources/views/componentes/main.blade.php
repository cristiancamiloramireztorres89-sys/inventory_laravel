<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inventory System')</title>

    {{-- Favicon Oficial Inventory System --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <link rel="alternate icon" href="{{ asset('favicon.svg') }}?v=2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased h-screen flex overflow-hidden">

    {{-- Sidebar Fijo --}}
    @include('componentes.sidebar')

    {{-- Área principal con scroll independiente --}}
    <div class="flex-1 flex flex-col h-screen min-w-0 overflow-y-auto">

        {{-- Header top --}}
        <header class="bg-white border-b border-slate-200 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30 flex-shrink-0 shadow-xs">
            <div>
                <h2 class="text-base font-black text-slate-900 tracking-tight leading-tight">@yield('page_title', 'Dashboard')</h2>
                <p class="text-xs font-semibold text-slate-600 mt-0.5">@yield('page_subtitle', 'Panel de control')</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200/80 text-xs font-bold text-slate-700 shadow-2xs">
                <i class="bi bi-calendar3 text-slate-600 text-xs"></i>
                <span class="capitalize">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM YYYY') }}</span>
            </div>
        </header>

        {{-- Contenido --}}
        <main class="flex-1 p-6 sm:p-8">

            @if (session('success'))
                <div id="flash-success-alert" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2.5 transition-all duration-500 ease-out shadow-xs">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-base flex-shrink-0"></i>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div id="flash-error-alert" class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium flex items-center gap-2.5 transition-all duration-500 ease-out shadow-xs">
                    <i class="bi bi-exclamation-circle-fill text-red-600 text-base flex-shrink-0"></i>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div id="flash-validation-errors" class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium space-y-1 transition-all duration-500 ease-out shadow-xs">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
                        <span>Por favor corrige los siguientes errores:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-slate-700 pl-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

        </main>

        {{-- Footer --}}
        @include('componentes.footer')

    </div>

    {{-- Script de auto-desaparición tras 3 segundos para todas las alertas --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dismissElement = (elementId, delayMs = 3000) => {
                const el = document.getElementById(elementId);
                if (el) {
                    setTimeout(() => {
                        el.style.transition = 'all 0.5s ease-out';
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            el.style.display = 'none';
                        }, 500);
                    }, delayMs);
                }
            };

            // Auto-ocultar a los 3 segundos
            dismissElement('flash-success-alert', 3000);
            dismissElement('flash-error-alert', 3000);
            dismissElement('flash-validation-errors', 3500);
        });

        function abrirModalLogout() {
            const m = document.getElementById('modalConfirmarLogout');
            if (m) m.classList.remove('hidden');
        }
        function cerrarModalLogout() {
            const m = document.getElementById('modalConfirmarLogout');
            if (m) m.classList.add('hidden');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') cerrarModalLogout();
        });
    </script>

    {{-- Modal de Confirmación para Cerrar Sesión --}}
    <div id="modalConfirmarLogout" onclick="if(event.target === this) cerrarModalLogout()"
         class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-sm w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150 text-center">
            
            <div class="flex items-center justify-center text-3xl mx-auto text-rose-500 mb-1">
                <i class="bi bi-box-arrow-right"></i>
            </div>

            <div class="space-y-1.5">
                <h3 class="font-bold text-slate-900 text-base">¿Cerrar Sesión?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    ¿Estás seguro de que deseas salir del sistema? Deberás ingresar tus credenciales nuevamente para acceder.
                </p>
            </div>

            <form id="formGlobalLogout" action="{{ route('logout') }}" method="POST" class="pt-2 flex items-center gap-2">
                @csrf
                <button type="button" onclick="cerrarModalLogout()"
                        class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold cursor-pointer transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                        class="w-1/2 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm cursor-pointer transition-colors">
                    Sí, Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

</body>
</html>
