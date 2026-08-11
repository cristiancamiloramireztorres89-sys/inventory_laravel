<header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between sticky top-0 z-40">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-lg">
            <i class="bi bi-box-seam-fill"></i>
        </div>
        <div>
            <h1 class="text-base font-bold text-slate-900 leading-tight">
                @if(auth()->user()?->esAdmin())
                    Panel Administrador
                @else
                    Panel Vendedor
                @endif
            </h1>
            <span class="text-xs text-slate-400 font-medium">Inventory System</span>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <div class="text-right hidden sm:block">
            <p class="text-xs font-bold text-slate-900">{{ auth()->user()->nombre ?? 'Usuario' }}</p>
            <p class="text-[11px] text-slate-500">{{ auth()->user()->correo ?? '' }}</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-semibold text-slate-700 flex items-center gap-1.5 transition-colors cursor-pointer">
                <i class="bi bi-box-arrow-right"></i> Salir
            </button>
        </form>
    </div>
</header>
