{{-- MODAL CONFIRMACIÓN ELIMINAR VENTA (ADMIN) --}}
<div id="modalEliminarVenta" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg flex-shrink-0">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base">¿Eliminar Venta #<span id="eliminar_id_venta_txt"></span>?</h3>
                <p class="text-xs text-slate-500 mt-0.5">Esta acción restaurará el stock de los productos vendidos automáticamente.</p>
            </div>
        </div>

        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
            <p><span class="font-semibold text-slate-600">Cliente:</span> <span id="eliminar_cliente_txt" class="font-bold text-slate-900"></span></p>
            <p><span class="font-semibold text-slate-600">Total Venta:</span> <span id="eliminar_total_txt" class="font-black text-rose-600"></span></p>
        </div>

        <form id="formEliminarVenta" method="POST" action="" class="flex items-center justify-end gap-2 pt-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="cerrarModalEliminarVenta()"
                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold">
                Cancelar
            </button>
            <button type="submit"
                class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition-colors">
                Sí, Eliminar y Restaurar Stock
            </button>
        </form>
    </div>
</div>
