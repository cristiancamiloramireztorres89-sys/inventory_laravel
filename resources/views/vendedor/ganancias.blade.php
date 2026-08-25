@extends('componentes.main')

@section('title', 'Mis Ganancias | Inventory System')
@section('page_title', 'Mis Ganancias y Utilidad Generada')
@section('page_subtitle', 'Supervisión de ingresos, costo base de mercancía y utilidad neta en tus ventas')

@section('content')

<div class="space-y-6">

    {{-- Encabezado con Métricas y Filtros --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900">Rendimiento de Tus Ventas</h3>
            <p class="text-xs text-slate-500">Márgenes y utilidad neta calculada sobre tus transacciones personales</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-semibold shadow-sm">
            <i class="bi bi-person-check text-emerald-400"></i> Ventas de {{ auth()->user()->nombre }}
        </span>
    </div>

    {{-- Tarjetas Resumen de Ganancias del Vendedor --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total Facturado (Ventas) --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between hover:border-slate-300 transition-colors">
            <div class="space-y-1">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Facturado por Ti</p>
                <h3 class="text-2xl font-black text-slate-900">${{ number_format($totalIngresos, 2) }}</h3>
                <p class="text-[11px] text-blue-600 font-semibold flex items-center gap-1">
                    <i class="bi bi-arrow-up-right"></i> Cobrado en tus ventas
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>

        {{-- Costo de Mercancía --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between hover:border-slate-300 transition-colors">
            <div class="space-y-1">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Costo Base Mercancía</p>
                <h3 class="text-2xl font-black text-slate-900">${{ number_format($totalCosto, 2) }}</h3>
                <p class="text-[11px] text-amber-600 font-semibold flex items-center gap-1">
                    <i class="bi bi-arrow-down-left"></i> Costo a proveedores
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                <i class="bi bi-cart-check-fill"></i>
            </div>
        </div>

        {{-- Tu Ganancia Neta Total --}}
        <div class="bg-white border border-emerald-200/80 rounded-2xl p-5 shadow-sm flex items-center justify-between bg-gradient-to-br from-white via-emerald-50/20 to-emerald-50/40 hover:border-emerald-300 transition-colors">
            <div class="space-y-1">
                <p class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Tu Ganancia Neta Total</p>
                <h3 class="text-2xl font-black {{ $gananciaTotal >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $gananciaTotal >= 0 ? '+$' . number_format($gananciaTotal, 2) : '-$' . number_format(abs($gananciaTotal), 2) }}
                </h3>
                <p class="text-[11px] font-bold {{ $gananciaTotal >= 0 ? 'text-emerald-700' : 'text-rose-700' }} flex items-center gap-1">
                    <i class="bi bi-check2-circle"></i> {{ $gananciaTotal >= 0 ? 'Dinero libre que ganaste' : 'Pérdida en el período' }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shadow-inner">
                <i class="bi bi-piggy-bank-fill"></i>
            </div>
        </div>

        {{-- Margen de Rendimiento --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between hover:border-slate-300 transition-colors">
            <div class="space-y-1">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Margen de Rentabilidad</p>
                <h3 class="text-2xl font-black text-indigo-600">
                    {{ $margenGlobal >= 0 ? '+' . $margenGlobal . '%' : $margenGlobal . '%' }}
                </h3>
                <p class="text-[11px] text-indigo-600 font-semibold flex items-center gap-1">
                    <i class="bi bi-graph-up"></i> Retorno sobre el costo
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                <i class="bi bi-percent"></i>
            </div>
        </div>

    </div>

    {{-- Top Productos Vendidos por el Vendedor --}}
    @if($topProductos->isNotEmpty())
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-trophy-fill text-amber-500"></i> Tus Productos Más Rentables
            </h4>
            <span class="text-[11px] text-slate-400 font-medium">Ranking personal</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            @foreach($topProductos as $tp)
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2 hover:bg-slate-100/60 transition-colors">
                    <p class="font-bold text-xs text-slate-900 truncate" title="{{ $tp['nombre'] }}">{{ $tp['nombre'] }}</p>
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-500">{{ $tp['cantidad'] }} unidades</span>
                        <span class="font-black {{ $tp['ganancia'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $tp['ganancia'] >= 0 ? '+$' . number_format($tp['ganancia'], 0, ',', '.') : '-$' . number_format(abs($tp['ganancia']), 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $tp['ganancia'] >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: {{ min(100, max(10, abs($tp['margen']))) }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-500 font-semibold text-right">Margen: +{{ $tp['margen'] }}%</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabla Detallada por Venta Propia --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Buscador --}}
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="relative flex-1 max-w-sm">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input
                    type="text"
                    id="buscadorGanancias"
                    placeholder="Buscar por cliente o producto..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:bg-white transition-all"
                    onkeyup="filtrarGanancias()"
                >
            </div>
            <p class="text-xs text-slate-500">
                Tus ventas: <span class="font-bold text-slate-800">{{ $ventas->count() }}</span> transacciones
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="tablaGanancias">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="text-left px-5 py-3.5"># Venta / Fecha</th>
                        <th class="text-left px-5 py-3.5">Cliente</th>
                        <th class="text-left px-5 py-3.5">Producto y Cantidad</th>
                        <th class="text-right px-5 py-3.5">Precio Compra (Proveedor)</th>
                        <th class="text-right px-5 py-3.5">Precio Venta (Cliente)</th>
                        <th class="text-right px-5 py-3.5">Ganancia por Unidad</th>
                        <th class="text-right px-5 py-3.5">Ganancia Total Venta</th>
                        <th class="text-center px-5 py-3.5">Margen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($ventas as $venta)
                        @php
                            $costoVenta = $venta->costo_total;
                            $gananciaVenta = $venta->ganancia_neta;
                            $margenVenta = $venta->margen_porcentaje;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors"
                            data-cliente="{{ strtolower($venta->cliente->nombre ?? '') }}"
                            data-items="{{ strtolower($venta->detalles->pluck('producto.nombre')->join(' ')) }}">
                            
                            {{-- ID Venta y Fecha --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="font-bold text-slate-900">#{{ str_pad($venta->id_venta, 5, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-[10px] text-slate-400">{{ $venta->fecha ? \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i') : 'N/A' }}</p>
                            </td>

                            {{-- Cliente --}}
                            <td class="px-5 py-4 font-medium text-slate-800">
                                <p class="font-bold text-slate-900">{{ $venta->cliente->nombre ?? 'Cliente Ocasional' }}</p>
                                @if(!empty($venta->cliente->correo))
                                    <p class="text-[10px] text-slate-400">{{ $venta->cliente->correo }}</p>
                                @endif
                            </td>

                            {{-- Detalle del Producto --}}
                            <td class="px-5 py-4 min-w-[200px]">
                                @foreach($venta->detalles as $det)
                                    <div class="space-y-0.5">
                                        <p class="font-bold text-slate-900">{{ $det->producto->nombre ?? 'Producto' }}</p>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500">
                                            <i class="bi bi-box-seam text-[10px]"></i> {{ $det->cantidad }} {{ $det->cantidad == 1 ? 'unidad' : 'unidades' }}
                                        </span>
                                    </div>
                                @endforeach
                            </td>

                            {{-- Precio de Compra (Proveedor) --}}
                            <td class="px-5 py-4 text-right">
                                @foreach($venta->detalles as $det)
                                    <div>
                                        <p class="font-bold text-slate-800">${{ number_format($det->costo_unitario_real, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">c/u</span></p>
                                        <p class="text-[10px] text-slate-500 font-medium">Total: ${{ number_format($det->costo_total, 0, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </td>

                            {{-- Precio de Venta (Cliente) --}}
                            <td class="px-5 py-4 text-right">
                                @foreach($venta->detalles as $det)
                                    <div>
                                        <p class="font-bold text-blue-700">${{ number_format($det->precio_unitario, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">c/u</span></p>
                                        <p class="text-[10px] text-blue-600/80 font-medium">Total: ${{ number_format($det->subtotal, 0, ',', '.') }}</p>
                                    </div>
                                @endforeach
                            </td>

                            {{-- Ganancia por Unidad --}}
                            <td class="px-5 py-4 text-right">
                                @foreach($venta->detalles as $det)
                                    @php $ganUnitaria = $det->ganancia_unitaria; @endphp
                                    <div>
                                        <p class="font-bold {{ $ganUnitaria >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $ganUnitaria >= 0 ? '+$' . number_format($ganUnitaria, 0, ',', '.') : '-$' . number_format(abs($ganUnitaria), 0, ',', '.') }}
                                        </p>
                                        <p class="text-[10px] text-slate-400">por unidad</p>
                                    </div>
                                @endforeach
                            </td>

                            {{-- Ganancia Total Venta --}}
                            <td class="px-5 py-4 text-right">
                                <span class="inline-block px-3 py-1.5 rounded-xl font-black text-xs {{ $gananciaVenta >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $gananciaVenta >= 0 ? '+$' . number_format($gananciaVenta, 2) : '-$' . number_format(abs($gananciaVenta), 2) }}
                                </span>
                            </td>

                            {{-- Margen --}}
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-black {{ $margenVenta >= 30 ? 'bg-emerald-100 text-emerald-800' : ($margenVenta > 0 ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $margenVenta >= 0 ? '+' . $margenVenta . '%' : $margenVenta . '%' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-wallet2 text-3xl block mb-2"></i>
                                No has registrado ventas aún para calcular ganancias.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

<script>
    function filtrarGanancias() {
        const query = document.getElementById('buscadorGanancias').value.toLowerCase();
        const filas = document.querySelectorAll('#tablaGanancias tbody tr[data-cliente]');
        filas.forEach(f => {
            const c = f.dataset.cliente || '';
            const items = f.dataset.items || '';
            f.style.display = (c.includes(query) || items.includes(query)) ? '' : 'none';
        });
    }
</script>

@endsection
