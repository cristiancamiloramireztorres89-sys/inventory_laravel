{{-- ========================================================================= --}}
{{-- MODAL REGISTRAR VENTA MULTI-PRODUCTO (ADMIN) --}}
{{-- ========================================================================= --}}
<div id="modalCrearVenta" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-xl w-full flex flex-col max-h-[90vh] animate-in fade-in zoom-in-95 duration-150 overflow-hidden">
        {{-- HEADER FIJO --}}
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 flex-shrink-0 bg-white">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm shadow-sm">
                    <i class="bi bi-cart-check-fill"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base leading-tight">Registrar Nueva Venta</h3>
                    <p class="text-[11px] text-slate-400">Punto de Venta Directo</p>
                </div>
            </div>
            <button type="button" onclick="cerrarModalCrearVenta()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center text-base cursor-pointer transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- FORMULARIO CON BODY SCROLLABLE Y FOOTER FIJO --}}
        <form action="{{ route('admin.ventas.store') }}" method="POST" id="formCrearVentaAdmin" class="flex flex-col flex-1 overflow-hidden" autocomplete="off" onsubmit="return validarEnvioVentaAdmin()">
            @csrf

            {{-- BODY SCROLLABLE --}}
            <div class="px-5 py-3.5 space-y-3 overflow-y-auto flex-1">
                
                {{-- Selector de Cliente --}}
                <div class="space-y-1 relative">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700">Cliente</label>
                        <button type="button" onclick="activarModoNuevoClienteAdmin()" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors cursor-pointer">
                            + Registrar Nuevo Cliente
                        </button>
                    </div>

                    <input type="hidden" name="id_cliente" id="admin_id_cliente" value="{{ $clientes->first()->id_cliente ?? '' }}">

                    <div id="admin_cliente_selector_container" class="relative">
                        <div class="relative flex items-center">
                            <i class="bi bi-search text-slate-400 text-xs pointer-events-none" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);"></i>
                            <input
                                type="text"
                                id="admin_buscador_cliente_input"
                                placeholder="Buscar cliente por nombre, teléfono o correo..."
                                value="{{ $clientes->first() ? $clientes->first()->nombre . ($clientes->first()->telefono ? ' (' . $clientes->first()->telefono . ')' : '') : '' }}"
                                autocomplete="off"
                                onfocus="mostrarDropdownClientesAdmin()"
                                oninput="filtrarListaClientesAdmin(this.value)"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all cursor-pointer"
                                style="padding-left: 36px; padding-right: 36px;"
                            >
                            <button type="button" onclick="toggleDropdownClientesAdmin()" class="text-slate-400 hover:text-slate-600 text-xs cursor-pointer" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);">
                                <i class="bi bi-chevron-down" id="admin_cliente_chevron"></i>
                            </button>
                        </div>

                        {{-- Dropdown clientes --}}
                        <div id="admin_dropdown_lista_clientes" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-40 overflow-y-auto hidden divide-y divide-slate-100">
                            <div id="admin_items_clientes">
                                @foreach($clientes as $cli)
                                    <div
                                        class="admin-cliente-item px-3.5 py-2.5 hover:bg-slate-50 cursor-pointer flex items-center justify-between transition-colors text-xs"
                                        data-id="{{ $cli->id_cliente }}"
                                        data-nombre="{{ strtolower($cli->nombre) }}"
                                        data-telefono="{{ strtolower($cli->telefono ?? '') }}"
                                        data-correo="{{ strtolower($cli->correo ?? '') }}"
                                        onclick="seleccionarClienteAdmin({{ $cli->id_cliente }}, '{{ addslashes($cli->nombre) }}', '{{ addslashes($cli->telefono ?? '') }}')"
                                    >
                                        <div>
                                            <p class="font-bold text-slate-900">{{ $cli->nombre }}</p>
                                            <div class="flex items-center gap-2 text-[10px] text-slate-500">
                                                @if($cli->telefono) <span>{{ $cli->telefono }}</span> @endif
                                                @if($cli->correo) <span class="text-slate-400">{{ $cli->correo }}</span> @endif
                                            </div>
                                        </div>
                                        <span class="text-emerald-600 text-sm {{ $loop->first ? '' : 'hidden' }} check-admin-icon" id="check_admin_{{ $cli->id_cliente }}">
                                            <i class="bi bi-check2"></i>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <div id="admin_opcion_nuevo_cliente"
                                 class="px-3.5 py-2.5 hover:bg-indigo-50 cursor-pointer text-xs font-semibold text-indigo-700 flex items-center gap-2 transition-colors border-t border-slate-100 hidden"
                                 onclick="activarModoNuevoClienteAdmin()">
                                <i class="bi bi-person-plus-fill text-indigo-600"></i>
                                <span>+ Registrar nuevo cliente</span>
                            </div>
                        </div>
                    </div>

                    {{-- Formulario para registrar nuevo cliente --}}
                    <div id="admin_box_nuevo_cliente" class="hidden space-y-2 p-3 bg-indigo-50/70 border border-indigo-100 rounded-xl mt-1.5 animate-in fade-in duration-150">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-indigo-900 flex items-center gap-1">
                                <i class="bi bi-person-plus-fill text-indigo-600"></i> Nuevo Cliente
                            </span>
                            <button type="button" onclick="cancelarModoNuevoClienteAdmin()" class="text-[10px] text-slate-500 hover:text-slate-800 cursor-pointer">
                                Cancelar
                            </button>
                        </div>
                        <input type="text" name="nuevo_cliente_nombre" id="admin_nuevo_cliente_nombre" placeholder="Nombre completo *"
                            class="w-full bg-white border border-indigo-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="nuevo_cliente_telefono" id="admin_nuevo_cliente_telefono" placeholder="Teléfono"
                                class="w-full bg-white border border-indigo-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600">
                            <input type="email" name="nuevo_cliente_correo" id="admin_nuevo_cliente_correo" placeholder="Correo"
                                class="w-full bg-white border border-indigo-200 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600">
                        </div>
                    </div>
                </div>

                {{-- Barra Compacta de Búsqueda y Adición de Producto --}}
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-bold text-slate-700">Agregar Producto</label>
                        <span id="admin_stock_badge" class="text-[10px] font-bold text-slate-500">Stock: Selecciona producto</span>
                    </div>

                    <div class="flex items-center gap-2.5">
                        {{-- Buscador interactivo --}}
                        <div class="relative flex-1" id="admin_producto_selector_container">
                            <input type="hidden" id="admin_pos_id_producto_seleccionado" value="">
                            <input type="hidden" id="admin_pos_precio_seleccionado" value="0">
                            <input type="hidden" id="admin_pos_stock_seleccionado" value="0">
                            <input type="hidden" id="admin_pos_nombre_seleccionado" value="">

                            <div class="relative flex items-center">
                                <i class="bi bi-search text-slate-400 text-xs pointer-events-none" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);"></i>
                                <input
                                    type="text"
                                    id="admin_pos_buscador_producto"
                                    placeholder="Buscar producto por nombre..."
                                    autocomplete="off"
                                    onfocus="mostrarDropdownProductosAdmin()"
                                    oninput="filtrarListaProductosAdmin(this.value)"
                                    onkeydown="handleKeydownProductoAdmin(event)"
                                    class="w-full bg-white border border-slate-200 rounded-xl py-2 text-xs text-slate-900 focus:outline-none focus:border-slate-900 transition-all cursor-pointer placeholder-slate-400"
                                    style="padding-left: 36px; padding-right: 36px;"
                                >
                                <button
                                    type="button"
                                    id="admin_btn_limpiar_producto"
                                    onclick="limpiarSeleccionProductoAdmin()"
                                    class="hidden text-slate-400 hover:text-rose-500 text-sm cursor-pointer transition-colors"
                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); z-index: 20; background: none; border: none; padding: 0; line-height: 1;"
                                    title="Limpiar selección"
                                >
                                    <i class="bi bi-x-circle-fill"></i>
                                </button>
                            </div>

                            {{-- Dropdown de productos --}}
                            <div id="admin_dropdown_lista_productos" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-50 max-h-48 overflow-y-auto hidden divide-y divide-slate-100">
                                <div id="admin_items_productos">
                                    @foreach($productos as $prod)
                                        <div
                                            class="admin-prod-item px-3 py-2 hover:bg-slate-50 cursor-pointer flex items-center justify-between transition-colors text-xs {{ $prod->stock_actual <= 0 ? 'opacity-50 pointer-events-none bg-slate-50/50' : '' }}"
                                            data-id="{{ $prod->id_producto }}"
                                            data-nombre="{{ strtolower($prod->nombre) }}"
                                            data-marca="{{ strtolower($prod->marca ?? '') }}"
                                            data-categoria="{{ strtolower($prod->categoria->nombre ?? '') }}"
                                            data-precio="{{ $prod->precio_venta }}"
                                            data-stock="{{ $prod->stock_actual }}"
                                            onclick="seleccionarProductoAdmin({{ $prod->id_producto }}, '{{ addslashes($prod->nombre) }}', {{ $prod->precio_venta }}, {{ $prod->stock_actual }})"
                                        >
                                            <div class="min-w-0 pr-2">
                                                <p class="font-bold text-slate-900 truncate">{{ $prod->nombre }}</p>
                                                <p class="text-[10px] text-slate-400 truncate">{{ $prod->categoria->nombre ?? 'Sin categoría' }}</p>
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <p class="font-black text-slate-900">${{ number_format($prod->precio_venta, 0, ',', '.') }}</p>
                                                <span class="text-[10px] font-bold {{ $prod->stock_actual > 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                                                    {{ $prod->stock_actual > 0 ? $prod->stock_actual . ' disp.' : 'Sin stock' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="admin_prod_no_results" class="px-3.5 py-3 text-center text-xs text-slate-400 hidden">
                                    <i class="bi bi-search text-sm block mb-0.5"></i> No se encontraron productos.
                                </div>
                            </div>
                        </div>

                        {{-- Cantidad --}}
                        <div class="w-20 flex-shrink-0">
                            <input type="number" id="admin_pos_cantidad" min="1" value="1" placeholder="Cant."
                                onkeydown="if(event.key === 'Enter'){ event.preventDefault(); agregarProductoAlCarritoAdmin(); }"
                                class="w-full bg-white border border-slate-200 rounded-xl px-2 py-2 text-xs text-slate-900 focus:outline-none focus:border-slate-900 text-center font-bold [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        </div>

                        {{-- Botón Agregar --}}
                        <button type="button" onclick="agregarProductoAlCarritoAdmin()"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors cursor-pointer flex-shrink-0">
                            <i class="bi bi-plus-lg font-black"></i> Agregar
                        </button>
                    </div>
                </div>

                {{-- LISTA DE ARTÍCULOS EN EL CARRITO --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700">Artículos en la Venta</label>
                        <span id="admin_resumen_items_count" class="text-[11px] font-semibold text-slate-400">0 productos</span>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                        <div class="max-h-40 overflow-y-auto relative">
                            <table class="w-full text-xs text-left" id="admin_tabla_carrito">
                                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-3 py-2 bg-slate-100">Producto</th>
                                        <th class="px-2 py-2 text-center w-28 bg-slate-100">Cant.</th>
                                        <th class="px-2 py-2 text-right bg-slate-100">Unitario</th>
                                        <th class="px-3 py-2 text-right bg-slate-100">Subtotal</th>
                                        <th class="px-2 py-2 text-center w-8 bg-slate-100"></th>
                                    </tr>
                                </thead>
                                <tbody id="admin_carrito_tbody" class="divide-y divide-slate-100">
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                            <i class="bi bi-cart-x text-2xl block mb-1 text-slate-300"></i>
                                            Aún no has agregado productos a esta venta.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="admin_hidden_items_inputs"></div>
            </div>

            {{-- FOOTER FIJO (SIEMPRE VISIBLE) --}}
            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between flex-shrink-0 rounded-b-2xl">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 block">Total a Cobrar:</span>
                    <span id="admin_venta_total_preview" class="text-xl font-black text-emerald-700 leading-none">$0.00</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="cerrarModalCrearVenta()"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-white text-xs font-semibold cursor-pointer transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" id="admin_btn_confirmar_venta"
                        class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        Confirmar y Cobrar Venta
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
