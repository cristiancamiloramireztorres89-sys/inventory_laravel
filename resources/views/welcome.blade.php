<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Inventory System - Gestión Inteligente de Inventario</title>

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
                    screens: {
                        'xs': '420px',
                    },
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
    <style>
        .nav-link {
            position: relative;
            color: #64748b;
            font-weight: 600;
            transition: all 0.25s ease;
            text-decoration: none;
            padding: 4px 2px;
        }
        .nav-link:hover {
            color: #0f172a;
            text-shadow: 0 0 10px rgba(15, 23, 42, 0.2);
        }
        .nav-link.active {
            color: #0f172a !important;
            font-weight: 800 !important;
            text-shadow: 0 0 12px rgba(15, 23, 42, 0.35), 0 0 1px #0f172a;
        }
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 100%;
            height: 2.5px;
            background: #0f172a;
            border-radius: 9999px;
            box-shadow: 0 0 10px rgba(15, 23, 42, 0.4);
            animation: glowLineIn 0.3s ease-out;
        }
        @keyframes glowLineIn {
            from { opacity: 0; transform: scaleX(0.4); }
            to { opacity: 1; transform: scaleX(1); }
        }

        /* Suavizar transiciones en el carrusel */
        .hero-slide {
            transition: opacity 0.7s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Hero a pantalla completa en móvil y PC */
        .hero-full-viewport {
            height: calc(100vh - 66px);
            height: calc(100dvh - 66px);
            min-height: 520px;
        }
        @media (min-width: 640px) {
            .hero-full-viewport {
                height: calc(100vh - 70px);
                height: calc(100dvh - 70px);
                min-height: 580px;
            }
        }
    </style>
</head>
<body class="bg-white text-slate-800 font-sans antialiased selection:bg-slate-900 selection:text-white min-h-screen flex flex-col">

    <!-- Header Navigation -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-[66px] sm:h-[70px] flex items-center justify-between">
            
            <!-- Logo y Nombre de Marca -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-base sm:text-lg shadow-sm">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="flex flex-col leading-none">
                    <span class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Inventory <span class="text-slate-500">System</span></span>
                    <span class="text-[9px] sm:text-[10px] text-slate-400 font-semibold tracking-wider uppercase mt-0.5">Gestión Inteligente</span>
                </div>
            </a>

            <!-- Menú de Enlaces Escritorio -->
            <nav class="hidden md:flex items-center gap-7 lg:gap-8 text-sm font-medium" id="mainNav">
                <a href="#inicio" class="nav-link active">Inicio</a>
                <a href="#caracteristicas" class="nav-link">Características</a>
                <a href="#modulos" class="nav-link">Módulos</a>
                <a href="#flujo" class="nav-link">Cómo Funciona</a>
                <a href="#contacto" class="nav-link">Contacto</a>
            </nav>

            <!-- Botones de Acción Derecha -->
            <div class="flex items-center gap-2 sm:gap-3">
                <button onclick="openDevModal()" class="hidden lg:inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-semibold text-slate-700 transition-colors">
                    <i class="bi bi-code-slash text-slate-900"></i> Desarrollador
                </button>

                @auth
                    <a href="{{ auth()->user()->esAdmin() ? route('admin.dashboard') : route('vendedor.dashboard') }}" class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors active:scale-95">
                        <i class="bi bi-speedometer2"></i>
                        <span class="hidden xs:inline">Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors active:scale-95">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span class="hidden sm:inline">Iniciar sesión</span>
                        <span class="sm:hidden">Acceder</span>
                    </a>
                @endauth

                <!-- Botón Hamburguesa Móvil -->
                <button id="mobileMenuBtn" aria-label="Abrir Menú" class="md:hidden w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 active:scale-95 transition-all">
                    <i class="bi bi-list text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Menú Desplegable Móvil -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white/98 backdrop-blur-xl px-4 py-4 space-y-2 text-sm font-medium text-slate-700 shadow-xl transition-all">
            <a href="#inicio" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-colors">
                <i class="bi bi-house text-slate-400"></i> Inicio
            </a>
            <a href="#caracteristicas" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-colors">
                <i class="bi bi-stars text-slate-400"></i> Características
            </a>
            <a href="#modulos" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-colors">
                <i class="bi bi-grid-1x2 text-slate-400"></i> Módulos
            </a>
            <a href="#flujo" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-colors">
                <i class="bi bi-diagram-3 text-slate-400"></i> Cómo Funciona
            </a>
            <a href="#contacto" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-colors">
                <i class="bi bi-telephone text-slate-400"></i> Contacto
            </a>
            
            <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-2">
                <button onclick="openDevModal()" class="py-2.5 px-3 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold text-center hover:bg-slate-200 transition-colors flex items-center justify-center gap-1.5">
                    <i class="bi bi-code-slash"></i> Desarrollador
                </button>
                @auth
                    <a href="{{ auth()->user()->esAdmin() ? route('admin.dashboard') : route('vendedor.dashboard') }}" class="py-2.5 px-3 rounded-xl bg-slate-900 text-white text-center text-xs font-semibold shadow hover:bg-slate-800 transition-colors flex items-center justify-center gap-1.5">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="py-2.5 px-3 rounded-xl bg-slate-900 text-white text-center text-xs font-semibold shadow hover:bg-slate-800 transition-colors flex items-center justify-center gap-1.5">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                    </a>
                @endauth
            </div>
        </div>
    </header>

    @if (session('status') || session('success'))
        <div id="logoutToast" class="fixed z-50 transition-all duration-500 ease-out" style="top: 80px; left: 50%; transform: translateX(-50%); z-index: 9999;">
            <div class="flex items-center gap-2.5 bg-white/95 backdrop-blur-md border border-emerald-200 shadow-2xl rounded-full px-5 py-2.5 text-xs font-bold text-emerald-800 whitespace-nowrap">
                <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-check2 text-xs"></i>
                </div>
                <span>{{ session('status') ?? session('success') }}</span>
                <button onclick="document.getElementById('logoutToast')?.remove()" class="text-slate-400 hover:text-slate-700 ml-2 cursor-pointer">
                    <i class="bi bi-x-lg text-[10px]"></i>
                </button>
            </div>
        </div>
        <script>
            setTimeout(function() {
                const toast = document.getElementById('logoutToast');
                if (toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translate(-50%, -20px)';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3000);
        </script>
    @endif

    <!-- ========================================================================= -->
    <!-- HERO SLIDER OPTIMIZADO PARA MÓVIL Y ESCRITORIO -->
    <!-- ========================================================================= -->
    <section id="inicio" class="relative mt-[66px] sm:mt-[70px] hero-full-viewport overflow-hidden bg-slate-950 text-white border-b border-slate-200 flex items-center justify-center">
        
        <!-- Slide 1: Gestión Inteligente -->
        <div class="hero-slide absolute inset-0 opacity-100 flex items-center justify-center px-4 sm:px-8 py-12">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1600&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Gestión de Inventario">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/70 to-slate-950/50 backdrop-blur-[1px]"></div>
            
            <div class="relative z-10 max-w-3xl mx-auto text-center w-full">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-slate-200 mb-4 sm:mb-5">
                    <i class="bi bi-shield-check text-emerald-400"></i> Plataforma Empresarial
                </span>
                <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight mb-3 sm:mb-4 drop-shadow-md">
                    Gestión Inteligente de Inventarios
                </h1>
                <p class="text-xs xs:text-sm sm:text-base md:text-lg text-slate-200/90 mb-6 sm:mb-8 max-w-xl mx-auto leading-relaxed drop-shadow">
                    Control total de tu almacén con tecnología moderna, alertas automáticas de existencias y trazabilidad de movimientos en tiempo real.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-stretch sm:items-center gap-3 w-full sm:w-auto max-w-xs sm:max-w-none mx-auto">
                    <a href="{{ route('login') }}" class="px-7 py-3 sm:py-3.5 rounded-full bg-white hover:bg-slate-100 text-slate-950 font-bold text-xs sm:text-sm shadow-xl flex items-center justify-center gap-2 transition-all active:scale-95">
                        <i class="bi bi-rocket-takeoff"></i> Comenzar Ahora
                    </a>
                    <a href="#modulos" class="px-7 py-3 sm:py-3.5 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-semibold text-xs sm:text-sm flex items-center justify-center transition-all active:scale-95">
                        Explorar Funciones
                    </a>
                </div>
            </div>
        </div>

        <!-- Slide 2: Control Total -->
        <div class="hero-slide absolute inset-0 opacity-0 pointer-events-none flex items-center justify-center px-4 sm:px-8 py-12">
            <img src="https://images.unsplash.com/photo-1553413077-190dd305871c?q=80&w=1600&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Control de Productos">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/70 to-slate-950/50 backdrop-blur-[1px]"></div>
            
            <div class="relative z-10 max-w-3xl mx-auto text-center w-full">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-slate-200 mb-4 sm:mb-5">
                    <i class="bi bi-boxes text-blue-400"></i> Stock Centralizado
                </span>
                <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight mb-3 sm:mb-4 drop-shadow-md">
                    Control Total de Productos y Stock
                </h1>
                <p class="text-xs xs:text-sm sm:text-base md:text-lg text-slate-200/90 mb-6 sm:mb-8 max-w-xl mx-auto leading-relaxed drop-shadow">
                    Gestiona productos, entradas por compras a proveedores, salidas por ventas en mostrador y usuarios desde un solo panel.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-stretch sm:items-center gap-3 w-full sm:w-auto max-w-xs sm:max-w-none mx-auto">
                    <a href="{{ route('login') }}" class="px-7 py-3 sm:py-3.5 rounded-full bg-white hover:bg-slate-100 text-slate-950 font-bold text-xs sm:text-sm shadow-xl flex items-center justify-center gap-2 transition-all active:scale-95">
                        <i class="bi bi-boxes"></i> Ingresar al Sistema
                    </a>
                    <a href="#caracteristicas" class="px-7 py-3 sm:py-3.5 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-semibold text-xs sm:text-sm flex items-center justify-center transition-all active:scale-95">
                        Ver Características
                    </a>
                </div>
            </div>
        </div>

        <!-- Slide 3: Análisis en Tiempo Real -->
        <div class="hero-slide absolute inset-0 opacity-0 pointer-events-none flex items-center justify-center px-4 sm:px-8 py-12">
            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1600&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Análisis y Estadísticas">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/70 to-slate-950/50 backdrop-blur-[1px]"></div>
            
            <div class="relative z-10 max-w-3xl mx-auto text-center w-full">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-semibold text-slate-200 mb-4 sm:mb-5">
                    <i class="bi bi-graph-up text-amber-400"></i> Métricas Clave
                </span>
                <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight mb-3 sm:mb-4 drop-shadow-md">
                    Análisis y Estadísticas en Tiempo Real
                </h1>
                <p class="text-xs xs:text-sm sm:text-base md:text-lg text-slate-200/90 mb-6 sm:mb-8 max-w-xl mx-auto leading-relaxed drop-shadow">
                    Toma decisiones estratégicas con balances de ventas, compras a proveedores y rotación de mercancía actualizados al instante.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-stretch sm:items-center gap-3 w-full sm:w-auto max-w-xs sm:max-w-none mx-auto">
                    <a href="{{ route('login') }}" class="px-7 py-3 sm:py-3.5 rounded-full bg-white hover:bg-slate-100 text-slate-950 font-bold text-xs sm:text-sm shadow-xl flex items-center justify-center gap-2 transition-all active:scale-95">
                        <i class="bi bi-graph-up-arrow"></i> Acceso al Panel
                    </a>
                    <a href="#flujo" class="px-7 py-3 sm:py-3.5 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-semibold text-xs sm:text-sm flex items-center justify-center transition-all active:scale-95">
                        Conoce el Flujo
                    </a>
                </div>
            </div>
        </div>

        <!-- Flechas Slider (Ocultas en Móvil para no tapar el texto; visibles en tablet y PC) -->
        <button onclick="prevSlide()" aria-label="Slide anterior" class="hidden md:flex absolute left-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md text-white shadow-lg items-center justify-center transition-all hover:scale-105 active:scale-95">
            <i class="bi bi-chevron-left text-base"></i>
        </button>
        <button onclick="nextSlide()" aria-label="Siguiente slide" class="hidden md:flex absolute right-5 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md text-white shadow-lg items-center justify-center transition-all hover:scale-105 active:scale-95">
            <i class="bi bi-chevron-right text-base"></i>
        </button>

        <!-- Puntos Indicadores -->
        <div class="absolute bottom-11 sm:bottom-14 inset-x-0 z-20 flex items-center justify-center gap-2">
            <button onclick="goToSlide(0)" aria-label="Ir a slide 1" class="slider-dot w-7 h-2 rounded-full bg-white transition-all shadow-sm"></button>
            <button onclick="goToSlide(1)" aria-label="Ir a slide 2" class="slider-dot w-2.5 h-2 rounded-full bg-white/40 transition-all shadow-sm"></button>
            <button onclick="goToSlide(2)" aria-label="Ir a slide 3" class="slider-dot w-2.5 h-2 rounded-full bg-white/40 transition-all shadow-sm"></button>
        </div>

        <!-- Indicador de desplazamiento hacia abajo -->
        <a href="#caracteristicas" aria-label="Desplazarse abajo" class="absolute bottom-2 sm:bottom-3.5 left-1/2 -translate-x-1/2 z-20 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white/80 hover:text-white flex items-center justify-center transition-all group">
            <i class="bi bi-chevron-down text-xs group-hover:translate-y-0.5 transition-transform animate-bounce"></i>
        </a>
    </section>

    <!-- Características Principales -->
    <section id="caracteristicas" class="py-14 sm:py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest bg-slate-200/60 px-3 py-1 rounded-full">Pilares del Sistema</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 tracking-tight">Características Principales</h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-2">Herramientas diseñadas para mantener el orden, la seguridad y la rentabilidad de tu negocio.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-8">
                <div class="p-6 sm:p-7 rounded-2xl bg-white border border-slate-200/80 hover:border-slate-300 shadow-sm hover:shadow-md transition-all">
                    <div class="w-11 h-11 rounded-xl bg-slate-900 text-white flex items-center justify-center text-lg mb-4"><i class="bi bi-box-seam"></i></div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2">Productos y Existencias</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">Gestiona y controla todos los productos con categorías, códigos de barra, imágenes y niveles automáticos de stock mínimo.</p>
                </div>

                <div class="p-6 sm:p-7 rounded-2xl bg-white border border-slate-200/80 hover:border-slate-300 shadow-sm hover:shadow-md transition-all">
                    <div class="w-11 h-11 rounded-xl bg-slate-900 text-white flex items-center justify-center text-lg mb-4"><i class="bi bi-arrow-left-right"></i></div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2">Movimientos y Auditoría</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">Control de entradas (compras a proveedores) y salidas (ventas a clientes) en tiempo real con historial minucioso.</p>
                </div>

                <div class="p-6 sm:p-7 rounded-2xl bg-white border border-slate-200/80 hover:border-slate-300 shadow-sm hover:shadow-md transition-all">
                    <div class="w-11 h-11 rounded-xl bg-slate-900 text-white flex items-center justify-center text-lg mb-4"><i class="bi bi-people"></i></div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-2">Usuarios y Permisos</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">Gestión de accesos con perfiles blindados e independientes para Administradores y Vendedores con control de sesiones.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Módulos de Gestión -->
    <section id="modulos" class="py-14 sm:py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest bg-slate-100 px-3 py-1 rounded-full">Ecosistema Modular</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 tracking-tight">Módulos Integrados del Sistema</h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-2">Conectividad completa entre compras, ventas, clientes y existencias.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition-all hover:bg-slate-100/60">
                    <div class="w-9 h-9 rounded-xl bg-white text-slate-900 shadow-sm flex items-center justify-center text-base mb-3 border border-slate-200">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base mb-1">Ventas y Facturación</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Punto de venta ágil, registro de clientes, emisión de comprobantes en PDF y descuento automático de inventario.</p>
                </div>

                <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition-all hover:bg-slate-100/60">
                    <div class="w-9 h-9 rounded-xl bg-white text-slate-900 shadow-sm flex items-center justify-center text-base mb-3 border border-slate-200">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base mb-1">Compras y Proveedores</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Ingreso de mercancía por órdenes de compra, control de costes unitarios y directorio completo de proveedores.</p>
                </div>

                <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition-all hover:bg-slate-100/60">
                    <div class="w-9 h-9 rounded-xl bg-white text-slate-900 shadow-sm flex items-center justify-center text-base mb-3 border border-slate-200">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base mb-1">Directorio de Clientes</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Historial de compras por cliente, cuentas corrientes y datos de contacto centralizados.</p>
                </div>

                <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition-all hover:bg-slate-100/60">
                    <div class="w-9 h-9 rounded-xl bg-white text-slate-900 shadow-sm flex items-center justify-center text-base mb-3 border border-slate-200">
                        <i class="bi bi-tags"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base mb-1">Categorías de Producto</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Clasificación ordenada para facilitar la búsqueda rápida y el filtrado de artículos en almacén.</p>
                </div>

                <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition-all hover:bg-slate-100/60">
                    <div class="w-9 h-9 rounded-xl bg-white text-slate-900 shadow-sm flex items-center justify-center text-base mb-3 border border-slate-200">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base mb-1">Seguridad & Permisos</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Control estricto de accesos para evitar manipulaciones de datos o modificaciones indebidas de existencias.</p>
                </div>

                <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition-all hover:bg-slate-100/60">
                    <div class="w-9 h-9 rounded-xl bg-white text-slate-900 shadow-sm flex items-center justify-center text-base mb-3 border border-slate-200">
                        <i class="bi bi-pie-chart"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm sm:text-base mb-1">Reportes y Finanzas</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Resúmenes de ingresos, artículos con mayor rotación y balances financieros claros y exportables.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cómo Funciona (Flujo Operativo) -->
    <section id="flujo" class="py-14 sm:py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-widest bg-slate-200/60 px-3 py-1 rounded-full">Flujo de Trabajo</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3 tracking-tight">Operaciones en 3 Pasos Sencillos</h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-2">Cero descuadres y máxima claridad en cada transacción del día a día.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm text-center relative">
                    <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white font-extrabold flex items-center justify-center mx-auto mb-4 text-sm shadow-sm">1</div>
                    <h4 class="font-bold text-slate-900 text-base mb-2">Entrada de Mercancía</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Registras la factura del proveedor con los productos adquiridos y el stock se suma automáticamente en almacén.</p>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm text-center relative">
                    <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white font-extrabold flex items-center justify-center mx-auto mb-4 text-sm shadow-sm">2</div>
                    <h4 class="font-bold text-slate-900 text-base mb-2">Venta y Facturación</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">El vendedor procesa la venta en segundos, se emite el comprobante y el producto se descuenta de inmediato.</p>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm text-center relative">
                    <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white font-extrabold flex items-center justify-center mx-auto mb-4 text-sm shadow-sm">3</div>
                    <h4 class="font-bold text-slate-900 text-base mb-2">Auditoría y Reportes</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">El administrador revisa balances en tiempo real, verifica utilidades e identifica productos próximos a agotarse.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Zona de Contacto y Footer -->
    <div id="contacto">
        <!-- Call to Action Banner -->
        <section class="py-12 sm:py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-3xl bg-slate-900 text-white p-7 sm:p-12 lg:p-14 text-center max-w-4xl mx-auto shadow-sm">
                    <h2 class="text-2xl sm:text-3xl font-extrabold mb-3 tracking-tight">Optimiza la Gestión de tu Inventario Hoy</h2>
                    <p class="text-slate-300 text-xs sm:text-sm max-w-xl mx-auto mb-6 sm:mb-8 leading-relaxed">Inicia sesión en la plataforma y comienza a registrar productos, compras, ventas y proveedores con total seguridad.</p>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-white text-slate-900 hover:bg-slate-100 font-bold text-xs sm:text-sm transition-all active:scale-95 shadow-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Acceder a la Plataforma
                    </a>
                </div>
            </div>
        </section>

        {{-- Footer Reutilizable --}}
        @include('componentes.footer-landing')
    </div>

    <!-- Modal Desarrollador -->
    <div id="devModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-slate-800 shadow-2xl border border-slate-200">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2"><i class="bi bi-code-slash text-slate-900"></i> Desarrollador</h3>
                <button onclick="closeDevModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>
            <div class="space-y-2.5 text-xs text-slate-600">
                <p><strong>Desarrollador:</strong> Cristian Ramírez</p>
                <p><strong>Email:</strong> Cristianramirez8537@gmail.com</p>
                <p><strong>Teléfono:</strong> +57 3188145842</p>
                <p><strong>Ubicación:</strong> Campoalegre, Huila, Colombia</p>
            </div>
            <button onclick="closeDevModal()" class="mt-5 w-full py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition-colors">Cerrar</button>
        </div>
    </div>

    <!-- Script Slider & Modal con Soporte Táctil (Swipe Móvil) -->
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.slider-dot');
        let slideTimer = null;

        function showSlide(index) {
            slides.forEach((s, i) => {
                s.style.opacity = i === index ? '1' : '0';
                s.style.pointerEvents = i === index ? 'auto' : 'none';
            });
            dots.forEach((d, i) => {
                d.className = i === index 
                    ? 'slider-dot w-7 h-2 rounded-full bg-white transition-all shadow-sm' 
                    : 'slider-dot w-2.5 h-2 rounded-full bg-white/40 transition-all shadow-sm';
            });
            currentSlide = index;
        }

        function restartTimer() {
            clearInterval(slideTimer);
            slideTimer = setInterval(nextSlide, 6000);
        }

        function nextSlide() { 
            showSlide((currentSlide + 1) % slides.length); 
            restartTimer();
        }
        function prevSlide() { 
            showSlide((currentSlide - 1 + slides.length) % slides.length); 
            restartTimer();
        }
        function goToSlide(i) { 
            showSlide(i); 
            restartTimer();
        }

        slideTimer = setInterval(nextSlide, 6000);

        // Soporte de gestos táctiles (Swipe) para teléfonos móviles
        const heroSection = document.getElementById('inicio');
        let touchStartX = 0;
        let touchEndX = 0;

        heroSection.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        heroSection.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });

        function handleSwipe() {
            const swipeThreshold = 50; // Mínimo recorrido en píxeles para reconocer el gesto
            if (touchEndX < touchStartX - swipeThreshold) {
                nextSlide(); // Deslizar hacia la izquierda -> siguiente slide
            }
            if (touchEndX > touchStartX + swipeThreshold) {
                prevSlide(); // Deslizar hacia la derecha -> slide anterior
            }
        }

        function openDevModal() { document.getElementById('devModal').classList.remove('hidden'); }
        function closeDevModal() { document.getElementById('devModal').classList.add('hidden'); }

        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
            
            const mobileMenuLinks = document.querySelectorAll('#mobileMenu a');
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', () => mobileMenu.classList.add('hidden'));
            });
        }

        // ScrollSpy interactivo para iluminar la sección activa en el menú
        const navLinks = document.querySelectorAll('#mainNav .nav-link');
        const sectionIds = ['inicio', 'caracteristicas', 'modulos', 'flujo', 'contacto'];

        function setActiveNav(targetId) {
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${targetId}`) {
                    link.classList.add('active');
                }
            });
        }

        function updateActiveSection() {
            const scrollY = window.pageYOffset || document.documentElement.scrollTop;
            const windowHeight = window.innerHeight;
            const documentHeight = document.documentElement.scrollHeight;

            if (windowHeight + scrollY >= documentHeight - 120) {
                setActiveNav('contacto');
                return;
            }

            let activeId = 'inicio';
            sectionIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    const top = el.offsetTop - 140;
                    if (scrollY >= top) {
                        activeId = id;
                    }
                }
            });

            setActiveNav(activeId);
        }

        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                const targetId = this.getAttribute('href').replace('#', '');
                setActiveNav(targetId);
            });
        });

        window.addEventListener('scroll', updateActiveSection, { passive: true });
        window.addEventListener('DOMContentLoaded', updateActiveSection);
        updateActiveSection();
    </script>
</body>
</html>
