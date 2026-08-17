@extends('componentes.main')

@section('title', 'Categorías | Inventory System')
@section('page_title', 'Categorías de Productos')
@section('page_subtitle', 'Organización y clasificación del catálogo de inventario')

@section('content')

<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center">
                <i class="bi bi-tags-fill text-lg"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">{{ $categorias->count() }} Categorías Registradas</p>
                <p class="text-xs text-slate-500">Estructura de catálogo</p>
            </div>
        </div>

        <button type="button" onclick="abrirModalCrearCat()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer">
            <i class="bi bi-plus-lg"></i> Nueva Categoría
        </button>
    </div>

    {{-- Tabla de Categorías --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="text-left px-6 py-3.5"># ID</th>
                        <th class="text-left px-6 py-3.5">Nombre de Categoría</th>
                        <th class="text-left px-6 py-3.5">Descripción</th>
                        <th class="text-center px-6 py-3.5">Productos Vinculados</th>
                        <th class="text-right px-6 py-3.5">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($categorias as $categoria)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                #{{ $categoria->id_categoria }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900">{{ $categoria->nombre }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 max-w-sm truncate">
                                {{ $categoria->descripcion ?? 'Sin descripción' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-800 font-semibold border border-slate-200">
                                    <i class="bi bi-boxes text-[10px]"></i>
                                    {{ $categoria->productos_count }} producto(s)
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" title="Editar categoría"
                                        onclick="abrirModalEditarCat({{ $categoria->id_categoria }}, '{{ addslashes($categoria->nombre) }}', '{{ addslashes($categoria->descripcion ?? '') }}')"
                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
                                        <i class="bi bi-pencil text-xs"></i>
                                    </button>
                                    <button type="button" title="Eliminar categoría"
                                        onclick="abrirModalEliminarCat({{ $categoria->id_categoria }}, '{{ addslashes($categoria->nombre) }}', {{ (int) $categoria->productos_count }})"
                                        class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-500 flex items-center justify-center transition-colors cursor-pointer">
                                        <i class="bi bi-trash text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <i class="bi bi-tags text-3xl block mb-2"></i>
                                No hay categorías registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

{{-- MODAL CREAR CATEGORIA --}}
<div id="modalCrearCat" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-base">Nueva Categoría</h3>
            <button type="button" onclick="cerrarModalCrearCat()" class="text-slate-400 hover:text-slate-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.categorias.store') }}" method="POST" class="space-y-4" autocomplete="off">
            @csrf
            <div class="space-y-1">
                <label for="cat_nombre" class="block text-xs font-bold text-slate-700">Nombre</label>
                <input type="text" name="nombre" id="cat_nombre" required placeholder="Ej. Accesorios Gaming"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
            </div>

            <div class="space-y-1">
                <label for="cat_desc" class="block text-xs font-bold text-slate-700">Descripción (Opcional)</label>
                <textarea name="descripcion" id="cat_desc" rows="2" placeholder="Breve descripción de la categoría..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="cerrarModalCrearCat()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR CATEGORIA --}}
<div id="modalEditarCat" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-base">Editar Categoría</h3>
            <button type="button" onclick="cerrarModalEditarCat()" class="text-slate-400 hover:text-slate-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formEditarCat" method="POST" class="space-y-4" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="space-y-1">
                <label for="edit_cat_nombre" class="block text-xs font-bold text-slate-700">Nombre</label>
                <input type="text" name="nombre" id="edit_cat_nombre" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
            </div>

            <div class="space-y-1">
                <label for="edit_cat_desc" class="block text-xs font-bold text-slate-700">Descripción</label>
                <textarea name="descripcion" id="edit_cat_desc" rows="2"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="cerrarModalEditarCat()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm">Actualizar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL ELIMINAR CATEGORIA (INTELIGENTE) --}}
<div id="modalEliminarCat" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-sm w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        
        <div id="eliminar_cat_icon_container" class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mx-auto">
            <i id="eliminar_cat_icon" class="bi bi-shield-exclamation"></i>
        </div>

        <div class="text-center space-y-1.5">
            <h3 id="eliminar_cat_titulo" class="font-bold text-slate-900 text-base">No se puede eliminar esta categoría</h3>
            <p id="eliminar_cat_descripcion" class="text-xs text-slate-500 leading-relaxed"></p>
        </div>

        {{-- Formulario cuando SÍ se puede eliminar --}}
        <form id="formEliminarCat" method="POST" class="pt-2 flex items-center gap-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="cerrarModalEliminarCat()" class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold cursor-pointer">Cancelar</button>
            <button type="submit" class="w-1/2 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold shadow-sm cursor-pointer">Sí, Eliminar</button>
        </form>

        {{-- Botón Entendido cuando NO se puede eliminar --}}
        <div id="eliminar_cat_btn_bloqueado_container" class="pt-2 hidden">
            <button type="button" onclick="cerrarModalEliminarCat()"
                    class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm">
                Entendido
            </button>
        </div>
    </div>
</div>

<script>
    function abrirModalCrearCat() { document.getElementById('modalCrearCat').classList.remove('hidden'); }
    function cerrarModalCrearCat() { document.getElementById('modalCrearCat').classList.add('hidden'); }

    function abrirModalEditarCat(id, nombre, desc) {
        document.getElementById('formEditarCat').action = `/admin/categorias/${id}`;
        document.getElementById('edit_cat_nombre').value = nombre;
        document.getElementById('edit_cat_desc').value = desc;
        document.getElementById('modalEditarCat').classList.remove('hidden');
    }
    function cerrarModalEditarCat() { document.getElementById('modalEditarCat').classList.add('hidden'); }

    function abrirModalEliminarCat(id, nombre, totalProductos) {
        const form = document.getElementById('formEliminarCat');
        const btnBloqueado = document.getElementById('eliminar_cat_btn_bloqueado_container');
        const iconContainer = document.getElementById('eliminar_cat_icon_container');
        const icon = document.getElementById('eliminar_cat_icon');
        const titulo = document.getElementById('eliminar_cat_titulo');
        const descripcion = document.getElementById('eliminar_cat_descripcion');

        form.action = `/admin/categorias/${id}`;

        if (totalProductos > 0) {
            // Bloqueado por integridad referencial
            iconContainer.className = "w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mx-auto";
            icon.className = "bi bi-shield-exclamation";
            titulo.textContent = "No se puede eliminar esta categoría";
            descripcion.innerHTML = `La categoría <strong class="text-slate-800">${nombre}</strong> tiene <strong class="text-slate-800">${totalProductos} producto(s)</strong> vinculados en el catálogo. Por seguridad no es posible eliminarla; primero debes reasignar o eliminar sus productos.`;

            form.classList.add('hidden');
            btnBloqueado.classList.remove('hidden');
        } else {
            // Permitido eliminar
            iconContainer.className = "w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl mx-auto";
            icon.className = "bi bi-trash3-fill";
            titulo.textContent = "¿Eliminar Categoría?";
            descripcion.innerHTML = `¿Estás seguro de que deseas eliminar permanentemente la categoría <strong class="text-slate-800">${nombre}</strong>? Esta acción no se puede deshacer.`;

            form.classList.remove('hidden');
            btnBloqueado.classList.add('hidden');
        }

        document.getElementById('modalEliminarCat').classList.remove('hidden');
    }
    function cerrarModalEliminarCat() { document.getElementById('modalEliminarCat').classList.add('hidden'); }
</script>

@endsection
