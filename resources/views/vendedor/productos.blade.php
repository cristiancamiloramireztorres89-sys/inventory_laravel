@extends('componentes.main')

@section('title', 'Consulta de Stock | Inventory System')
@section('page_title', 'Catálogo de Productos')
@section('page_subtitle', 'Consulta visual de existencias, precios al público y disponibilidad')

@section('content')

<div class="space-y-6">

    {{-- Encabezado con buscador y filtro de categoría --}}
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                <span>Catálogo de Mostrador</span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700" id="contadorProductos">
                    {{ $productos->count() }} artículos
                </span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Precios vigentes y existencias en tiempo real</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            {{-- Buscador en tiempo real --}}
            <div class="relative flex-1 min-w-[200px]">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input
                    type="text"
                    id="buscadorProductos"
                    placeholder="Buscar por artículo o marca..."
                    class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-900 focus:bg-white transition-all shadow-sm"
                    onkeyup="aplicarFiltrosVendedor()"
                >
            </div>

            {{-- Filtro por Categoría --}}
            <div class="min-w-[160px] flex-1 sm:flex-initial">
                <select id="filtroCategoria" onchange="aplicarFiltrosVendedor()"
                    class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 font-medium focus:outline-none focus:border-slate-900 transition-all shadow-sm cursor-pointer">
                    <option value="todas">Todas las Categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ strtolower($cat->nombre) }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200 flex-shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                {{ $productos->filter(fn($p) => $p->stock_actual > 0)->count() }} con stock
            </span>
        </div>
    </div>

    {{-- Cuadrícula de Productos (Cards / Cuadros) para Vendedor --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="gridProductos">
        @forelse ($productos as $producto)
            <div class="card-producto group bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 flex flex-col overflow-hidden"
                 data-nombre="{{ strtolower($producto->nombre) }}"
                 data-marca="{{ strtolower($producto->marca ?? '') }}"
                 data-categoria="{{ strtolower($producto->categoria->nombre ?? '') }}">

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

                {{-- Cuerpo de la Tarjeta (Todos los datos debajo de la imagen) --}}
                <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                    <div>
                        {{-- Fila de Insignias: Categoría y Estado de Stock --}}
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 truncate max-w-[140px]">
                                {{ $producto->categoria->nombre ?? 'General' }}
                            </span>
                            @if($producto->stock_actual <= 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 flex-shrink-0">
                                    Agotado
                                </span>
                            @elseif($producto->stock_actual <= $producto->stock_minimo)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 flex-shrink-0">
                                    Stock Bajo ({{ $producto->stock_actual }})
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex-shrink-0">
                                    {{ $producto->stock_actual }} disp.
                                </span>
                            @endif
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
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Precio al Público</span>
                            <p class="text-2xl font-black text-slate-900">
                                ${{ number_format($producto->precio_venta, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold {{ $producto->stock_actual > 0 ? 'text-emerald-700' : 'text-red-500' }}">
                                {{ $producto->stock_actual }} unid.
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white border border-slate-200 rounded-2xl p-8">
                <i class="bi bi-box-seam text-4xl text-slate-300 block mb-2"></i>
                <p class="text-sm font-bold text-slate-700">No hay productos disponibles</p>
                <p class="text-xs text-slate-400 mt-1">El catálogo se encuentra vacío en este momento.</p>
            </div>
        @endforelse
    </div>

    {{-- Mensaje cuando no hay resultados de búsqueda/filtro --}}
    <div id="sinResultadosFiltroVendedor" class="hidden py-16 text-center bg-white border border-slate-200 rounded-2xl p-8">
        <i class="bi bi-funnel text-4xl text-slate-300 block mb-2"></i>
        <p class="text-sm font-bold text-slate-700">No se encontraron productos con los filtros seleccionados</p>
        <p class="text-xs text-slate-400 mt-1">Intenta cambiar la categoría o el término de búsqueda.</p>
    </div>

</div>

<script>
    function aplicarFiltrosVendedor() {
        const texto = document.getElementById('buscadorProductos').value.toLowerCase().trim();
        const categoria = document.getElementById('filtroCategoria').value.toLowerCase();

        const tarjetas = document.querySelectorAll('.card-producto');
        let visibles = 0;

        tarjetas.forEach(card => {
            const cardNombre = card.dataset.nombre || '';
            const cardMarca = card.dataset.marca || '';
            const cardCat = card.dataset.categoria || '';

            const matchTexto = !texto || cardNombre.includes(texto) || cardMarca.includes(texto) || cardCat.includes(texto);
            const matchCat = (categoria === 'todas') || (cardCat === categoria);

            if (matchTexto && matchCat) {
                card.style.display = '';
                visibles++;
            } else {
                card.style.display = 'none';
            }
        });

        const contador = document.getElementById('contadorProductos');
        if (contador) {
            contador.textContent = `${visibles} artículos`;
        }

        const sinResultados = document.getElementById('sinResultadosFiltroVendedor');
        if (sinResultados) {
            if (visibles === 0 && tarjetas.length > 0) {
                sinResultados.classList.remove('hidden');
            } else {
                sinResultados.classList.add('hidden');
            }
        }
    }
</script>

@endsection
