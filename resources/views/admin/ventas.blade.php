@extends('componentes.main')

@section('title', 'Historial Global de Ventas | Inventory System')
@section('page_title', 'Ventas del Sistema')
@section('page_subtitle', 'Supervisión y registro de transacciones comerciales')

@section('content')

<div class="space-y-6">

    {{-- Tarjetas de Resumen y Botón Nueva Venta --}}
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900">Historial de Ventas</h3>
            <p class="text-xs text-slate-500">Transacciones comerciales y salidas de inventario</p>
        </div>

        <button type="button" onclick="abrirModalCrearVenta()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer flex-shrink-0">
            <i class="bi bi-plus-lg"></i> Registrar Venta
        </button>
    </div>

    {{-- Métricas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Ventas</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalVentas }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ingresos Totales</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">${{ number_format($totalDinero, 2) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ventas por Usuario</p>
                <h3 class="text-sm font-bold text-slate-900 mt-1">Trazabilidad Total</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="bi bi-person-badge"></i>
            </div>
        </div>
    </div>

    {{-- Tabla de Ventas --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Buscador --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
            <div class="relative flex-1 max-w-sm">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input
                    type="text"
                    id="buscadorVentas"
                    placeholder="Buscar por cliente o vendedor..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:bg-white transition-all"
                    onkeyup="filtrarVentas()"
                >
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="tablaVentas">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="text-left px-6 py-3.5"># ID Venta</th>
                        <th class="text-left px-6 py-3.5">Fecha</th>
                        <th class="text-left px-6 py-3.5">Cliente</th>
                        <th class="text-left px-6 py-3.5">Vendido Por</th>
                        <th class="text-left px-6 py-3.5">Artículos</th>
                        <th class="text-right px-6 py-3.5">Total Cobrado</th>
                        <th class="text-center px-6 py-3.5">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($ventas as $venta)
                        <tr class="hover:bg-slate-50 transition-colors"
                            data-cliente="{{ strtolower($venta->cliente->nombre ?? '') }}"
                            data-usuario="{{ strtolower($venta->usuario->nombre ?? '') }}">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                #{{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $venta->fecha ? \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                <div>
                                    <p class="font-bold">{{ $venta->cliente->nombre ?? 'Cliente general' }}</p>
                                    @if(!empty($venta->cliente->correo))
                                        <p class="text-[10px] text-slate-400">{{ $venta->cliente->correo }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                                    <i class="bi bi-person-circle text-[10px]"></i>
                                    {{ $venta->usuario->nombre ?? 'Sistema' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($venta->detalles->isNotEmpty())
                                    <span class="font-semibold">{{ $venta->detalles->sum('cantidad') }} unid.</span>
                                    <span class="text-slate-400">({{ $venta->detalles->pluck('producto.nombre')->filter()->join(', ') }})</span>
                                @else
                                    <span class="text-slate-400">Sin items</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-black text-emerald-700 text-sm">
                                ${{ number_format($venta->total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.ventas.factura', $venta->id_venta) }}"
                                        target="_blank"
                                        class="p-2 rounded-xl text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 border border-transparent hover:border-indigo-100 transition-colors"
                                        title="Ver / Imprimir Factura POS">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                    <a href="{{ route('admin.ventas.factura.pdf', $venta->id_venta) }}"
                                        target="_blank"
                                        class="p-2 rounded-xl text-rose-500 hover:text-rose-700 hover:bg-rose-50 border border-transparent hover:border-rose-100 transition-colors"
                                        title="Descargar PDF">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </a>
                                    <button type="button"
                                        onclick="abrirModalEliminarVenta({{ $venta->id_venta }}, '{{ number_format($venta->total, 2) }}', '{{ addslashes($venta->cliente->nombre ?? "Cliente") }}')"
                                        class="p-2 rounded-xl text-rose-500 hover:text-rose-700 hover:bg-rose-50 border border-transparent hover:border-rose-100 transition-colors cursor-pointer"
                                        title="Eliminar Venta">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-receipt text-3xl block mb-2"></i>
                                No hay ventas registradas en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

{{-- MODALES Y SCRIPTS MODULARES --}}
@include('admin.ventas_partials.modal_eliminar')
@include('admin.ventas_partials.modal_crear')
@include('admin.ventas_partials.modal_exito')
@include('admin.ventas_partials.scripts')

@endsection