<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desarrollador | Cristian Ramírez - Inventory System</title>

    {{-- Favicon Oficial Inventory System --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <link rel="alternate icon" href="{{ asset('favicon.svg') }}?v=2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-slate-900 selection:text-white min-h-screen flex flex-col">

    <!-- Header Navigation -->
    <header class="sticky top-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-[68px] flex items-center justify-between">
            
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 sm:gap-3 group">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-base sm:text-lg shadow-sm group-hover:scale-105 transition-transform">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="flex flex-col leading-none">
                    <span class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Inventory <span class="text-slate-500">System</span></span>
                    <span class="text-[9px] sm:text-[10px] text-slate-400 font-semibold tracking-wider uppercase mt-0.5">Gestión Inteligente</span>
                </div>
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-semibold text-slate-700 transition-colors">
                    <i class="bi bi-arrow-left text-slate-900"></i>
                    <span>Volver al Inicio</span>
                </a>

                @auth
                    <a href="{{ auth()->user()->esAdmin() ? route('admin.dashboard') : route('vendedor.dashboard') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors">
                        <i class="bi bi-box-arrow-in-right"></i> Acceder
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-10 sm:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Hero Perfil Card -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm overflow-hidden mb-8">
                
                <!-- Portada decorativa oscura -->
                <div class="h-36 sm:h-44 bg-slate-900 relative overflow-hidden flex items-center justify-between px-6 sm:px-10">
                    <div class="absolute -right-10 -bottom-10 w-60 h-60 rounded-full bg-slate-800/60 pointer-events-none"></div>
                    <div class="absolute left-10 -top-10 w-40 h-40 rounded-full bg-slate-800/40 pointer-events-none"></div>

                    <div class="relative z-10">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-slate-200">
                            <i class="bi bi-code-slash text-emerald-400"></i> Perfil Profesional
                        </span>
                        <h1 class="text-xl sm:text-2xl font-bold text-white mt-2">Información del Desarrollador</h1>
                    </div>

                    <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-white/10 border border-white/15 text-white items-center justify-center text-2xl relative z-10">
                        <i class="bi bi-terminal"></i>
                    </div>
                </div>

                <!-- Info Principal con Avatar -->
                <div class="px-6 sm:px-10 pb-8 relative pt-14 sm:pt-16">
                    
                    <!-- Avatar flotante -->
                    <div class="absolute -top-12 left-6 sm:left-10 w-24 h-24 rounded-2xl bg-slate-900 text-white border-4 border-white shadow-xl flex items-center justify-center text-3xl font-extrabold tracking-wider">
                        CR
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
                        <div>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Cristian Ramírez</h2>
                            <p class="text-sm font-medium text-slate-500 mt-0.5">Desarrollador de Software & Creador de Inventory System</p>
                        </div>

                        <!-- Botón WhatsApp Directo -->
                        <div class="flex items-center gap-2">
                            <a href="https://wa.me/573188145842" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow transition-all active:scale-95">
                                <i class="bi bi-whatsapp text-sm"></i> Chatear por WhatsApp
                            </a>
                        </div>
                    </div>

                    <!-- Datos Clave de Contacto -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
                        
                        <!-- Email -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg flex-shrink-0">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Correo Electrónico</span>
                                <a href="mailto:Cristianramirez8537@gmail.com" class="text-xs sm:text-sm font-bold text-slate-900 hover:text-blue-600 transition-colors break-all">
                                    Cristianramirez8537@gmail.com
                                </a>
                            </div>
                        </div>

                        <!-- Teléfono -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Teléfono / Celular</span>
                                <a href="tel:+573188145842" class="text-xs sm:text-sm font-bold text-slate-900 hover:text-emerald-600 transition-colors">
                                    +57 318 814 5842
                                </a>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Ubicación</span>
                                <p class="text-xs sm:text-sm font-bold text-slate-900">
                                    Campoalegre, Huila, Colombia
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Ficha Técnica del Proyecto & Stack -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-10 mb-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center text-base">
                        <i class="bi bi-layers-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Stack Tecnológico del Proyecto</h3>
                        <p class="text-xs text-slate-500">Herramientas y tecnologías utilizadas en el desarrollo de Inventory System</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                    
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                        <i class="bi bi-filetype-php text-2xl text-indigo-600 mb-2 block"></i>
                        <span class="text-xs font-bold text-slate-900 block">Laravel 11</span>
                        <span class="text-[10px] text-slate-500">Backend & MVC</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                        <i class="bi bi-database text-2xl text-blue-600 mb-2 block"></i>
                        <span class="text-xs font-bold text-slate-900 block">TiDB Cloud / MySQL</span>
                        <span class="text-[10px] text-slate-500">Base de Datos</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                        <i class="bi bi-palette text-2xl text-teal-600 mb-2 block"></i>
                        <span class="text-xs font-bold text-slate-900 block">Tailwind CSS</span>
                        <span class="text-[10px] text-slate-500">Diseño Responsivo</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                        <i class="bi bi-shield-check text-2xl text-emerald-600 mb-2 block"></i>
                        <span class="text-xs font-bold text-slate-900 block">Brevo API HTTPS</span>
                        <span class="text-[10px] text-slate-500">Correos & Seguridad</span>
                    </div>

                </div>

                <!-- Detalle de Funcionalidades implementadas -->
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Módulos Desarrollados</h4>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-semibold flex items-center gap-1.5">
                            <i class="bi bi-box-seam text-slate-900"></i> Gestión de Productos & Stock
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-semibold flex items-center gap-1.5">
                            <i class="bi bi-cash-stack text-slate-900"></i> Punto de Venta & Facturación POS
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-semibold flex items-center gap-1.5">
                            <i class="bi bi-truck text-slate-900"></i> Compras & Directorio de Proveedores
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-semibold flex items-center gap-1.5">
                            <i class="bi bi-people text-slate-900"></i> Roles & Permisos (Admin y Vendedor)
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-semibold flex items-center gap-1.5">
                            <i class="bi bi-file-earmark-pdf text-slate-900"></i> Exportación en PDF con DomPDF
                        </span>
                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-semibold flex items-center gap-1.5">
                            <i class="bi bi-key text-slate-900"></i> Recuperación de Cuenta con Código de 6 Dígitos
                        </span>
                    </div>
                </div>
            </div>

            <!-- Acciones de Navegación Finales -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url('/') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="bi bi-house"></i> Volver a la Página Principal
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-800 font-bold text-xs shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Ir a Iniciar Sesión
                </a>
            </div>

        </div>
    </main>

    <!-- Footer Simple -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} Inventory System &bull; Creado por <strong>Cristian Ramírez</strong> &bull; Campoalegre, Huila, Colombia.</p>
    </footer>

</body>
</html>
