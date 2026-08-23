@extends('componentes.main')

@section('title', 'Mis Compras | Inventory System')
@section('page_title', 'Mis Compras')
@section('page_subtitle', 'Registro de órdenes de abastecimiento registradas por tu usuario')

@section('content')

<div class="space-y-6">

    {{-- Encabezado con Botón Nueva Compra --}}
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900">Mis Compras Registradas</h3>
            <p class="text-xs text-slate-500">Historial de órdenes de abastecimiento a proveedores</p>
        </div>

        <button type="button" onclick="abrirModalCrearCompraVendedor()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer flex-shrink-0">
            <i class="bi bi-plus-lg"></i> Registrar Compra
        </button>
    </div>

    {{-- Tarjetas de Resumen Personal --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tus Compras Registradas</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalCompras }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="bi bi-box-arrow-in-down"></i>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Invertido por Ti</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">${{ number_format($totalInvertido, 2) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>

    {{-- Tabla de Compras Propias --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Buscador --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between gap-4">
            <div class="relative flex-1 max-w-sm">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input
                    type="text"
                    id="buscadorCompras"
                    placeholder="Buscar por proveedor o producto..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:bg-white transition-all"
                    onkeyup="filtrarCompras()"
                >
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
                <i class="bi bi-lock-fill text-[10px]"></i>
                Filtrado por tu usuario ({{ auth()->user()->nombre }})
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="tablaCompras">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="text-left px-6 py-3.5"># ID Compra</th>
                        <th class="text-left px-6 py-3.5">Fecha</th>
                        <th class="text-left px-6 py-3.5">Proveedor</th>
                        <th class="text-left px-6 py-3.5">Artículos Comprados</th>
                        <th class="text-right px-6 py-3.5">Total</th>
                        <th class="text-center px-6 py-3.5">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($compras as $compra)
                        <tr class="hover:bg-slate-50 transition-colors"
                            data-proveedor="{{ strtolower($compra->proveedor->nombre ?? '') }}"
                            data-items="{{ strtolower($compra->detalles->pluck('producto.nombre')->join(' ')) }}">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                #{{ str_pad($compra->id_compra, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ $compra->fecha ? \Carbon\Carbon::parse($compra->fecha)->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                <div>
                                    <p class="font-bold">{{ $compra->proveedor->nombre ?? 'Proveedor general' }}</p>
                                    @if(!empty($compra->proveedor->correo))
                                        <p class="text-[10px] text-slate-400">{{ $compra->proveedor->correo }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($compra->detalles->isNotEmpty())
                                    <span class="font-semibold">{{ $compra->detalles->sum('cantidad') }} unid.</span>
                                    <span class="text-slate-400">({{ $compra->detalles->pluck('producto.nombre')->filter()->join(', ') }})</span>
                                @else
                                    <span class="text-slate-400">Sin items</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-black text-slate-900">
                                ${{ number_format($compra->total, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button"
                                    onclick="abrirModalEliminarCompraVendedor({{ $compra->id_compra }}, '{{ number_format($compra->total, 2) }}', '{{ addslashes($compra->proveedor->nombre ?? "Proveedor") }}')"
                                    class="p-2 rounded-xl text-rose-500 hover:text-rose-700 hover:bg-rose-50 border border-transparent hover:border-rose-100 transition-colors cursor-pointer"
                                    title="Eliminar Compra">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-inbox text-3xl block mb-2"></i>
                                No has registrado órdenes de compra aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

{{-- MODAL CONFIRMACIÓN ELIMINAR COMPRA VENDEDOR --}}
<div id="modalEliminarCompraVendedor" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg flex-shrink-0">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base">¿Eliminar Compra #<span id="vendedor_eliminar_id_compra_txt"></span>?</h3>
                <p class="text-xs text-slate-500 mt-0.5">Esta acción descontará las existencias que habían ingresado en el inventario.</p>
            </div>
        </div>

        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
            <p><span class="font-semibold text-slate-600">Proveedor:</span> <span id="vendedor_eliminar_proveedor_txt" class="font-bold text-slate-900"></span></p>
            <p><span class="font-semibold text-slate-600">Total Compra:</span> <span id="vendedor_eliminar_total_compra_txt" class="font-black text-rose-600"></span></p>
        </div>

        <form id="formEliminarCompraVendedor" method="POST" action="" class="flex items-center justify-end gap-2 pt-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="cerrarModalEliminarCompraVendedor()"
                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">
                Cancelar
            </button>
            <button type="submit"
                class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-colors">
                Sí, Eliminar y Descontar Stock
            </button>
        </form>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL REGISTRAR COMPRA VENDEDOR (con buscador interactivo de proveedores) --}}
{{-- ========================================================================= --}}
<div id="modalCrearCompraVendedor" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center text-sm">
                    <i class="bi bi-box-arrow-in-down"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Registrar Compra</h3>
            </div>
            <button type="button" onclick="cerrarModalCrearCompraVendedor()" class="text-slate-400 hover:text-slate-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('vendedor.compras.store') }}" method="POST" class="space-y-4" autocomplete="off">
            @csrf

            {{-- Buscador y Selector de Proveedor con Correo --}}
            <div class="space-y-1.5 relative">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700">Proveedor</label>
                    <button type="button" onclick="activarModoNuevoProveedorVendedor()" class="text-[11px] font-bold text-amber-600 hover:text-amber-800 transition-colors cursor-pointer">
                        + Registrar Nuevo Proveedor
                    </button>
                </div>

                {{-- Campo oculto con id_proveedor --}}
                <input type="hidden" name="id_proveedor" id="vendedor_id_proveedor" value="{{ $proveedores->first()->id_proveedor ?? '' }}">

                {{-- Input buscador interactivo con dropdown --}}
                <div id="vendedor_proveedor_selector_container" class="relative">
                    <div class="relative">
                        <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        <input
                            type="text"
                            id="vendedor_buscador_proveedor_input"
                            placeholder="Buscar proveedor por nombre, teléfono o correo..."
                            value="{{ $proveedores->first() ? $proveedores->first()->nombre . ($proveedores->first()->telefono ? ' (' . $proveedores->first()->telefono . ')' : '') : '' }}"
                            autocomplete="off"
                            onfocus="mostrarDropdownProveedoresVendedor()"
                            oninput="filtrarListaProveedoresVendedor(this.value)"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-9 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all cursor-pointer"
                        >
                        <button type="button" onclick="toggleDropdownProveedoresVendedor()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs cursor-pointer">
                            <i class="bi bi-chevron-down" id="vendedor_proveedor_chevron"></i>
                        </button>
                    </div>

                    {{-- Lista desplegable filtrable --}}
                    <div id="vendedor_dropdown_lista_proveedores" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-30 max-h-48 overflow-y-auto hidden divide-y divide-slate-100">
                        <div id="vendedor_items_proveedores">
                            @foreach($proveedores as $prov)
                                <div
                                    class="vendedor-proveedor-item px-3.5 py-2.5 hover:bg-slate-50 cursor-pointer flex items-center justify-between transition-colors text-xs"
                                    data-id="{{ $prov->id_proveedor }}"
                                    data-nombre="{{ strtolower($prov->nombre) }}"
                                    data-telefono="{{ strtolower($prov->telefono ?? '') }}"
                                    data-correo="{{ strtolower($prov->correo ?? '') }}"
                                    onclick="seleccionarProveedorVendedor({{ $prov->id_cliente ?? $prov->id_proveedor }}, '{{ addslashes($prov->nombre) }}', '{{ addslashes($prov->telefono ?? "") }}')"
                                >
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $prov->nombre }}</p>
                                        <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-0.5">
                                            @if($prov->telefono)
                                                <span class="flex items-center gap-1"><i class="bi bi-telephone text-[10px]"></i> {{ $prov->telefono }}</span>
                                            @endif
                                            @if($prov->correo)
                                                <span class="flex items-center gap-1 text-slate-400"><i class="bi bi-envelope text-[10px]"></i> {{ $prov->correo }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-emerald-600 text-sm {{ $loop->first ? '' : 'hidden' }} check-vendedor-prov-icon" id="check_vendedor_prov_{{ $prov->id_proveedor }}">
                                        <i class="bi bi-check2"></i>
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Botón rápido si no existe el proveedor --}}
                        <div id="vendedor_opcion_nuevo_proveedor"
                             class="px-3.5 py-2.5 hover:bg-amber-50 cursor-pointer text-xs font-semibold text-amber-700 flex items-center gap-2 transition-colors border-t border-slate-100 hidden"
                             onclick="activarModoNuevoProveedorVendedor()">
                            <i class="bi bi-building-add text-amber-600"></i>
                            <span>+ Registrar nuevo proveedor con este nombre</span>
                        </div>
                    </div>
                </div>

                {{-- Formulario desplegable para registrar nuevo proveedor (Nombre, Teléfono y Correo) --}}
                <div id="vendedor_box_nuevo_proveedor" class="hidden space-y-2.5 p-3.5 bg-amber-50/60 border border-amber-100 rounded-xl mt-2 animate-in fade-in duration-150">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                            <i class="bi bi-building-add text-amber-600"></i> Nuevo Proveedor a Registrar
                        </span>
                        <button type="button" onclick="cancelarModoNuevoProveedorVendedor()" class="text-[11px] text-slate-500 hover:text-slate-800 cursor-pointer">
                            Elegir de la lista
                        </button>
                    </div>
                    <div class="space-y-2">
                        <input type="text" name="nuevo_proveedor_nombre" id="vendedor_nuevo_proveedor_nombre" placeholder="Nombre completo / Razón social *"
                            class="w-full bg-white border border-amber-200 rounded-lg px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-amber-600">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <input type="text" name="nuevo_proveedor_telefono" id="vendedor_nuevo_proveedor_telefono" placeholder="Teléfono / Contacto (Opcional)"
                                class="w-full bg-white border border-amber-200 rounded-lg px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-amber-600">
                            <input type="email" name="nuevo_proveedor_correo" id="vendedor_nuevo_proveedor_correo" placeholder="Correo electrónico (Opcional)"
                                class="w-full bg-white border border-amber-200 rounded-lg px-3 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-amber-600">
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <label for="vendedor_compra_id_producto" class="block text-xs font-bold text-slate-700">Producto a Abastecer</label>
                <select name="id_producto" id="vendedor_compra_id_producto" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                    <option value="">Selecciona el producto...</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->id_producto }}">
                            {{ $prod->nombre }} - Stock Actual: {{ $prod->stock_actual }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label for="vendedor_compra_cantidad" class="block text-xs font-bold text-slate-700">Cantidad Comprada</label>
                    <input type="number" name="cantidad" id="vendedor_compra_cantidad" min="1" value="1" required
                        oninput="calcularTotalCompraVendedor()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>

                <div class="space-y-1">
                    <label for="vendedor_compra_precio_unitario" class="block text-xs font-bold text-slate-700">Costo Unitario ($)</label>
                    <input type="number" step="0.01" name="precio_unitario" id="vendedor_compra_precio_unitario" min="0.01" required placeholder="0.00"
                        oninput="calcularTotalCompraVendedor()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>
            </div>

            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600">Total a Pagar:</span>
                <span id="vendedor_compra_total_preview" class="text-lg font-black text-slate-900">$0.00</span>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="cerrarModalCrearCompraVendedor()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancelar</button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm">
                    Guardar Compra
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function filtrarCompras() {
        const query = document.getElementById('buscadorCompras').value.toLowerCase();
        const filas = document.querySelectorAll('#tablaCompras tbody tr[data-proveedor]');
        filas.forEach(f => {
            const p = f.dataset.proveedor || '';
            const items = f.dataset.items || '';
            f.style.display = (p.includes(query) || items.includes(query)) ? '' : 'none';
        });
    }

    function abrirModalCrearCompraVendedor() {
        document.getElementById('modalCrearCompraVendedor').classList.remove('hidden');
    }
    function cerrarModalCrearCompraVendedor() {
        document.getElementById('modalCrearCompraVendedor').classList.add('hidden');
        ocultarDropdownProveedoresVendedor();
    }

    function calcularTotalCompraVendedor() {
        const cant = parseFloat(document.getElementById('vendedor_compra_cantidad').value) || 0;
        const prec = parseFloat(document.getElementById('vendedor_compra_precio_unitario').value) || 0;
        const total = cant * prec;
        document.getElementById('vendedor_compra_total_preview').textContent = '$' + total.toLocaleString('es-CO');
    }

    // Funciones del Buscador de Proveedores Vendedor
    function mostrarDropdownProveedoresVendedor() {
        document.getElementById('vendedor_dropdown_lista_proveedores').classList.remove('hidden');
    }
    function ocultarDropdownProveedoresVendedor() {
        document.getElementById('vendedor_dropdown_lista_proveedores').classList.add('hidden');
    }
    function toggleDropdownProveedoresVendedor() {
        const dd = document.getElementById('vendedor_dropdown_lista_proveedores');
        dd.classList.toggle('hidden');
    }

    function filtrarListaProveedoresVendedor(query) {
        mostrarDropdownProveedoresVendedor();
        const q = query.toLowerCase().trim();
        const items = document.querySelectorAll('.vendedor-proveedor-item');
        let visibles = 0;

        items.forEach(item => {
            const nombre = item.dataset.nombre || '';
            const tel = item.dataset.telefono || '';
            const correo = item.dataset.correo || '';
            const match = nombre.includes(q) || tel.includes(q) || correo.includes(q);
            item.style.display = match ? 'flex' : 'none';
            if (match) visibles++;
        });

        const opcNuevo = document.getElementById('vendedor_opcion_nuevo_proveedor');
        if (q.length > 0) {
            opcNuevo.classList.remove('hidden');
        } else {
            opcNuevo.classList.add('hidden');
        }
    }

    function seleccionarProveedorVendedor(id, nombre, telefono) {
        document.getElementById('vendedor_id_proveedor').value = id;
        document.getElementById('vendedor_buscador_proveedor_input').value = nombre + (telefono ? ` (${telefono})` : '');
        
        // Actualizar checks
        document.querySelectorAll('.check-vendedor-prov-icon').forEach(el => el.classList.add('hidden'));
        const check = document.getElementById(`check_vendedor_prov_${id}`);
        if (check) check.classList.remove('hidden');

        ocultarDropdownProveedoresVendedor();
        cancelarModoNuevoProveedorVendedor();
    }

    function activarModoNuevoProveedorVendedor() {
        document.getElementById('vendedor_id_proveedor').value = 'nuevo';
        ocultarDropdownProveedoresVendedor();
        
        const box = document.getElementById('vendedor_box_nuevo_proveedor');
        box.classList.remove('hidden');
        
        const inputBuscador = document.getElementById('vendedor_buscador_proveedor_input');
        const inputNombre = document.getElementById('vendedor_nuevo_proveedor_nombre');
        
        if (inputBuscador.value && !inputBuscador.value.includes('(')) {
            inputNombre.value = inputBuscador.value;
        }
        inputNombre.focus();
    }

    function cancelarModoNuevoProveedorVendedor() {
        document.getElementById('vendedor_box_nuevo_proveedor').classList.add('hidden');
        document.getElementById('vendedor_nuevo_proveedor_nombre').value = '';
        document.getElementById('vendedor_nuevo_proveedor_telefono').value = '';
        const correoInput = document.getElementById('vendedor_nuevo_proveedor_correo');
        if (correoInput) correoInput.value = '';
        
        const actualId = document.getElementById('vendedor_id_proveedor').value;
        if (actualId === 'nuevo') {
            const primerItem = document.querySelector('.vendedor-proveedor-item');
            if (primerItem) {
                primerItem.click();
            }
        }
    }

    // Cerrar dropdown al hacer clic afuera
    document.addEventListener('click', function(e) {
        const container = document.getElementById('vendedor_proveedor_selector_container');
        if (container && !container.contains(e.target)) {
            ocultarDropdownProveedoresVendedor();
        }
    });

    function abrirModalEliminarCompraVendedor(id, total, proveedor) {
        document.getElementById('vendedor_eliminar_id_compra_txt').textContent = id;
        document.getElementById('vendedor_eliminar_proveedor_txt').textContent = proveedor;
        document.getElementById('vendedor_eliminar_total_compra_txt').textContent = '$' + total;
        document.getElementById('formEliminarCompraVendedor').action = `/vendedor/compras/${id}`;
        document.getElementById('modalEliminarCompraVendedor').classList.remove('hidden');
    }

    function cerrarModalEliminarCompraVendedor() {
        document.getElementById('modalEliminarCompraVendedor').classList.add('hidden');
    }
</script>

@endsection
