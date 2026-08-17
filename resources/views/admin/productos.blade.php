@extends('componentes.main')

@section('title', 'Catálogo de Productos | Inventory System')
@section('page_title', 'Productos e Inventario')
@section('page_subtitle', 'Catálogo visual de artículos, precios, existencias e imágenes')

@section('content')

<div class="space-y-6">

    {{-- Encabezado con buscador, filtros de categoría y estado, y botón nuevo --}}
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                <span>Catálogo de Productos</span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700" id="contadorProductos">
                    {{ $productos->count() }} artículos
                </span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Control y filtrado de artículos activos e inactivos</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            {{-- Buscador de Texto --}}
            <div class="relative flex-1 min-w-[200px]">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input
                    type="text"
                    id="buscadorProductos"
                    placeholder="Buscar por artículo o marca..."
                    class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-900 focus:bg-white transition-all shadow-sm"
                    onkeyup="aplicarFiltros()"
                >
            </div>

            {{-- Filtro por Categoría --}}
            <div class="min-w-[160px] flex-1 sm:flex-initial">
                <select id="filtroCategoria" onchange="aplicarFiltros()"
                    class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 font-medium focus:outline-none focus:border-slate-900 transition-all shadow-sm cursor-pointer">
                    <option value="todas">Todas las Categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ strtolower($cat->nombre) }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por Estado (Activos / Inactivos) --}}
            <div class="min-w-[150px] flex-1 sm:flex-initial">
                <select id="filtroEstado" onchange="aplicarFiltros()"
                    class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 font-medium focus:outline-none focus:border-slate-900 transition-all shadow-sm cursor-pointer">
                    <option value="todos">Todos los Estados</option>
                    <option value="activo">Solo Activos</option>
                    <option value="inactivo">Solo Desactivados</option>
                </select>
            </div>

            {{-- Botón Nuevo Producto --}}
            <button type="button" onclick="abrirModalCrearProd()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer flex-shrink-0">
                <i class="bi bi-plus-lg"></i> Nuevo Producto
            </button>
        </div>
    </div>

    {{-- Cuadrícula de Productos (Cards / Cuadros) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="gridProductos">
        @forelse ($productos as $producto)
            <div class="card-producto group bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 flex flex-col overflow-hidden {{ !$producto->activo ? 'opacity-70 bg-slate-50/60 border-dashed' : '' }}"
                 data-nombre="{{ strtolower($producto->nombre) }}"
                 data-marca="{{ strtolower($producto->marca ?? '') }}"
                 data-categoria="{{ strtolower($producto->categoria->nombre ?? '') }}"
                 data-estado="{{ $producto->activo ? 'activo' : 'inactivo' }}">

                {{-- Contenedor de Imagen de Altura Fija Amplia y Uniforme (290px) --}}
                <div class="w-full relative overflow-hidden bg-slate-100 border-b border-slate-100 flex-shrink-0" style="height: 290px; min-height: 290px; max-height: 290px;">
                    @if($producto->imagen && file_exists(public_path('uploads/productos/' . $producto->imagen)))
                        <img src="{{ asset('uploads/productos/' . $producto->imagen) }}"
                             alt="{{ $producto->nombre }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             style="height: 290px; width: 100%; object-fit: cover;">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50" style="height: 290px;">
                            <i class="bi bi-image text-5xl mb-2 text-slate-300"></i>
                            <span class="text-xs font-semibold text-slate-400">Sin foto</span>
                        </div>
                    @endif
                </div>

                {{-- Cuerpo de la Tarjeta (Todos los datos debajo de la foto) --}}
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        {{-- Fila de Insignias: Categoría, Estado de Stock y Estado Activo/Inactivo --}}
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 truncate max-w-[120px]">
                                {{ $producto->categoria->nombre ?? 'General' }}
                            </span>

                            <div class="flex items-center gap-1.5">
                                @if(!$producto->activo)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 flex-shrink-0" title="Oculto para vendedores">
                                        Desactivado
                                    </span>
                                @elseif($producto->stock_actual <= 0)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 flex-shrink-0">
                                        Agotado
                                    </span>
                                @elseif($producto->stock_actual <= $producto->stock_minimo)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 flex-shrink-0">
                                        Bajo ({{ $producto->stock_actual }})
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex-shrink-0">
                                        {{ $producto->stock_actual }} disp.
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Marca --}}
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            {{ $producto->marca ?? 'Genérica' }}
                        </p>

                        {{-- Nombre del Producto --}}
                        <h4 class="text-sm font-bold text-slate-900 mt-1 leading-snug line-clamp-2" title="{{ $producto->nombre }}">
                            {{ $producto->nombre }}
                        </h4>

                        {{-- Descripción breve --}}
                        @if($producto->descripcion)
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                {{ $producto->descripcion }}
                            </p>
                        @endif
                    </div>

                    {{-- Precio Grande y Existencias --}}
                    <div class="pt-2 border-t border-slate-100 flex items-end justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Precio de Venta</span>
                            <p class="text-xl font-black text-slate-900">
                                ${{ number_format($producto->precio_venta, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-semibold text-slate-500">
                                <i class="bi bi-box-seam text-slate-400"></i> {{ $producto->stock_actual }} unid.
                            </span>
                        </div>
                    </div>

                    {{-- Botones de Acción (Editar, Desactivar/Activar y Eliminar) --}}
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                        {{-- Botón Editar --}}
                        <button type="button"
                            onclick="abrirModalEditarProd({{ $producto->id_producto }}, '{{ addslashes($producto->nombre) }}', {{ $producto->id_categoria }}, '{{ addslashes($producto->marca ?? '') }}', {{ $producto->stock_actual }}, {{ $producto->stock_minimo }}, {{ $producto->precio_venta }}, '{{ addslashes($producto->descripcion ?? '') }}', '{{ $producto->imagen ? asset('uploads/productos/' . $producto->imagen) : '' }}')"
                            class="flex-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="bi bi-pencil"></i> Editar
                        </button>

                        {{-- Botón Desactivar / Activar --}}
                        @if($producto->activo)
                            <button type="button"
                                onclick="abrirModalToggleProd({{ $producto->id_producto }}, '{{ addslashes($producto->nombre) }}', true)"
                                class="py-2 px-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                                title="Desactivar producto (ocultar a vendedores sin borrar historial)">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        @else
                            <button type="button"
                                onclick="abrirModalToggleProd({{ $producto->id_producto }}, '{{ addslashes($producto->nombre) }}', false)"
                                class="py-2 px-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                                title="Activar producto (mostrar de nuevo en el catálogo de vendedores)">
                                <i class="bi bi-eye"></i>
                            </button>
                        @endif

                        {{-- Botón Eliminar --}}
                        <button type="button"
                            onclick="abrirModalEliminarProd({{ $producto->id_producto }}, '{{ addslashes($producto->nombre) }}')"
                            class="py-2 px-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold transition-colors flex items-center justify-center gap-1 cursor-pointer"
                            title="Eliminar permanentemente">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white border border-slate-200 rounded-2xl p-8">
                <i class="bi bi-box-seam text-4xl text-slate-300 block mb-2"></i>
                <p class="text-sm font-bold text-slate-700">No hay productos registrados</p>
                <p class="text-xs text-slate-400 mt-1">Crea tu primer producto con el botón "Nuevo Producto".</p>
            </div>
        @endforelse
    </div>

    {{-- Mensaje cuando no hay resultados de búsqueda/filtro --}}
    <div id="sinResultadosFiltro" class="hidden py-16 text-center bg-white border border-slate-200 rounded-2xl p-8">
        <i class="bi bi-funnel text-4xl text-slate-300 block mb-2"></i>
        <p class="text-sm font-bold text-slate-700">No se encontraron productos con los filtros seleccionados</p>
        <p class="text-xs text-slate-400 mt-1">Intenta cambiar la categoría, el estado o el término de búsqueda.</p>
    </div>

</div>

{{-- ========================================================================= --}}
{{-- MODAL CREAR PRODUCTO --}}
{{-- ========================================================================= --}}
<div id="modalCrearProd" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-base">Nuevo Producto</h3>
            <button type="button" onclick="cerrarModalCrearProd()" class="text-slate-400 hover:text-slate-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.productos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5" autocomplete="off">
            @csrf

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Imagen del Producto (Opcional)</label>
                <div class="flex items-center gap-4">
                    <div id="preview_create_box" class="w-16 h-16 rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0 text-slate-400">
                        <i class="bi bi-image text-xl" id="preview_create_icon"></i>
                        <img id="preview_create_img" class="w-full h-full object-cover hidden" alt="Preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="imagen" id="imagen_create" accept="image/*" onchange="previewImage(this, 'preview_create_img', 'preview_create_icon')"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Formatos: JPG, PNG, WEBP. Máximo 2MB.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Nombre del Producto</label>
                <input type="text" name="nombre" required placeholder="Ej. Teclado Organeta Piano Musical"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Categoría</label>
                    <select name="id_categoria" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                        <option value="">Seleccione categoría...</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Marca</label>
                    <input type="text" name="marca" placeholder="Ej. Logitech, Sony, etc."
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Stock Inicial</label>
                    <input type="number" name="stock_actual" min="0" value="0" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Stock Mínimo</label>
                    <input type="number" name="stock_minimo" min="0" value="5" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Precio Venta ($)</label>
                    <input type="number" step="0.01" name="precio_venta" min="0" required placeholder="0.00"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Descripción (Opcional)</label>
                <textarea name="descripcion" rows="2" placeholder="Detalles o especificaciones del producto..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="cerrarModalCrearProd()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm">Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL EDITAR PRODUCTO --}}
{{-- ========================================================================= --}}
<div id="modalEditarProd" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-base">Editar Producto</h3>
            <button type="button" onclick="cerrarModalEditarProd()" class="text-slate-400 hover:text-slate-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formEditarProd" method="POST" enctype="multipart/form-data" class="space-y-3.5" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Imagen del Producto (Opcional)</label>
                <div class="flex items-center gap-4">
                    <div id="preview_edit_box" class="w-16 h-16 rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0 text-slate-400">
                        <i class="bi bi-image text-xl" id="preview_edit_icon"></i>
                        <img id="preview_edit_img" class="w-full h-full object-cover hidden" alt="Preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="imagen" id="imagen_edit" accept="image/*" onchange="previewImage(this, 'preview_edit_img', 'preview_edit_icon')"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Selecciona una nueva imagen solo si deseas reemplazarla.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Nombre del Producto</label>
                <input type="text" name="nombre" id="edit_prod_nombre" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Categoría</label>
                    <select name="id_categoria" id="edit_prod_categoria" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Marca</label>
                    <input type="text" name="marca" id="edit_prod_marca"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Stock Actual</label>
                    <input type="number" name="stock_actual" id="edit_prod_stock" min="0" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Stock Mínimo</label>
                    <input type="number" name="stock_minimo" id="edit_prod_min" min="0" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Precio Venta ($)</label>
                    <input type="number" step="0.01" name="precio_venta" id="edit_prod_precio" min="0" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700">Descripción</label>
                <textarea name="descripcion" id="edit_prod_desc" rows="2"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="cerrarModalEditarProd()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm">Actualizar Producto</button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL DESACTIVAR / ACTIVAR PRODUCTO --}}
{{-- ========================================================================= --}}
<div id="modalToggleProd" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-sm w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div id="toggle_prod_icon_container" class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl mx-auto">
            <i id="toggle_prod_icon" class="bi"></i>
        </div>

        <div class="text-center space-y-1">
            <h3 id="toggle_prod_titulo" class="font-bold text-slate-900 text-base"></h3>
            <p id="toggle_prod_descripcion" class="text-xs text-slate-500 leading-relaxed"></p>
        </div>

        <form id="formToggleProd" method="POST" class="pt-2 flex items-center gap-2">
            @csrf
            @method('PATCH')
            <button type="button" onclick="cerrarModalToggleProd()"
                class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors cursor-pointer">
                Cancelar
            </button>
            <button type="submit" id="toggle_prod_btn_confirm"
                class="w-1/2 py-2.5 rounded-xl text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm">
            </button>
        </form>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL ELIMINAR PRODUCTO --}}
{{-- ========================================================================= --}}
<div id="modalEliminarProd" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-sm w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-xl mx-auto">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>

        <div class="text-center space-y-1">
            <h3 class="font-bold text-slate-900 text-base">¿Eliminar Producto?</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Estás a punto de eliminar <strong id="delete_prod_nombre" class="text-slate-800"></strong> permanentemente del inventario.
            </p>
        </div>

        <form id="formEliminarProd" method="POST" class="pt-2 flex items-center gap-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="cerrarModalEliminarProd()" class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancelar</button>
            <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-sm">Sí, Eliminar</button>
        </form>
    </div>
</div>

<script>
    function aplicarFiltros() {
        const texto = document.getElementById('buscadorProductos').value.toLowerCase().trim();
        const categoria = document.getElementById('filtroCategoria').value.toLowerCase();
        const estado = document.getElementById('filtroEstado').value.toLowerCase();

        const tarjetas = document.querySelectorAll('.card-producto');
        let visibles = 0;

        tarjetas.forEach(card => {
            const cardNombre = card.dataset.nombre || '';
            const cardMarca = card.dataset.marca || '';
            const cardCat = card.dataset.categoria || '';
            const cardEstado = card.dataset.estado || '';

            // Filtro de texto
            const matchTexto = !texto || cardNombre.includes(texto) || cardMarca.includes(texto) || cardCat.includes(texto);
            // Filtro de categoría
            const matchCat = (categoria === 'todas') || (cardCat === categoria);
            // Filtro de estado
            let matchEstado = true;
            if (estado === 'activo') {
                matchEstado = (cardEstado === 'activo');
            } else if (estado === 'inactivo') {
                matchEstado = (cardEstado === 'inactivo');
            }

            if (matchTexto && matchCat && matchEstado) {
                card.style.display = '';
                visibles++;
            } else {
                card.style.display = 'none';
            }
        });

        // Actualizar contador
        const contador = document.getElementById('contadorProductos');
        if (contador) {
            contador.textContent = `${visibles} artículos`;
        }

        // Mostrar / ocultar mensaje de sin resultados
        const sinResultados = document.getElementById('sinResultadosFiltro');
        if (sinResultados) {
            if (visibles === 0 && tarjetas.length > 0) {
                sinResultados.classList.remove('hidden');
            } else {
                sinResultados.classList.add('hidden');
            }
        }
    }

    function previewImage(input, imgId, iconId) {
        const img = document.getElementById(imgId);
        const icon = document.getElementById(iconId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                img.classList.remove('hidden');
                icon.classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function abrirModalCrearProd() {
        document.getElementById('imagen_create').value = '';
        document.getElementById('preview_create_img').src = '';
        document.getElementById('preview_create_img').classList.add('hidden');
        document.getElementById('preview_create_icon').classList.remove('hidden');
        document.getElementById('modalCrearProd').classList.remove('hidden');
    }
    function cerrarModalCrearProd() { document.getElementById('modalCrearProd').classList.add('hidden'); }

    function abrirModalEditarProd(id, nombre, idCat, marca, stock, min, precio, desc, imgUrl) {
        document.getElementById('formEditarProd').action = `/admin/productos/${id}`;
        document.getElementById('edit_prod_nombre').value = nombre;
        document.getElementById('edit_prod_categoria').value = idCat;
        document.getElementById('edit_prod_marca').value = marca;
        document.getElementById('edit_prod_stock').value = stock;
        document.getElementById('edit_prod_min').value = min;
        document.getElementById('edit_prod_precio').value = precio;
        document.getElementById('edit_prod_desc').value = desc;
        document.getElementById('imagen_edit').value = '';

        const editImg = document.getElementById('preview_edit_img');
        const editIcon = document.getElementById('preview_edit_icon');
        if (imgUrl) {
            editImg.src = imgUrl;
            editImg.classList.remove('hidden');
            editIcon.classList.add('hidden');
        } else {
            editImg.src = '';
            editImg.classList.add('hidden');
            editIcon.classList.remove('hidden');
        }

        document.getElementById('modalEditarProd').classList.remove('hidden');
    }
    function cerrarModalEditarProd() { document.getElementById('modalEditarProd').classList.add('hidden'); }

    function abrirModalToggleProd(id, nombre, esActivoActualmente) {
        const form = document.getElementById('formToggleProd');
        form.action = `/admin/productos/${id}/toggle`;

        const iconContainer = document.getElementById('toggle_prod_icon_container');
        const icon = document.getElementById('toggle_prod_icon');
        const titulo = document.getElementById('toggle_prod_titulo');
        const descripcion = document.getElementById('toggle_prod_descripcion');
        const btn = document.getElementById('toggle_prod_btn_confirm');

        if (esActivoActualmente) {
            // Desactivar
            iconContainer.className = "w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mx-auto";
            icon.className = "bi bi-eye-slash";
            titulo.textContent = "¿Desactivar Producto?";
            descripcion.innerHTML = `Estás a punto de desactivar <strong class="text-slate-800">${nombre}</strong>. El producto se mantendrá en tu historial pero ya no aparecerá en el catálogo del vendedor.`;
            btn.className = "w-1/2 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm";
            btn.textContent = "Sí, Desactivar";
        } else {
            // Activar
            iconContainer.className = "w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mx-auto";
            icon.className = "bi bi-eye";
            titulo.textContent = "¿Activar Producto?";
            descripcion.innerHTML = `Estás a punto de reactivar <strong class="text-slate-800">${nombre}</strong> para que vuelva a estar visible en el catálogo de ventas.`;
            btn.className = "w-1/2 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm";
            btn.textContent = "Sí, Activar";
        }

        document.getElementById('modalToggleProd').classList.remove('hidden');
    }
    function cerrarModalToggleProd() { document.getElementById('modalToggleProd').classList.add('hidden'); }

    function abrirModalEliminarProd(id, nombre) {
        document.getElementById('formEliminarProd').action = `/admin/productos/${id}`;
        document.getElementById('delete_prod_nombre').textContent = nombre;
        document.getElementById('modalEliminarProd').classList.remove('hidden');
    }
    function cerrarModalEliminarProd() { document.getElementById('modalEliminarProd').classList.add('hidden'); }
</script>

@endsection
