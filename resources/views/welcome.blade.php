<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] } } } }
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
    </style>
</head>
<body class="bg-white text-slate-800 font-sans antialiased selection:bg-slate-900 selection:text-white min-h-screen flex flex-col">

    <!-- Header Navigation -->
    <header class="fixed top-0 inset-x-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-[70px] flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-lg shadow-sm">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="flex flex-col leading-none">
                    <span class="text-lg font-bold text-slate-900 tracking-tight">Inventory <span class="text-slate-500">System</span></span>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase mt-0.5">Gestión Inteligente</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium" id="mainNav">
                <a href="#inicio" class="nav-link active">Inicio</a>
                <a href="#caracteristicas" class="nav-link">Características</a>
                <a href="#modulos" class="nav-link">Módulos</a>
                <a href="#flujo" class="nav-link">Cómo Funciona</a>
                <a href="#contacto" class="nav-link">Contacto</a>
            </nav>

            <div class="flex items-center gap-3">
                <button onclick="openDevModal()" class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-semibold text-slate-700 transition-colors">
                    <i class="bi bi-code-slash text-slate-900"></i> Desarrollador
                </button>
                @auth
                    <a href="{{ auth()->user()->esAdmin() ? route('admin.dashboard') : route('vendedor.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar sesión
                    </a>
                @endauth
                <button id="mobileMenuBtn" class="md:hidden p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100">
                    <i class="bi bi-list text-xl"></i>
                </button>
            </div>
        </div>

        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white px-5 py-4 space-y-3 text-sm font-medium text-slate-700 shadow-lg">
            <a href="#inicio" class="block py-1 hover:text-slate-900">Inicio</a>
            <a href="#caracteristicas" class="block py-1 hover:text-slate-900">Características</a>
            <a href="#modulos" class="block py-1 hover:text-slate-900">Módulos</a>
            <a href="#flujo" class="block py-1 hover:text-slate-900">Cómo Funciona</a>
            <a href="#contacto" class="block py-1 hover:text-slate-900">Contacto</a>
            <div class="pt-2 border-t border-slate-100 flex gap-2">
                <button onclick="openDevModal()" class="w-1/2 py-2 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">Desarrollador</button>
                @auth
                    <a href="{{ auth()->user()->esAdmin() ? route('admin.dashboard') : route('vendedor.dashboard') }}" class="w-1/2 py-2 rounded-lg bg-slate-900 text-white text-center text-xs font-semibold">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="w-1/2 py-2 rounded-lg bg-slate-900 text-white text-center text-xs font-semibold">Iniciar sesión</a>
                @endauth
            </div>
        </div>
    </header>

    @if (session('status') || session('success'))
        <div id="logoutToast" class="fixed z-50 transition-all duration-500 ease-out" style="top: 88px; left: 50%; transform: translateX(-50%); z-index: 9999;">
            <div class="flex items-center gap-3 bg-white/95 backdrop-blur-md border border-emerald-200 shadow-2xl rounded-full px-6 py-3 text-xs font-bold text-emerald-800 whitespace-nowrap">
                <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-check2-circle text-base"></i>
                </div>
                <span>{{ session('status') ?? session('success') }}</span>
                <button onclick="document.getElementById('logoutToast')?.remove()" class="text-slate-400 hover:text-slate-700 ml-2 cursor-pointer">
                    <i class="bi bi-x-lg text-xs"></i>
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
    <!-- HERO SLIDER SIN RECUADRO BLANCO (LETRAS Y BOTONES DIRECTOS) -->
    <!-- ========================================================================= -->
    <section id="inicio" class="relative mt-[70px] h-[520px] sm:h-[580px] overflow-hidden bg-slate-900 text-white border-b border-slate-200">
        
        <!-- Slide 1: Gestión Inteligente -->
        <div class="hero-slide absolute inset-0 transition-opacity duration-700 opacity-100 flex items-center justify-center p-6">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1600&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Gestión de Inventario">
            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-[1px]"></div>
            
            <div class="relative z-10 max-w-3xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight mb-4 drop-shadow-md">
                    Gestión Inteligente de Inventarios
                </h1>
                <p class="text-base sm:text-lg text-slate-200 mb-8 max-w-2xl mx-auto leading-relaxed drop-shadow">
                    Control total de tu inventario con tecnología moderna, alertas automáticas de existencias y análisis de movimientos en tiempo real.
                </p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('login') }}" class="px-8 py-3.5 rounded-full bg-white hover:bg-slate-100 text-slate-950 font-bold text-sm shadow-xl flex items-center gap-2 transition-transform hover:scale-105">
                        <i class="bi bi-rocket-takeoff"></i> Comenzar Ahora
                    </a>
                    <a href="#modulos" class="px-8 py-3.5 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-semibold text-sm transition-colors">
                        Explorar Funciones
                    </a>
                </div>
            </div>
        </div>

        <!-- Slide 2: Control Total -->
        <div class="hero-slide absolute inset-0 transition-opacity duration-700 opacity-0 pointer-events-none flex items-center justify-center p-6">
            <img src="https://images.unsplash.com/photo-1553413077-190dd305871c?q=80&w=1600&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Control de Productos">
            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-[1px]"></div>
            
            <div class="relative z-10 max-w-3xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight mb-4 drop-shadow-md">
                    Control Total de Productos y Stock
                </h1>
                <p class="text-base sm:text-lg text-slate-200 mb-8 max-w-2xl mx-auto leading-relaxed drop-shadow">
                    Gestiona productos, entradas por compras a proveedores, salidas por ventas en mostrador y usuarios desde una plataforma centralizada.
                </p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('login') }}" class="px-8 py-3.5 rounded-full bg-white hover:bg-slate-100 text-slate-950 font-bold text-sm shadow-xl flex items-center gap-2 transition-transform hover:scale-105">
                        <i class="bi bi-boxes"></i> Ingresar al Sistema
                    </a>
                    <a href="#caracteristicas" class="px-8 py-3.5 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-semibold text-sm transition-colors">
                        Ver Características
                    </a>
                </div>
            </div>
        </div>

        <!-- Slide 3: Análisis en Tiempo Real -->
        <div class="hero-slide absolute inset-0 transition-opacity duration-700 opacity-0 pointer-events-none flex items-center justify-center p-6">
            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1600&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" alt="Análisis y Estadísticas">
            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-[1px]"></div>
            
            <div class="relative z-10 max-w-3xl mx-auto text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-tight mb-4 drop-shadow-md">
                    Análisis y Estadísticas en Tiempo Real
                </h1>
                <p class="text-base sm:text-lg text-slate-200 mb-8 max-w-2xl mx-auto leading-relaxed drop-shadow">
                    Toma decisiones estratégicas con balances de ventas, compras a proveedores y rotación de mercancía actualizados al instante.
                </p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('login') }}" class="px-8 py-3.5 rounded-full bg-white hover:bg-slate-100 text-slate-950 font-bold text-sm shadow-xl flex items-center gap-2 transition-transform hover:scale-105">
                        <i class="bi bi-graph-up-arrow"></i> Acceso al Panel
                    </a>
                    <a href="#flujo" class="px-8 py-3.5 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-semibold text-sm transition-colors">
                        Conoce el Flujo
                    </a>
                </div>
            </div>
        </div>

        <!-- Flechas Slider -->
        <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md text-white shadow-lg flex items-center justify-center transition-all">
            <i class="bi bi-chevron-left text-base"></i>
        </button>
        <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md text-white shadow-lg flex items-center justify-center transition-all">
            <i class="bi bi-chevron-right text-base"></i>
        </button>

        <!-- Puntos Indicadores -->
        <div class="absolute bottom-6 inset-x-0 z-20 flex justify-center gap-2">
            <button onclick="goToSlide(0)" class="slider-dot w-7 h-2 rounded-full bg-white transition-all shadow-sm"></button>
            <button onclick="goToSlide(1)" class="slider-dot w-2.5 h-2 rounded-full bg-white/40 transition-all shadow-sm"></button>
            <button onclick="goToSlide(2)" class="slider-dot w-2.5 h-2 rounded-full bg-white/40 transition-all shadow-sm"></button>
        </div>
    </section>

    <!-- Métricas Rápidas -->
    <section class="py-10 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                    <p class="text-2xl font-bold text-slate-900">99.9%</p>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Exactitud de inventario</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                    <p class="text-2xl font-bold text-slate-900">En Vivo</p>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Sincronización instantánea</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                    <p class="text-2xl font-bold text-slate-900">Multi-Rol</p>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Admin y Vendedores</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-center">
                    <p class="text-2xl font-bold text-slate-900">Auto</p>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Alertas de stock mínimo</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Características Principales -->
    <section id="caracteristicas" class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Pilares del Sistema</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-2">Características Principales</h2>
                <p class="text-sm text-slate-600 mt-2">Herramientas diseñadas para mantener el orden, la seguridad y la rentabilidad de tu negocio.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-7 rounded-2xl bg-white border border-slate-200 hover:border-slate-300 shadow-sm transition-all">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xl mb-5"><i class="bi bi-box-seam"></i></div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Productos</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Gestiona y controla todos los productos del inventario con categorización, códigos y niveles de stock mínimo.</p>
                </div>
                <div class="p-7 rounded-2xl bg-white border border-slate-200 hover:border-slate-300 shadow-sm transition-all">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xl mb-5"><i class="bi bi-arrow-left-right"></i></div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Movimientos</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Control de entradas (compras) y salidas (ventas) de mercancía en tiempo real con historial completo detallado.</p>
                </div>
                <div class="p-7 rounded-2xl bg-white border border-slate-200 hover:border-slate-300 shadow-sm transition-all">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xl mb-5"><i class="bi bi-people"></i></div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Usuarios y Roles</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">Gestión de accesos y seguridad con perfiles independientes para Administradores y Vendedores con auditoría.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Módulos de Gestión -->
    <section id="modulos" class="py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Ecosistema Modular</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-2">Módulos Integrados del Sistema</h2>
                <p class="text-sm text-slate-600 mt-1">Conectividad completa entre compras, ventas y existencias.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-slate-300 transition-all">
                    <i class="bi bi-cash-stack text-xl text-slate-900 mb-3 block"></i>
                    <h4 class="font-bold text-slate-900 text-base mb-1">Ventas y Facturación</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Punto de venta ágil, registro de clientes, emisión de comprobantes y descuento automático.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-slate-300 transition-all">
                    <i class="bi bi-truck text-xl text-slate-900 mb-3 block"></i>
                    <h4 class="font-bold text-slate-900 text-base mb-1">Compras y Proveedores</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Ingreso de mercancía por órdenes de compra, control de costes y catálogo de proveedores.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-slate-300 transition-all">
                    <i class="bi bi-person-lines-fill text-xl text-slate-900 mb-3 block"></i>
                    <h4 class="font-bold text-slate-900 text-base mb-1">Directorio de Clientes</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Historial de compras por cliente, cuentas corrientes y datos de contacto centralizados.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-slate-300 transition-all">
                    <i class="bi bi-tags text-xl text-slate-900 mb-3 block"></i>
                    <h4 class="font-bold text-slate-900 text-base mb-1">Categorías de Producto</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Clasificación ordenada para facilitar la búsqueda rápida de artículos en almacén.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-slate-300 transition-all">
                    <i class="bi bi-shield-check text-xl text-slate-900 mb-3 block"></i>
                    <h4 class="font-bold text-slate-900 text-base mb-1">Seguridad & Permisos</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Control estricto de acceso por usuario para evitar modificaciones indebidas de stock.</p>
                </div>
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 hover:border-slate-300 transition-all">
                    <i class="bi bi-pie-chart text-xl text-slate-900 mb-3 block"></i>
                    <h4 class="font-bold text-slate-900 text-base mb-1">Reportes y Finanzas</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Resúmenes de ingresos, artículos más vendidos y balance financiero descargable.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cómo Funciona (Flujo Operativo) -->
    <section id="flujo" class="py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Flujo de Trabajo</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-2">Operaciones en 3 Pasos Sencillos</h2>
                <p class="text-sm text-slate-600 mt-1">Cero descuadres y máxima claridad en cada transacción.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm text-center">
                    <div class="w-10 h-10 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center mx-auto mb-4 text-sm">1</div>
                    <h4 class="font-bold text-slate-900 text-base mb-2">Entrada de Mercancía</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">Registras la factura del proveedor y el stock se suma automáticamente en almacén.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm text-center">
                    <div class="w-10 h-10 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center mx-auto mb-4 text-sm">2</div>
                    <h4 class="font-bold text-slate-900 text-base mb-2">Venta y Facturación</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">El vendedor procesa la venta, se genera el comprobante y el producto se descuenta.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm text-center">
                    <div class="w-10 h-10 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center mx-auto mb-4 text-sm">3</div>
                    <h4 class="font-bold text-slate-900 text-base mb-2">Auditoría y Reportes</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">El administrador revisa los balances diarios de ingresos y el estado del inventario.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Zona de Contacto y Footer -->
    <div id="contacto">
        <!-- Call to Action Banner -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-3xl bg-slate-900 text-white p-10 lg:p-14 text-center max-w-4xl mx-auto shadow-sm">
                    <h2 class="text-3xl font-bold mb-3">Optimiza la Gestión de tu Inventario Hoy</h2>
                    <p class="text-slate-300 text-sm max-w-xl mx-auto mb-7">Inicia sesión en la plataforma y comienza a registrar productos, compras, ventas y proveedores con total seguridad.</p>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-white text-slate-900 hover:bg-slate-100 font-semibold text-xs transition-colors">
                        <i class="bi bi-box-arrow-in-right"></i> Acceder a la Plataforma
                    </a>
                </div>
            </div>
        </section>

        {{-- Footer Reutilizable --}}
        @include('componentes.footer-landing')
    </div>

    <!-- Modal Desarrollador -->
    <div id="devModal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-slate-800 shadow-xl border border-slate-200">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2"><i class="bi bi-code-slash text-slate-900"></i> Desarrollador</h3>
                <button onclick="closeDevModal()" class="text-slate-400 hover:text-slate-700"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="space-y-2 text-xs text-slate-600">
                <p><strong>Desarrollador:</strong> Cristian Ramírez</p>
                <p><strong>Email:</strong> Cristianramirez8537@gmail.com</p>
                <p><strong>Teléfono:</strong> +57 3188145842</p>
                <p><strong>Ubicación:</strong> Campoalegre, Huila, Colombia</p>
            </div>
            <button onclick="closeDevModal()" class="mt-5 w-full py-2.5 rounded-xl bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition-colors">Cerrar</button>
        </div>
    </div>

    <!-- Script Slider & Modal -->
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.slider-dot');

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

        function nextSlide() { showSlide((currentSlide + 1) % slides.length); }
        function prevSlide() { showSlide((currentSlide - 1 + slides.length) % slides.length); }
        function goToSlide(i) { showSlide(i); }
        setInterval(nextSlide, 6000);

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

            // Si está cerca del final de la página (Footer / Contacto)
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

        // Click suave en enlaces del menú
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
