@extends('componentes.main')

@section('title', 'Categorías | Inventory System')
@section('page_title', 'Categorías de Productos')
@section('page_subtitle', 'Organización y clasificación del catálogo de inventario')

@section('content')

<div class="space-y-6">

    {{-- Barra Superior: Resumen, Buscador y Botón Crear --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <i class="bi bi-tags-fill text-xl text-emerald-600"></i>
                <h3 class="text-sm font-bold text-slate-900" id="contadorCategorias">{{ $categorias->count() }} Categorías Registradas</h3>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Estructura y clasificación del catálogo de inventario</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            {{-- Buscador de Categorías --}}
            <div class="relative flex-1 sm:w-64 md:w-72">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input
                    type="text"
                    id="buscadorCategorias"
                    placeholder="Buscar por categoría o descripción..."
                    class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-900 focus:bg-white transition-all shadow-sm"
                    onkeyup="aplicarFiltroCategorias()"
                >
            </div>

            {{-- Botón Nueva Categoría --}}
            <button type="button" onclick="abrirModalCrearCat()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer flex-shrink-0">
                <i class="bi bi-plus-lg"></i> Nueva Categoría
            </button>
        </div>
    </div>

    {{-- Cuadrícula de Categorías (Cards / Cuadros estilo Productos) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="gridCategorias">
        @forelse ($categorias as $categoria)
            @php
                $catImgUrl = ($categoria->imagen && file_exists(public_path('uploads/categorias/' . $categoria->imagen)))
                    ? asset('uploads/categorias/' . $categoria->imagen)
                    : '';
            @endphp
            <div class="card-categoria group bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 flex flex-col overflow-hidden"
                 data-nombre="{{ strtolower($categoria->nombre) }}"
                 data-descripcion="{{ strtolower($categoria->descripcion ?? '') }}">

                {{-- Contenedor de Imagen de Altura Fija Amplia y Uniforme (estilo Productos 280px) --}}
                <div class="w-full relative overflow-hidden bg-slate-100 border-b border-slate-100 flex-shrink-0" style="height: 280px; min-height: 280px; max-height: 280px;">
                    @if($catImgUrl)
                        <img src="{{ $catImgUrl }}"
                             alt="{{ $categoria->nombre }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             style="height: 280px; width: 100%; object-fit: cover;">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50" style="height: 280px;">
                            <i class="bi bi-image text-5xl mb-2 text-slate-300"></i>
                            <span class="text-xs font-semibold text-slate-400">Sin foto</span>
                        </div>
                    @endif
                </div>

                {{-- Cuerpo de la Tarjeta (Todos los datos debajo de la foto) --}}
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        {{-- Tag de tipo --}}
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            CATEGORÍA
                        </p>

                        {{-- Nombre de la Categoría --}}
                        <h4 class="text-sm font-bold text-slate-900 mt-1 leading-snug line-clamp-1" title="{{ $categoria->nombre }}">
                            {{ $categoria->nombre }}
                        </h4>

                        {{-- Descripción breve --}}
                        @if($categoria->descripcion)
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                {{ $categoria->descripcion }}
                            </p>
                        @else
                            <p class="text-xs text-slate-400 italic mt-1">
                                Sin descripción
                            </p>
                        @endif
                    </div>

                    {{-- Productos Vinculados --}}
                    <div class="pt-2 border-t border-slate-100 flex items-end justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Productos</span>
                            <p class="text-xl font-black text-slate-900">
                                {{ $categoria->productos_count }}
                            </p>
                        </div>
                    </div>

                    {{-- Botones de Acción (Editar y Eliminar) con el estilo exacto de productos --}}
                    <div class="pt-3 border-t border-slate-100 flex items-center gap-1">
                        {{-- Botón Editar --}}
                        <button type="button"
                            onclick="abrirModalEditarCat({{ $categoria->id_categoria }}, '{{ addslashes($categoria->nombre) }}', '{{ addslashes($categoria->descripcion ?? '') }}', '{{ $catImgUrl }}')"
                            class="flex-1 py-1.5 px-3 text-slate-700 hover:text-slate-900 hover:bg-slate-100 rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="bi bi-pencil text-slate-400"></i> Editar
                        </button>

                        {{-- Botón Eliminar --}}
                        <button type="button"
                            onclick="abrirModalEliminarCat({{ $categoria->id_categoria }}, '{{ addslashes($categoria->nombre) }}', {{ (int) $categoria->productos_count }})"
                            class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg text-sm transition-colors cursor-pointer"
                            title="Eliminar categoría">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white border border-slate-200 rounded-2xl">
                <i class="bi bi-tags text-4xl block mb-3 text-slate-300"></i>
                <p class="text-sm font-semibold text-slate-700">No hay categorías registradas</p>
                <p class="text-xs text-slate-400 mt-1">Crea tu primera categoría para organizar el inventario.</p>
            </div>
        @endforelse

        {{-- Estado sin resultados de búsqueda --}}
        <div id="sinResultadosFiltro" class="col-span-full py-16 text-center text-slate-400 bg-white border border-slate-200 rounded-2xl hidden">
            <i class="bi bi-search text-3xl block mb-2 text-slate-300"></i>
            <p class="text-sm font-semibold text-slate-700">No se encontraron categorías</p>
            <p class="text-xs text-slate-400 mt-1">Intenta con otro término de búsqueda.</p>
        </div>
    </div>

</div>

{{-- MODAL CREAR CATEGORIA --}}
<div id="modalCrearCat" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-tags text-xl text-slate-800"></i>
                <h3 class="font-bold text-slate-900 text-base">Nueva Categoría</h3>
            </div>
            <button type="button" onclick="cerrarModalCrearCat()" class="text-slate-400 hover:text-slate-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.categorias.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" autocomplete="off">
            @csrf

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Imagen de la Categoría (Opcional)</label>
                <div class="flex items-center gap-4">
                    <div id="preview_cat_create_box" class="w-16 h-16 rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0 text-slate-400">
                        <i class="bi bi-image text-xl" id="preview_cat_create_icon"></i>
                        <img id="preview_cat_create_img" class="w-full h-full object-cover hidden" alt="Preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="imagen" id="imagen_cat_create" accept="image/*" onchange="previewCatImage(this, 'preview_cat_create_img', 'preview_cat_create_icon')"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Formatos: JPG, PNG, WEBP. Máximo 2MB.</p>
                    </div>
                </div>
            </div>

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
                <button type="button" onclick="cerrarModalCrearCat()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold cursor-pointer">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm cursor-pointer">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR CATEGORIA --}}
<div id="modalEditarCat" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <i class="bi bi-pencil-square text-xl text-slate-800"></i>
                <h3 class="font-bold text-slate-900 text-base">Editar Categoría</h3>
            </div>
            <button type="button" onclick="cerrarModalEditarCat()" class="text-slate-400 hover:text-slate-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formEditarCat" method="POST" enctype="multipart/form-data" class="space-y-4" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Imagen de la Categoría (Opcional)</label>
                <div class="flex items-center gap-4">
                    <div id="preview_cat_edit_box" class="w-16 h-16 rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0 text-slate-400">
                        <i class="bi bi-image text-xl" id="preview_cat_edit_icon"></i>
                        <img id="preview_cat_edit_img" class="w-full h-full object-cover hidden" alt="Preview">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="imagen" id="imagen_cat_edit" accept="image/*" onchange="previewCatImage(this, 'preview_cat_edit_img', 'preview_cat_edit_icon')"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Selecciona una nueva imagen solo si deseas reemplazarla.</p>
                    </div>
                </div>
            </div>

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
                <button type="button" onclick="cerrarModalEditarCat()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold cursor-pointer">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm cursor-pointer">Actualizar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL ELIMINAR CATEGORIA (INTELIGENTE) --}}
<div id="modalEliminarCat" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-sm w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        
        <div id="eliminar_cat_icon_container" class="flex items-center justify-center text-3xl mx-auto text-amber-500 mb-1">
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
    function aplicarFiltroCategorias() {
        const texto = (document.getElementById('buscadorCategorias')?.value || '').toLowerCase().trim();
        const tarjetas = document.querySelectorAll('.card-categoria');
        let visibles = 0;

        tarjetas.forEach(card => {
            const nombre = card.dataset.nombre || '';
            const desc = card.dataset.descripcion || '';
            const match = !texto || nombre.includes(texto) || desc.includes(texto);

            if (match) {
                card.style.display = '';
                visibles++;
            } else {
                card.style.display = 'none';
            }
        });

        const contador = document.getElementById('contadorCategorias');
        if (contador) {
            contador.textContent = `${visibles} Categorías Registradas`;
        }

        const sinResultados = document.getElementById('sinResultadosFiltro');
        if (sinResultados) {
            if (visibles === 0 && tarjetas.length > 0) {
                sinResultados.classList.remove('hidden');
            } else {
                sinResultados.classList.add('hidden');
            }
        }
    }

    function previewCatImage(input, imgId, iconId) {
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

    function abrirModalCrearCat() {
        document.getElementById('imagen_cat_create').value = '';
        document.getElementById('preview_cat_create_img').src = '';
        document.getElementById('preview_cat_create_img').classList.add('hidden');
        document.getElementById('preview_cat_create_icon').classList.remove('hidden');
        document.getElementById('modalCrearCat').classList.remove('hidden');
    }
    function cerrarModalCrearCat() { document.getElementById('modalCrearCat').classList.add('hidden'); }

    function abrirModalEditarCat(id, nombre, desc, imgUrl) {
        document.getElementById('formEditarCat').action = `/admin/categorias/${id}`;
        document.getElementById('edit_cat_nombre').value = nombre;
        document.getElementById('edit_cat_desc').value = desc;
        document.getElementById('imagen_cat_edit').value = '';

        const editImg = document.getElementById('preview_cat_edit_img');
        const editIcon = document.getElementById('preview_cat_edit_icon');
        if (imgUrl) {
            editImg.src = imgUrl;
            editImg.classList.remove('hidden');
            editIcon.classList.add('hidden');
        } else {
            editImg.src = '';
            editImg.classList.add('hidden');
            editIcon.classList.remove('hidden');
        }

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
            iconContainer.className = "flex items-center justify-center text-3xl mx-auto text-amber-500 mb-1";
            icon.className = "bi bi-shield-exclamation";
            titulo.textContent = "No se puede eliminar esta categoría";
            descripcion.innerHTML = `La categoría <strong class="text-slate-800">${nombre}</strong> tiene <strong class="text-slate-800">${totalProductos} producto(s)</strong> vinculados en el catálogo. Por seguridad no es posible eliminarla; primero debes reasignar o eliminar sus productos.`;

            form.classList.add('hidden');
            btnBloqueado.classList.remove('hidden');
        } else {
            // Permitido eliminar
            iconContainer.className = "flex items-center justify-center text-3xl mx-auto text-rose-500 mb-1";
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
