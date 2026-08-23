@extends('componentes.main')

@section('title', 'Panel Vendedor | Inventory System')
@section('page_title', 'Tu Punto de Control y Ventas')
@section('page_subtitle', 'Resumen en tiempo real de tus ventas, catálogo disponible y rendimiento personal')

@section('content')

<div class="space-y-6">

    {{-- 1. Encabezado Ejecutivo Limpio con Acciones Rápidas (Sin cuadros oscuros) --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                ¡Hola, {{ auth()->user()->nombre ?? 'Vendedor' }}! 👋
            </h2>
            <p class="text-xs font-medium text-slate-500 mt-1">
                Registra ventas al mostrador rápidamente y consulta las existencias en inventario.
            </p>
        </div>

        {{-- Botones de Acción Inmediata --}}
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('vendedor.ventas') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all">
                <i class="bi bi-cart-check-fill"></i> Registrar Venta
            </a>
            <a href="{{ route('vendedor.productos') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-all">
                <i class="bi bi-boxes"></i> Ver Catálogo
            </a>
            <a href="{{ route('vendedor.ganancias') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold shadow-2xs transition-all">
                <i class="bi bi-wallet2 text-emerald-600"></i> Mis Ganancias
            </a>
        </div>
    </div>

    {{-- 2. Tarjetas KPI de Rendimiento Personal --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total Facturado por el Vendedor --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Facturado por Ti</p>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mt-2">${{ number_format($miFacturado, 2) }}</h3>
            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2 pt-2 border-t border-slate-100 font-medium">
                <span>{{ $misVentas }} {{ $misVentas == 1 ? 'venta' : 'ventas' }}</span>
                <a href="{{ route('vendedor.ventas') }}" class="text-blue-600 font-bold hover:underline">Ver mis ventas →</a>
            </div>
        </div>

        {{-- Ganancia Neta Personal --}}
        <div class="bg-white border border-emerald-200/80 rounded-2xl p-5 shadow-sm bg-gradient-to-br from-white via-emerald-50/20 to-emerald-50/40 hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Tu Ganancia Neta</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shadow-inner">
                    <i class="bi bi-piggy-bank-fill"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black {{ $miGanancia >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-2">
                {{ $miGanancia >= 0 ? '+$' . number_format($miGanancia, 2) : '-$' . number_format(abs($miGanancia), 2) }}
            </h3>
            <div class="flex items-center justify-between text-[11px] text-emerald-700 mt-2 pt-2 border-t border-emerald-100 font-bold">
                <span>Margen: +{{ $miMargen }}%</span>
                <a href="{{ route('vendedor.ganancias') }}" class="text-emerald-800 font-bold hover:underline">Ver detalle →</a>
            </div>
        </div>

        {{-- Compras Registradas --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Tus Abastecimientos</p>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mt-2">${{ number_format($miTotalCompras, 2) }}</h3>
            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2 pt-2 border-t border-slate-100 font-medium">
                <span>{{ $misCompras }} {{ $misCompras == 1 ? 'compra' : 'compras' }}</span>
                <a href="{{ route('vendedor.compras') }}" class="text-amber-600 font-bold hover:underline">Ver compras →</a>
            </div>
        </div>

        {{-- Catálogo de Productos Disponibles --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Productos en Catálogo</p>
                <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $totalProductos }} <span class="text-xs font-normal text-slate-400">artículos</span></h3>
            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2 pt-2 border-t border-slate-100 font-medium">
                <span>{{ number_format($totalStockUnidades) }} unidades disp.</span>
                <a href="{{ route('vendedor.productos') }}" class="text-violet-600 font-bold hover:underline">Ver catálogo →</a>
            </div>
        </div>

    </div>

    {{-- 3. Fila de Tablas y Listas: Últimas Ventas Propias + Productos con Mayor Stock --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Últimas Ventas Propias (Columna 2/3) --}}
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-900 text-sm">Tus Últimas Ventas Realizadas</h4>
                        <p class="text-[11px] text-slate-500">Historial reciente de cobros al mostrador</p>
                    </div>
                </div>
                <a href="{{ route('vendedor.ventas') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 hover:underline">
                    Ver todas →
                </a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="text-left px-5 py-3"># ID Venta</th>
                            <th class="text-left px-5 py-3">Cliente</th>
                            <th class="text-left px-5 py-3">Artículos</th>
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
                                <td class="px-5 py-3.5 text-slate-500">
                                    {{ $uv->detalles->sum('cantidad') }} unid.
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
                                    No has registrado ventas aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Productos Destacados para Vender (Columna 1/3) --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center text-sm">
                            <i class="bi bi-tag-fill"></i>
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm">Productos con Más Stock</h4>
                    </div>
                    <a href="{{ route('vendedor.productos') }}" class="text-[11px] font-bold text-violet-600 hover:underline">
                        Catálogo →
                    </a>
                </div>

                <div class="mt-3 space-y-2.5">
                    @forelse($productosDestacados as $pd)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                            <div class="min-w-0 flex-1 pr-2">
                                <p class="font-bold text-slate-900 truncate" title="{{ $pd->nombre }}">{{ $pd->nombre }}</p>
                                <p class="text-[10px] text-slate-500">${{ number_format($pd->precio_venta, 0, ',', '.') }} • {{ $pd->categoria->nombre ?? 'General' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-black text-[11px] flex-shrink-0">
                                {{ $pd->stock_actual }} disp.
                            </span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            No hay productos activos en el catálogo.
                        </div>
                    @endforelse
                </div>
            </div>

            <a href="{{ route('vendedor.ventas') }}"
               class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold flex items-center justify-center gap-1.5 transition-colors shadow-sm">
                <i class="bi bi-plus-lg"></i> Registrar Venta Inmediata
            </a>
        </div>

    </div>

</div>

@endsection
