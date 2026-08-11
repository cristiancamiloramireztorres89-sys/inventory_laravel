<!-- Footer (Claro & Minimalista) -->
<footer class="bg-slate-50 border-t border-slate-200 mt-auto pt-14 pb-8 text-slate-600 text-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-slate-200">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm"><i class="bi bi-box-seam-fill"></i></div>
                    <span class="text-base font-bold text-slate-900">Inventory System</span>
                </div>
                <p class="text-slate-500 leading-relaxed max-w-md text-xs">La solución definitiva para la gestión inteligente de inventarios. Control total de stock, análisis en tiempo real y seguridad empresarial.</p>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-3">Accesos</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('login') }}" class="hover:text-slate-900 transition-colors">Iniciar Sesión</a></li>
                    <li><a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition-colors">Panel Administrador</a></li>
                    <li><a href="#modulos" class="hover:text-slate-900 transition-colors">Módulos</a></li>
                    <li><a href="#caracteristicas" class="hover:text-slate-900 transition-colors">Características</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-slate-900 uppercase tracking-wider mb-3">Contacto</h4>
                <div class="space-y-2 text-xs text-slate-600">
                    <p><strong class="text-slate-900">Email:</strong> <a href="mailto:Cristianramirez8537@gmail.com" class="hover:underline">Cristianramirez8537@gmail.com</a></p>
                    <p><strong class="text-slate-900">Teléfono:</strong> +57 3188145842</p>
                    <p><strong class="text-slate-900">Ubicación:</strong> Campoalegre, Huila</p>
                    <p><strong class="text-slate-900">Horario:</strong> Lun – Vie, 7:00 AM – 7:00 PM</p>
                </div>
            </div>
        </div>

        <div class="pt-6 flex items-center justify-center text-[11px] text-slate-400">
            <p>&copy; {{ date('Y') }} Inventory System. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>
