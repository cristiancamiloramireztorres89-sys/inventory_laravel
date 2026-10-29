@php
    $usuario = auth()->user();
    $esAdmin = $usuario?->esAdmin() ?? false;
    $currentRoute = request()->route()?->getName();
@endphp

<aside id="appSidebar" class="w-64 h-screen sticky top-0 z-40 flex-shrink-0 flex flex-col select-none border-r border-white/[0.08]"
       style="background: #0d1117;">

    {{-- 1. Logo Original Pulido con Botón de Colapso Elegante --}}
    <div class="sidebar-logo-box flex items-center justify-between px-4 py-4 border-b border-white/[0.08] min-h-[73px]">
        <div class="sidebar-logo-brand flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                 style="background: #1c2a3a;" title="Inventory System">
                <i class="bi bi-box-seam-fill text-white text-lg"></i>
            </div>
            <div class="sidebar-text leading-tight overflow-hidden whitespace-nowrap">
                <p class="text-white font-extrabold text-base tracking-tight">Inventory</p>
                <p class="text-[11px] font-bold tracking-widest uppercase" style="color: #4b6280;">System</p>
            </div>
        </div>
        <button id="btnToggleSidebar" type="button" onclick="toggleSidebar()"
                title="Plegar / Expandir menú lateral"
                class="w-8 h-8 rounded-lg bg-white/[0.06] hover:bg-white/[0.12] border border-white/[0.08] hover:border-white/[0.18] text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer flex-shrink-0 shadow-2xs active:scale-95">
            <i id="iconoSidebar" class="bi bi-layout-sidebar-inset-reverse text-sm"></i>
        </button>
    </div>

    {{-- 2. Contenido del Navegador con Logos y Tooltips --}}
    <nav class="flex-1 px-3.5 py-4 space-y-5 overflow-y-auto overflow-x-hidden">

        {{-- SECCIÓN: PRINCIPAL --}}
        <div>
            <p class="sidebar-section-title px-3 mb-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color: #4b6280;">Principal</p>
            <ul class="space-y-1">
                @php
                    $dashRoute = $esAdmin ? 'admin.dashboard' : 'vendedor.dashboard';
                    $dashActive = $currentRoute === $dashRoute;
                @endphp
                <li>
                    <a href="{{ route($dashRoute) }}"
                       title="{{ $esAdmin ? 'Dashboard Administrador' : 'Dashboard Vendedor' }}"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $dashActive
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(56,189,248,0.35);'
                           : 'color: #ffffff;' }}"
                       onmouseover="if(!{{ $dashActive ? 'true' : 'false' }}) { this.style.background='#141f2e'; }"
                       onmouseout="if(!{{ $dashActive ? 'true' : 'false' }}) { this.style.background='transparent'; }">
                        <i class="bi bi-speedometer2 text-lg w-6 text-center flex-shrink-0" style="color: #38bdf8;"></i>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden text-ellipsis">{{ $esAdmin ? 'Dashboard Administrador' : 'Dashboard Vendedor' }}</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN: GESTIÓN (Solo Administrador) --}}
        @if ($esAdmin)
        <div>
            <p class="sidebar-section-title px-3 mb-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color: #4b6280;">Gestión</p>
            <ul class="space-y-1">

                {{-- Usuarios --}}
                @php $activeU = $currentRoute === 'admin.usuarios'; @endphp
                <li>
                    <a href="{{ route('admin.usuarios') }}"
                       title="Usuarios"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeU
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(56,189,248,0.35);'
                           : 'color: #ffffff;' }}"
                       onmouseover="if(!{{ $activeU ? 'true' : 'false' }}) { this.style.background='#141f2e'; }"
                       onmouseout="if(!{{ $activeU ? 'true' : 'false' }}) { this.style.background='transparent'; }">
                        <i class="bi bi-people-fill text-lg w-6 text-center flex-shrink-0" style="color: #38bdf8;"></i>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden text-ellipsis">Usuarios</span>
                    </a>
                </li>

                {{-- Categorías --}}
                @php $activeC = $currentRoute === 'admin.categorias'; @endphp
                <li>
                    <a href="{{ route('admin.categorias') }}"
                       title="Categorías"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeC
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(56,189,248,0.35);'
                           : 'color: #ffffff;' }}"
                       onmouseover="if(!{{ $activeC ? 'true' : 'false' }}) { this.style.background='#141f2e'; }"
                       onmouseout="if(!{{ $activeC ? 'true' : 'false' }}) { this.style.background='transparent'; }">
                        <i class="bi bi-tags-fill text-lg w-6 text-center flex-shrink-0" style="color: #38bdf8;"></i>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden text-ellipsis">Categorías</span>
                    </a>
                </li>

                {{-- Productos --}}
                @php $activeP = $currentRoute === 'admin.productos'; @endphp
                <li>
                    <a href="{{ route('admin.productos') }}"
                       title="Productos"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeP
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(56,189,248,0.35);'
                           : 'color: #ffffff;' }}"
                       onmouseover="if(!{{ $activeP ? 'true' : 'false' }}) { this.style.background='#141f2e'; }"
                       onmouseout="if(!{{ $activeP ? 'true' : 'false' }}) { this.style.background='transparent'; }">
                        <i class="bi bi-box-seam-fill text-lg w-6 text-center flex-shrink-0" style="color: #38bdf8;"></i>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden text-ellipsis">Productos</span>
                    </a>
                </li>

            </ul>
        </div>
        @endif

        {{-- SECCIÓN: OPERACIONES --}}
        <div>
            <p class="sidebar-section-title px-3 mb-2 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap" style="color: #4b6280;">Operaciones</p>
            <ul class="space-y-1">

                {{-- Compras --}}
                @php
                    $comprasRoute = $esAdmin ? 'admin.compras' : 'vendedor.compras';
                    $activeComp = $currentRoute === $comprasRoute;
                @endphp
                <li>
                    <a href="{{ route($comprasRoute) }}"
                       title="Compras"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeComp
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(56,189,248,0.35);'
                           : 'color: #ffffff;' }}"
                       onmouseover="if(!{{ $activeComp ? 'true' : 'false' }}) { this.style.background='#141f2e'; }"
                       onmouseout="if(!{{ $activeComp ? 'true' : 'false' }}) { this.style.background='transparent'; }">
                        <i class="bi bi-cart-fill text-lg w-6 text-center flex-shrink-0" style="color: #38bdf8;"></i>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden text-ellipsis">Compras</span>
                    </a>
                </li>

                {{-- Ventas --}}
                @php
                    $ventasRoute = $esAdmin ? 'admin.ventas' : 'vendedor.ventas';
                    $activeVent = $currentRoute === $ventasRoute;
                @endphp
                <li>
                    <a href="{{ route($ventasRoute) }}"
                       title="Ventas"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeVent
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(56,189,248,0.35);'
                           : 'color: #ffffff;' }}"
                       onmouseover="if(!{{ $activeVent ? 'true' : 'false' }}) { this.style.background='#141f2e'; }"
                       onmouseout="if(!{{ $activeVent ? 'true' : 'false' }}) { this.style.background='transparent'; }">
                        <i class="bi bi-graph-up-arrow text-lg w-6 text-center flex-shrink-0" style="color: #38bdf8;"></i>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden text-ellipsis">Ventas</span>
                    </a>
                </li>

                {{-- Ganancias --}}
                @php
                    $gananciasRoute = $esAdmin ? 'admin.ganancias' : 'vendedor.ganancias';
                    $activeGan = $currentRoute === $gananciasRoute;
                @endphp
                <li>
                    <a href="{{ route($gananciasRoute) }}"
                       title="Ganancias"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeGan
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(56,189,248,0.35);'
                           : 'color: #ffffff;' }}"
                       onmouseover="if(!{{ $activeGan ? 'true' : 'false' }}) { this.style.background='#141f2e'; }"
                       onmouseout="if(!{{ $activeGan ? 'true' : 'false' }}) { this.style.background='transparent'; }">
                        <i class="bi bi-cash-coin text-lg w-6 text-center flex-shrink-0" style="color: #38bdf8;"></i>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden text-ellipsis">Ganancias</span>
                    </a>
                </li>

                {{-- Productos (vista vendedor) --}}
                @if (!$esAdmin)
                @php $activePV = $currentRoute === 'vendedor.productos'; @endphp
                <li>
                    <a href="{{ route('vendedor.productos') }}"
                       title="Productos"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activePV
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(56,189,248,0.35);'
                           : 'color: #ffffff;' }}"
                       onmouseover="if(!{{ $activePV ? 'true' : 'false' }}) { this.style.background='#141f2e'; }"
                       onmouseout="if(!{{ $activePV ? 'true' : 'false' }}) { this.style.background='transparent'; }">
                        <i class="bi bi-box-seam-fill text-lg w-6 text-center flex-shrink-0" style="color: #38bdf8;"></i>
                        <span class="sidebar-label whitespace-nowrap overflow-hidden text-ellipsis">Productos</span>
                    </a>
                </li>
                @endif

            </ul>
        </div>

    </nav>

    {{-- 3. Usuario Logueado Abajo --}}
    <div class="sidebar-user-box px-3 py-3 border-t border-white/[0.08]">
        <div class="sidebar-user-wrapper flex items-center gap-2">
            <a href="{{ route('perfil') }}"
               class="flex items-center gap-2.5 flex-1 min-w-0 p-1.5 rounded-xl hover:bg-white/[0.06] transition-colors group"
               title="{{ $usuario->nombre ?? 'Usuario' }} - Ver Perfil">
                @if($usuario && $usuario->foto_url)
                    <img src="{{ $usuario->foto_url }}"
                         alt="{{ $usuario->nombre }}"
                         class="w-9 h-9 rounded-full object-cover flex-shrink-0 border border-sky-400/30 shadow-sm">
                @else
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white shadow-sm"
                         style="background: #1c3a5e;">
                        {{ strtoupper(substr($usuario->nombre ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <div class="sidebar-user-text flex-1 min-w-0 overflow-hidden">
                    <p class="text-xs font-semibold text-white truncate group-hover:text-sky-300 transition-colors">{{ $usuario->nombre ?? 'Usuario' }}</p>
                    <p class="text-[10px] truncate" style="color: #4b6280;">{{ $usuario->correo ?? '' }}</p>
                </div>
            </a>
            <button type="button" onclick="abrirModalLogout()" title="Cerrar sesión"
                    class="sidebar-logout-btn w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer flex-shrink-0"
                    style="color: #4b6280; background: transparent;"
                    onmouseover="this.style.background='#1a2332'; this.style.color='#ef4444'"
                    onmouseout="this.style.background='transparent'; this.style.color='#4b6280'">
                <i class="bi bi-box-arrow-right text-sm"></i>
            </button>
        </div>
    </div>

</aside>
