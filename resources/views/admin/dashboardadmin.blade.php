@extends('componentes.main')

@section('title', 'Panel Administrador | Inventory System')
@section('page_title', 'Panel de Control')
@section('page_subtitle', 'Supervisión integral de ventas, inventario, abastecimiento y rentabilidad')

@section('content')

<div class="space-y-6">

    {{-- 1. Encabezado Ejecutivo Limpio con Acciones Rápidas (Sin cuadros oscuros) --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                ¡Hola de nuevo, {{ auth()->user()->nombre ?? 'Administrador' }}! 👋
            </h2>
            <p class="text-xs font-medium text-slate-500 mt-1">
                Aquí tienes el resumen operativo y financiero de tu negocio en tiempo real.
            </p>
        </div>

        {{-- Botones de Acción Rápida con Estilo Limpio --}}
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.ventas') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all">
                <i class="bi bi-cart-check-fill"></i> Registrar Venta
            </a>
            <a href="{{ route('admin.compras') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-all">
                <i class="bi bi-box-arrow-in-down"></i> Nueva Compra
            </a>
            <a href="{{ route('admin.ganancias') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold shadow-2xs transition-all">
                <i class="bi bi-cash-coin text-emerald-600"></i> Ver Ganancias
            </a>
        </div>
    </div>

    {{-- 2. Tarjetas KPI de Alto Impacto --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total Facturado (Ventas) --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Ingresos Totales (Ventas)</p>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mt-2">${{ number_format($totalRecaudado, 2) }}</h3>
            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2 pt-2 border-t border-slate-100 font-medium">
                <span>{{ $totalVentas }} {{ $totalVentas == 1 ? 'venta realizada' : 'ventas realizadas' }}</span>
                <a href="{{ route('admin.ventas') }}" class="text-blue-600 font-bold hover:underline">Ver ventas →</a>
            </div>
        </div>

        {{-- Ganancia Neta Total --}}
        <div class="bg-white border border-emerald-200/80 rounded-2xl p-5 shadow-sm bg-gradient-to-br from-white via-emerald-50/20 to-emerald-50/40 hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Ganancia Neta (Utilidad)</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shadow-inner">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black {{ $gananciaTotal >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-2">
                {{ $gananciaTotal >= 0 ? '+$' . number_format($gananciaTotal, 2) : '-$' . number_format(abs($gananciaTotal), 2) }}
            </h3>
            <div class="flex items-center justify-between text-[11px] text-emerald-700 mt-2 pt-2 border-t border-emerald-100 font-bold">
                <span>Margen: +{{ $margenGlobal }}%</span>
                <a href="{{ route('admin.ganancias') }}" class="text-emerald-800 font-bold hover:underline">Ver balances →</a>
            </div>
        </div>

        {{-- Inversión en Compras --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Inversión a Proveedores</p>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mt-2">${{ number_format($totalInvertido, 2) }}</h3>
            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2 pt-2 border-t border-slate-100 font-medium">
                <span>{{ $totalCompras }} {{ $totalCompras == 1 ? 'compra registrada' : 'compras registradas' }}</span>
                <a href="{{ route('admin.compras') }}" class="text-amber-600 font-bold hover:underline">Ver compras →</a>
            </div>
        </div>

        {{-- Stock en Almacén --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Stock Total en Almacén</p>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ number_format($totalStockUnidades) }} <span class="text-xs font-normal text-slate-400">unidades</span></h3>
            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2 pt-2 border-t border-slate-100 font-medium">
                <span>{{ $totalProductos }} productos</span>
                @if($stockBajoCount > 0)
                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 font-bold text-[10px]">{{ $stockBajoCount }} stock bajo</span>
                @else
                    <span class="text-emerald-600 font-bold">Stock óptimo</span>
                @endif
            </div>
        </div>

    </div>

    {{-- 3. Fila Intermedia: Últimas Ventas (Feed en Vivo) + Alertas de Stock Bajo --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Últimas Ventas Realizadas (Columna 2/3) --}}
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-sm">Últimas Ventas Realizadas</h4>
                        <p class="text-[11px] text-slate-500">Transacciones comerciales más recientes</p>
                    </div>
                </div>
                <a href="{{ route('admin.ventas') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline">
                    Ver todas ({{ $totalVentas }}) →
                </a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="text-left px-5 py-3"># ID Venta</th>
                            <th class="text-left px-5 py-3">Cliente</th>
                            <th class="text-left px-5 py-3">Vendedor</th>
                            <th class="text-left px-5 py-3">Fecha</th>
                            <th class="text-right px-5 py-3">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ultimasVentas as $uv)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-slate-900">
                                    #{{ str_pad($uv->id_venta, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-5 py-3.5 font-medium text-slate-800">
                                    {{ $uv->cliente->nombre ?? 'Cliente Ocasional' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-semibold border border-slate-200">
                                        <i class="bi bi-person-circle"></i> {{ $uv->usuario->nombre ?? 'Sistema' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">
                                    {{ $uv->fecha ? \Carbon\Carbon::parse($uv->fecha)->format('d/m/Y H:i') : 'N/A' }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-black text-emerald-700">
                                    ${{ number_format($uv->total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">
                                    No hay ventas registradas aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Alertas de Inventario / Stock Bajo (Columna 1/3) --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm">Control de Stock Crítico</h4>
                    </div>
                    <span class="px-2 py-0.5 rounded-full {{ $stockBajoCount > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} text-[10px] font-bold">
                        {{ $stockBajoCount }} por reponer
                    </span>
                </div>

                <div class="mt-3 space-y-3">
                    @forelse($productosStockBajo as $psb)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <p class="font-bold text-xs text-slate-900 truncate" title="{{ $psb->nombre }}">{{ $psb->nombre }}</p>
                                <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-800 text-[10px] font-black">
                                    {{ $psb->stock_actual }} unid.
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-500 font-medium">
                                <span>Categoría: {{ $psb->categoria->nombre ?? 'General' }}</span>
                                <span>Mínimo: {{ $psb->stock_minimo }}</span>
                            </div>
                            <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-rose-500 h-full rounded-full" style="width: {{ min(100, max(5, ($psb->stock_actual / max(1, $psb->stock_minimo)) * 100)) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 space-y-1">
                            <i class="bi bi-check2-circle text-3xl text-emerald-500 block"></i>
                            <p class="text-xs font-semibold text-slate-600">¡Inventario en estado óptimo!</p>
                            <p class="text-[11px] text-slate-400">Todos los productos superan su stock mínimo.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <a href="{{ route('admin.compras') }}"
               class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition-colors shadow-sm">
                <i class="bi bi-plus-lg"></i> Abastecer con Nueva Compra
            </a>
        </div>

    </div>

    {{-- 4. Fila Inferior: Top Productos Rentables + Últimas Compras --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Top Productos Más Rentables --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-sm">Top Productos Más Rentables</h4>
                        <p class="text-[11px] text-slate-500">Artículos que más ganancia neta generan</p>
                    </div>
                </div>
                <a href="{{ route('admin.ganancias') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline">
                    Ver balance completo →
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse($topProductos as $tp)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
                        <div class="min-w-0 flex-1 pr-3">
                            <p class="font-bold text-slate-900 truncate">{{ $tp['nombre'] }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">{{ $tp['cantidad'] }} unidades vendidas • Margen: +{{ $tp['margen'] }}%</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="font-black text-emerald-600 text-sm block">
                                {{ $tp['ganancia'] >= 0 ? '+$' . number_format($tp['ganancia'], 0, ',', '.') : '-$' . number_format(abs($tp['ganancia']), 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-slate-400">utilidad neta</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">
                        No hay suficientes datos de ventas aún.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Últimas Órdenes de Compra a Proveedores --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                        <i class="bi bi-box-arrow-in-down"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-sm">Últimos Abastecimientos</h4>
                        <p class="text-[11px] text-slate-500">Órdenes de compra registradas a proveedores</p>
                    </div>
                </div>
                <a href="{{ route('admin.compras') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline">
                    Ver todas →
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse($ultimasCompras as $uc)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
                        <div class="min-w-0 flex-1 pr-3">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900">#{{ str_pad($uc->id_compra, 5, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-slate-600 font-semibold truncate">{{ $uc->proveedor->nombre ?? 'Proveedor' }}</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                {{ $uc->fecha ? \Carbon\Carbon::parse($uc->fecha)->format('d/m/Y H:i') : 'N/A' }} • Por: {{ $uc->usuario->nombre ?? 'Sistema' }}
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="font-black text-slate-900 text-sm block">
                                ${{ number_format($uc->total, 2) }}
                            </span>
                            <span class="text-[10px] text-slate-400">inversión</span>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">
                        No hay compras registradas aún.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

@endsection
