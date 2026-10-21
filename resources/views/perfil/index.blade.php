@extends('componentes.main')

@section('title', 'Mi Perfil | Inventory System')
@section('page_title', 'Mi Perfil de Usuario')
@section('page_subtitle', 'Gestiona tu información personal, avatar y seguridad de la cuenta')

@section('content')

<div class="max-w-6xl mx-auto space-y-6">

    {{-- Cuadrícula de 2 Columnas --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ========================================================================= --}}
        {{-- COLUMNA IZQUIERDA: TARJETA DE RESUMEN DEL USUARIO --}}
        {{-- ========================================================================= --}}
        <div class="space-y-6">

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col items-center text-center">
                
                {{-- Contenedor del Avatar / Foto de Perfil --}}
                <div class="flex flex-col items-center">
                    <div class="relative group cursor-pointer" onclick="document.getElementById('input_foto_perfil').click()" title="Haz clic para cambiar tu foto">
                        {{-- Círculo Avatar con tamaño garantizado --}}
                        <div id="avatar_preview_container"
                             class="rounded-full shadow-md overflow-hidden bg-slate-900 flex items-center justify-center text-white border-4 border-slate-100 transition-all duration-200 group-hover:border-slate-300"
                             style="width: 104px; height: 104px; min-width: 104px; min-height: 104px;">
                            @if($usuario->foto_url)
                                <img id="avatar_img_preview"
                                     src="{{ $usuario->foto_url }}"
                                     alt="{{ $usuario->nombre }}"
                                     class="w-full h-full object-cover">
                                <span id="avatar_initial_preview" class="hidden text-3xl font-extrabold">{{ strtoupper(substr($usuario->nombre ?? 'U', 0, 1)) }}</span>
                            @else
                                <img id="avatar_img_preview"
                                     src=""
                                     alt="Avatar"
                                     class="w-full h-full object-cover hidden">
                                <span id="avatar_initial_preview" class="text-3xl font-extrabold">{{ strtoupper(substr($usuario->nombre ?? 'U', 0, 1)) }}</span>
                            @endif
                        </div>

                        {{-- Capa Hover elegante --}}
                        <div class="absolute inset-0 rounded-full bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex flex-col items-center justify-center text-white gap-1">
                            <i class="bi bi-camera text-xl"></i>
                            <span class="text-[10px] font-bold">Cambiar</span>
                        </div>
                    </div>

                    {{-- Botón claro y elegante para cambiar foto --}}
                    <button type="button"
                            onclick="document.getElementById('input_foto_perfil').click()"
                            class="mt-3 cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 text-xs font-semibold transition-all shadow-2xs">
                        <i class="bi bi-camera text-slate-500"></i> Cambiar Foto
                    </button>

                    {{-- Si tiene foto, botón sutil para eliminarla --}}
                    @if($usuario->foto)
                        <form action="{{ route('perfil.photo.destroy') }}" method="POST" class="mt-2"
                              onsubmit="return confirm('¿Seguro que deseas eliminar tu foto de perfil?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="py-1 px-2.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 text-[11px] font-semibold transition-colors flex items-center justify-center gap-1 cursor-pointer">
                                <i class="bi bi-trash"></i> Eliminar Foto
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Nombre y Correo --}}
                <h3 class="text-base font-bold text-slate-900 mt-4">{{ $usuario->nombre }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $usuario->correo }}</p>

                {{-- Rol del Usuario (sin cuadro de color) --}}
                <div class="mt-2">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500">
                        <i class="bi {{ $usuario->esAdmin() ? 'bi-shield-check text-sky-500' : 'bi-person-badge text-sky-500' }}"></i>
                        {{ ucfirst($usuario->rol?->nombre ?? 'Usuario') }}
                    </span>
                </div>

                {{-- Metadatos de la Cuenta --}}
                <div class="w-full mt-6 pt-5 border-t border-slate-100 text-left space-y-3 text-xs">

                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Miembro desde</span>
                        <span class="font-semibold text-slate-800">
                            {{ $usuario->created_at ? $usuario->created_at->format('d/m/Y') : 'Registrado' }}
                        </span>
                    </div>

                    @if($usuario->telefono)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Teléfono</span>
                            <span class="font-semibold text-slate-800">{{ $usuario->telefono }}</span>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Tarjeta de Ayuda / Info --}}
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-xs text-slate-600 space-y-2">
                <div class="flex items-center gap-2 font-bold text-slate-900">
                    <i class="bi bi-shield-lock-fill text-sky-600 text-sm"></i>
                    <span>Seguridad y Privacidad</span>
                </div>
                <p class="leading-relaxed text-slate-500">
                    Tu información de acceso es privada. Te recomendamos actualizar periódicamente tu contraseña y no compartirla con nadie.
                </p>
            </div>

        </div>

        {{-- ========================================================================= --}}
        {{-- COLUMNA DERECHA: FORMULARIOS DE INFORMACIÓN Y SEGURIDAD --}}
        {{-- ========================================================================= --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. INFORMACIÓN PERSONAL Y FOTO --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                    <i class="bi bi-person-lines-fill text-xl text-sky-600"></i>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Información Personal</h4>
                        <p class="text-xs text-slate-500">Actualiza tus nombres, correo y foto de perfil</p>
                    </div>
                </div>

                <form id="formPerfil" action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4" autocomplete="off">
                    @csrf
                    @method('PUT')

                    {{-- Selector de Archivo de Foto (Conectado con el avatar de la columna izquierda) --}}
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <label class="block text-xs font-bold text-slate-700">Cambiar Foto de Perfil</label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <input type="file"
                                   name="foto"
                                   id="input_foto_perfil"
                                   accept="image/*"
                                   onchange="handleAvatarFileSelect(this)"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                            <span class="text-[11px] text-slate-400 flex-shrink-0">JPG, PNG, WEBP. Máx. 2MB.</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Nombre Completo --}}
                        <div class="space-y-1">
                            <label for="nombre" class="block text-xs font-bold text-slate-700">Nombre Completo</label>
                            <input type="text"
                                   name="nombre"
                                   id="nombre"
                                   value="{{ old('nombre', $usuario->nombre) }}"
                                   required
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                        </div>

                        {{-- Correo Electrónico --}}
                        <div class="space-y-1">
                            <label for="correo" class="block text-xs font-bold text-slate-700">Correo Electrónico</label>
                            <input type="email"
                                   name="correo"
                                   id="correo"
                                   value="{{ old('correo', $usuario->correo) }}"
                                   required
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Teléfono --}}
                        <div class="space-y-1">
                            <label for="telefono" class="block text-xs font-bold text-slate-700">Teléfono (Opcional)</label>
                            <input type="text"
                                   name="telefono"
                                   id="telefono"
                                   placeholder="Ej. +57 300 123 4567"
                                   value="{{ old('telefono', $usuario->telefono) }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                        </div>

                        {{-- Rol (Solo Lectura) --}}
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">Rol Asignado</label>
                            <input type="text"
                                   value="{{ ucfirst($usuario->rol?->nombre ?? 'Usuario') }}"
                                   disabled
                                   class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-500 cursor-not-allowed">
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                        <button type="button"
                                onclick="abrirModalConfirmarPerfil()"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer">
                            <i class="bi bi-check-lg"></i> Guardar Información
                        </button>
                    </div>
                </form>

            </div>

            {{-- 2. SEGURIDAD Y CAMBIO DE CONTRASEÑA --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                
                <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                    <i class="bi bi-key-fill text-xl text-amber-600"></i>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Seguridad de la Cuenta</h4>
                        <p class="text-xs text-slate-500">Actualiza tu contraseña de acceso</p>
                    </div>
                </div>

                <form action="{{ route('perfil.password') }}" method="POST" class="space-y-4" autocomplete="off">
                    @csrf
                    @method('PUT')

                    {{-- Contraseña Actual --}}
                    <div class="space-y-1">
                        <label for="contrasena_actual" class="block text-xs font-bold text-slate-700">Contraseña Actual</label>
                        <div class="relative">
                            <input type="password"
                                   name="contrasena_actual"
                                   id="contrasena_actual"
                                   required
                                   placeholder="Ingresa tu contraseña actual"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3.5 pr-10 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                            <button type="button"
                                    onclick="togglePasswordVisibility('contrasena_actual', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Nueva Contraseña --}}
                        <div class="space-y-1">
                            <label for="nueva_contrasena" class="block text-xs font-bold text-slate-700">Nueva Contraseña</label>
                            <div class="relative">
                                <input type="password"
                                       name="nueva_contrasena"
                                       id="nueva_contrasena"
                                       required
                                       placeholder="Mínimo 6 caracteres"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3.5 pr-10 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                                <button type="button"
                                        onclick="togglePasswordVisibility('nueva_contrasena', this)"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Confirmar Nueva Contraseña --}}
                        <div class="space-y-1">
                            <label for="nueva_contrasena_confirmation" class="block text-xs font-bold text-slate-700">Confirmar Nueva Contraseña</label>
                            <div class="relative">
                                <input type="password"
                                       name="nueva_contrasena_confirmation"
                                       id="nueva_contrasena_confirmation"
                                       required
                                       placeholder="Repite la nueva contraseña"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3.5 pr-10 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                                <button type="button"
                                        onclick="togglePasswordVisibility('nueva_contrasena_confirmation', this)"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-3 border-t border-slate-100">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition-colors cursor-pointer">
                            <i class="bi bi-shield-lock"></i> Actualizar Contraseña
                        </button>
                    </div>
                </form>

            </div>

        </div>

    </div>

{{-- MODAL CONFIRMAR GUARDAR INFORMACIÓN DE PERFIL --}}
<div id="modalConfirmarPerfil" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-sm w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150 text-center">
        
        <div class="flex items-center justify-center text-3xl mx-auto text-slate-800 mb-1">
            <i class="bi bi-person-check"></i>
        </div>

        <div class="space-y-1.5">
            <h3 class="font-bold text-slate-900 text-base">¿Guardar cambios?</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                ¿Estás seguro de que deseas actualizar tu información personal y foto de perfil?
            </p>
        </div>

        <div class="pt-2 flex items-center gap-2">
            <button type="button"
                    onclick="cerrarModalConfirmarPerfil()"
                    class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold cursor-pointer transition-colors">
                Cancelar
            </button>
            <button type="button"
                    onclick="confirmarGuardarPerfil()"
                    class="w-1/2 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-sm cursor-pointer transition-colors">
                Sí, Guardar
            </button>
        </div>
    </div>
</div>

<script>
    function abrirModalConfirmarPerfil() {
        const form = document.getElementById('formPerfil');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        document.getElementById('modalConfirmarPerfil').classList.remove('hidden');
    }

    function cerrarModalConfirmarPerfil() {
        document.getElementById('modalConfirmarPerfil').classList.add('hidden');
    }

    function confirmarGuardarPerfil() {
        document.getElementById('formPerfil').submit();
    }

    function handleAvatarFileSelect(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatar_img_preview');
                const initial = document.getElementById('avatar_initial_preview');
                
                img.src = e.target.result;
                img.classList.remove('hidden');
                if (initial) {
                    initial.classList.add('hidden');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePasswordVisibility(fieldId, btn) {
        const field = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>

@endsection
