{{-- MODAL VENTA CREADA: IMPRIMIR FACTURA POS (VENDEDOR) --}}
@if(session('venta_creada_id'))
<div id="modalVentaCreadaExitoVendedor" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 text-center space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mx-auto shadow-inner">
            <i class="bi bi-cart-check-fill"></i>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-900 text-lg">¡Venta Registrada Exitosamente!</h3>
            <p class="text-xs text-slate-500 mt-1">Se generó la factura POS <strong>#VEN-{{ str_pad(session('venta_creada_id'), 5, '0', STR_PAD_LEFT) }}</strong></p>
        </div>

        <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row items-center justify-center gap-2">
            <a href="{{ route('vendedor.ventas.factura', session('venta_creada_id')) }}" target="_blank"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                <i class="bi bi-printer-fill"></i> Imprimir Ticket POS
            </a>
            <a href="{{ route('vendedor.ventas.factura.pdf', session('venta_creada_id')) }}" target="_blank"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                <i class="bi bi-file-earmark-pdf-fill"></i> Descargar PDF
            </a>
        </div>

        <div class="pt-1">
            <button type="button" onclick="document.getElementById('modalVentaCreadaExitoVendedor').remove()"
                    class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold cursor-pointer">
                Cerrar y Continuar
            </button>
        </div>
    </div>
</div>
@endif
