<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Inventory System'))</title>

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
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex">

    {{-- Sidebar --}}
    @include('componentes.sidebar')

    {{-- Área principal --}}
    <div class="flex-1 flex flex-col min-h-screen min-w-0">

        {{-- Header top --}}
        <header class="bg-white border-b border-slate-200 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30 shadow-xs">
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
                <div id="flash-success-alert-app" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center gap-2.5 transition-all duration-500 ease-out shadow-xs">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-base flex-shrink-0"></i>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div id="flash-error-alert-app" class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium flex items-center gap-2.5 transition-all duration-500 ease-out shadow-xs">
                    <i class="bi bi-exclamation-circle-fill text-red-600 text-base flex-shrink-0"></i>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')

        </main>

        {{-- Footer --}}
        @include('componentes.footer')

    </div>

    {{-- Script de auto-desaparición tras 3 segundos --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const flashSuccess = document.getElementById('flash-success-alert-app');
            if (flashSuccess) {
                setTimeout(() => {
                    flashSuccess.style.opacity = '0';
                    flashSuccess.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        flashSuccess.style.display = 'none';
                    }, 500);
                }, 3000);
            }

            const flashError = document.getElementById('flash-error-alert-app');
            if (flashError) {
                setTimeout(() => {
                    flashError.style.opacity = '0';
                    flashError.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        flashError.style.display = 'none';
                    }, 500);
                }, 3000);
            }
        });
    </script>

</body>
</html>
