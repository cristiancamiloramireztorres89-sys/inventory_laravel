<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desarrollador | Cristian Camilo Ramírez Torres - Inventory System</title>

    {{-- Favicon Oficial Inventory System --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <link rel="alternate icon" href="{{ asset('favicon.svg') }}?v=2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif']
                    }
                }
            }
        }
    </script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-slate-50/70 text-slate-800 font-sans antialiased selection:bg-slate-900 selection:text-white min-h-screen flex flex-col justify-between">

    <!-- Header Navigation Minimalista -->
    <header class="sticky top-0 inset-x-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-[64px] flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm shadow-xs group-hover:scale-105 transition-transform">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <span class="text-sm font-bold text-slate-900 tracking-tight">Inventory <span class="text-slate-500">System</span></span>
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-semibold text-slate-700 transition-colors">
                    <i class="bi bi-arrow-left"></i>
                    <span>Volver al Inicio</span>
                </a>

                @auth
                    <a href="{{ auth()->user()->esAdmin() ? route('admin.dashboard') : route('vendedor.dashboard') }}" class="hidden xs:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition-colors">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden xs:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition-colors">
                        <i class="bi bi-box-arrow-in-right"></i> Acceder
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content: Tarjeta Minimalista de Perfil -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-8">
        <div class="w-full max-w-[410px] bg-white rounded-[32px] border border-slate-200/90 shadow-xl p-7 sm:p-9 flex flex-col items-center text-center transition-all hover:shadow-2xl">
            
            <!-- Foto de Perfil con esquinas redondeadas -->
            <div class="w-40 h-48 sm:w-44 sm:h-52 rounded-3xl overflow-hidden shadow-sm border border-slate-100 mb-5 flex-shrink-0 bg-slate-100">
                <img src="{{ asset('img/cristian_ramirez.png') }}" 
                     alt="Cristian Camilo Ramírez Torres" 
                     class="w-full h-full object-cover">
            </div>

            <!-- Nombre -->
            <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-snug">
                Cristian Camilo Ramírez Torres
            </h1>

            <!-- Subtítulo / Rol -->
            <p class="text-sm font-bold text-sky-800 tracking-wide mt-1.5 mb-3.5">
                Líder Full-Stack
            </p>

            <!-- Descripción de funciones / Liderazgo -->
            <p class="text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal mb-6 px-1">
                Liderazgo técnico, diseño de arquitectura, base de datos, desarrollo backend, estructuracion de controladores, verificacion y autenticacion y apoyo en liderazgo.
            </p>

            <!-- Etiquetas / Habilidades (Tags Pills) -->
            <div class="flex flex-wrap justify-center gap-1.5 sm:gap-2 mb-6">
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">MVC</span>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">Backend</span>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">Frontend</span>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">Laravel</span>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">Base de datos</span>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">Tailwind</span>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">Controladores</span>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">Liderazgo</span>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">UI / UX</span>
            </div>

            <!-- Línea Separadora -->
            <div class="w-full border-t border-slate-100 mb-5"></div>

            <!-- Botones de Redes Sociales y Contacto Circulares -->
            <div class="flex items-center justify-center gap-3">
                
                <!-- GitHub -->
                <a href="https://github.com/cristiancamiloramireztorres89-sys/inventory_laravel" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   title="GitHub" 
                   class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 flex items-center justify-center text-base transition-all active:scale-95 shadow-xs">
                    <i class="bi bi-github"></i>
                </a>

                <!-- LinkedIn -->
                <a href="https://www.linkedin.com" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   title="LinkedIn" 
                   class="w-10 h-10 rounded-full bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 flex items-center justify-center text-base transition-all active:scale-95 shadow-xs">
                    <i class="bi bi-linkedin"></i>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/573188145842" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   title="WhatsApp (+57 318 814 5842)" 
                   class="w-10 h-10 rounded-full bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 flex items-center justify-center text-base transition-all active:scale-95 shadow-xs">
                    <i class="bi bi-whatsapp"></i>
                </a>

                <!-- Email -->
                <a href="mailto:Cristianramirez8537@gmail.com" 
                   title="Correo (Cristianramirez8537@gmail.com)" 
                   class="w-10 h-10 rounded-full bg-slate-100 hover:bg-blue-500 hover:text-white text-slate-700 flex items-center justify-center text-base transition-all active:scale-95 shadow-xs">
                    <i class="bi bi-envelope-fill"></i>
                </a>

            </div>

            <!-- Institución al pie de la tarjeta -->
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-6">
                SENA CEFA
            </p>

        </div>
    </main>

    <!-- Footer Simple -->
    <footer class="py-4 text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} Inventory System &bull; Campoalegre, Huila, Colombia</p>
    </footer>

</body>
</html>
