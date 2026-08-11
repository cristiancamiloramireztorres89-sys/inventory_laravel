@php
    $usuario = auth()->user();
    $esAdmin = $usuario?->esAdmin() ?? false;
    $currentRoute = request()->route()?->getName();
@endphp

<aside class="w-64 h-screen sticky top-0 z-40 flex-shrink-0 flex flex-col select-none border-r border-white/[0.08]"
       style="background: #0d1117;">

    {{-- 1. Logo Original Pulido --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/[0.08]">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
             style="background: #1c2a3a;">
            <i class="bi bi-box-seam-fill text-white text-lg"></i>
        </div>
        <div class="leading-tight">
            <p class="text-white font-extrabold text-base tracking-tight">Inventory</p>
            <p class="text-[11px] font-bold tracking-widest uppercase" style="color: #4b6280;">System</p>
        </div>
    </div>

    {{-- 2. Contenido del Navegador --}}
    <nav class="flex-1 px-3.5 py-4 space-y-5 overflow-y-auto">

        {{-- Badge de Rol --}}
        <div class="px-1.5">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider shadow-sm"
                  style="background: rgba(56,189,148,0.12); color: #34d399; border: 1px solid rgba(52,211,153,0.25);">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                {{ $esAdmin ? 'Administrador' : 'Vendedor' }}
            </span>
        </div>

        {{-- SECCIÓN: PRINCIPAL --}}
        <div>
            <p class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest" style="color: #4b6280;">Principal</p>
            <ul class="space-y-1">
                @php
                    $dashRoute = $esAdmin ? 'admin.dashboard' : 'vendedor.dashboard';
                    $dashActive = $currentRoute === $dashRoute;
                @endphp
                <li>
                    <a href="{{ route($dashRoute) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $dashActive
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(59,130,246,0.3);'
                           : 'color: #8ba3bc;' }}"
                       onmouseover="if(!{{ $dashActive ? 'true' : 'false' }}) { this.style.background='#141f2e'; this.style.color='#ffffff'; }"
                       onmouseout="if(!{{ $dashActive ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='#8ba3bc'; }">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-transform"
                              style="background: {{ $dashActive ? '#2563eb' : '#162032' }};">
                            <i class="bi bi-speedometer2 text-sm" style="color: {{ $dashActive ? '#fff' : '#3b82f6' }};"></i>
                        </span>
                        Dashboard
                    </a>
                </li>
            </ul>
        </div>

        {{-- SECCIÓN: GESTIÓN (Solo Administrador) --}}
        @if ($esAdmin)
        <div>
            <p class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest" style="color: #4b6280;">Gestión</p>
            <ul class="space-y-1">

                {{-- Usuarios --}}
                @php $activeU = $currentRoute === 'admin.usuarios'; @endphp
                <li>
                    <a href="{{ route('admin.usuarios') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeU
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(99,102,241,0.3);'
                           : 'color: #8ba3bc;' }}"
                       onmouseover="if(!{{ $activeU ? 'true' : 'false' }}) { this.style.background='#141f2e'; this.style.color='#ffffff'; }"
                       onmouseout="if(!{{ $activeU ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='#8ba3bc'; }">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                              style="background: {{ $activeU ? '#4f46e5' : '#1a1f2e' }};">
                            <i class="bi bi-people-fill text-sm" style="color: {{ $activeU ? '#fff' : '#818cf8' }};"></i>
                        </span>
                        Usuarios
                    </a>
                </li>

                {{-- Categorías --}}
                @php $activeC = $currentRoute === 'admin.categorias'; @endphp
                <li>
                    <a href="{{ route('admin.categorias') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeC
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(16,185,129,0.3);'
                           : 'color: #8ba3bc;' }}"
                       onmouseover="if(!{{ $activeC ? 'true' : 'false' }}) { this.style.background='#141f2e'; this.style.color='#ffffff'; }"
                       onmouseout="if(!{{ $activeC ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='#8ba3bc'; }">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                              style="background: {{ $activeC ? '#059669' : '#0d2218' }};">
                            <i class="bi bi-tags-fill text-sm" style="color: {{ $activeC ? '#fff' : '#34d399' }};"></i>
                        </span>
                        Categorías
                    </a>
                </li>

                {{-- Productos --}}
                @php $activeP = $currentRoute === 'admin.productos'; @endphp
                <li>
                    <a href="{{ route('admin.productos') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeP
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(139,92,246,0.3);'
                           : 'color: #8ba3bc;' }}"
                       onmouseover="if(!{{ $activeP ? 'true' : 'false' }}) { this.style.background='#141f2e'; this.style.color='#ffffff'; }"
                       onmouseout="if(!{{ $activeP ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='#8ba3bc'; }">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                              style="background: {{ $activeP ? '#7c3aed' : '#1a1228' }};">
                            <i class="bi bi-box-seam-fill text-sm" style="color: {{ $activeP ? '#fff' : '#a78bfa' }};"></i>
                        </span>
                        Productos
                    </a>
                </li>

            </ul>
        </div>
        @endif

        {{-- SECCIÓN: OPERACIONES --}}
        <div>
            <p class="px-3 mb-2 text-[10px] font-bold uppercase tracking-widest" style="color: #4b6280;">Operaciones</p>
            <ul class="space-y-1">

                {{-- Compras --}}
                @php
                    $comprasRoute = $esAdmin ? 'admin.compras' : 'vendedor.compras';
                    $activeComp = $currentRoute === $comprasRoute;
                @endphp
                <li>
                    <a href="{{ route($comprasRoute) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeComp
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(245,158,11,0.3);'
                           : 'color: #8ba3bc;' }}"
                       onmouseover="if(!{{ $activeComp ? 'true' : 'false' }}) { this.style.background='#141f2e'; this.style.color='#ffffff'; }"
                       onmouseout="if(!{{ $activeComp ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='#8ba3bc'; }">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                              style="background: {{ $activeComp ? '#b45309' : '#1f1608' }};">
                            <i class="bi bi-cart-fill text-sm" style="color: {{ $activeComp ? '#fff' : '#f59e0b' }};"></i>
                        </span>
                        Compras
                    </a>
                </li>

                {{-- Ventas --}}
                @php
                    $ventasRoute = $esAdmin ? 'admin.ventas' : 'vendedor.ventas';
                    $activeVent = $currentRoute === $ventasRoute;
                @endphp
                <li>
                    <a href="{{ route($ventasRoute) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeVent
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(16,185,129,0.3);'
                           : 'color: #8ba3bc;' }}"
                       onmouseover="if(!{{ $activeVent ? 'true' : 'false' }}) { this.style.background='#141f2e'; this.style.color='#ffffff'; }"
                       onmouseout="if(!{{ $activeVent ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='#8ba3bc'; }">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                              style="background: {{ $activeVent ? '#065f46' : '#061a12' }};">
                            <i class="bi bi-graph-up-arrow text-sm" style="color: {{ $activeVent ? '#fff' : '#10b981' }};"></i>
                        </span>
                        Ventas
                    </a>
                </li>

                {{-- Ganancias --}}
                @php
                    $gananciasRoute = $esAdmin ? 'admin.ganancias' : 'vendedor.ganancias';
                    $activeGan = $currentRoute === $gananciasRoute;
                @endphp
                <li>
                    <a href="{{ route($gananciasRoute) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activeGan
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(52,211,153,0.3);'
                           : 'color: #8ba3bc;' }}"
                       onmouseover="if(!{{ $activeGan ? 'true' : 'false' }}) { this.style.background='#141f2e'; this.style.color='#ffffff'; }"
                       onmouseout="if(!{{ $activeGan ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='#8ba3bc'; }">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                              style="background: {{ $activeGan ? '#059669' : '#092116' }};">
                            <i class="bi bi-cash-coin text-sm" style="color: {{ $activeGan ? '#fff' : '#34d399' }};"></i>
                        </span>
                        Ganancias
                    </a>
                </li>

                {{-- Productos (vista vendedor) --}}
                @if (!$esAdmin)
                @php $activePV = $currentRoute === 'vendedor.productos'; @endphp
                <li>
                    <a href="{{ route('vendedor.productos') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all duration-150"
                       style="{{ $activePV
                           ? 'background: #1a2d45; color: #ffffff; border: 1px solid rgba(139,92,246,0.3);'
                           : 'color: #8ba3bc;' }}"
                       onmouseover="if(!{{ $activePV ? 'true' : 'false' }}) { this.style.background='#141f2e'; this.style.color='#ffffff'; }"
                       onmouseout="if(!{{ $activePV ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='#8ba3bc'; }">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                              style="background: {{ $activePV ? '#7c3aed' : '#1a1228' }};">
                            <i class="bi bi-box-seam-fill text-sm" style="color: {{ $activePV ? '#fff' : '#a78bfa' }};"></i>
                        </span>
                        Productos
                    </a>
                </li>
                @endif

            </ul>
        </div>

    </nav>

    {{-- 3. Usuario Logueado Abajo --}}
    <div class="px-4 py-4 border-t border-white/[0.08]">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white shadow-sm"
                 style="background: #1c3a5e;">
                {{ strtoupper(substr($usuario->nombre ?? 'U', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white truncate">{{ $usuario->nombre ?? 'Usuario' }}</p>
                <p class="text-[10px] truncate" style="color: #4b6280;">{{ $usuario->correo ?? '' }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Cerrar sesión"
                        class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                        style="color: #4b6280; background: transparent;"
                        onmouseover="this.style.background='#1a2332'; this.style.color='#ef4444'"
                        onmouseout="this.style.background='transparent'; this.style.color='#4b6280'">
                    <i class="bi bi-box-arrow-right text-sm"></i>
                </button>
            </form>
        </div>
    </div>

</aside>
