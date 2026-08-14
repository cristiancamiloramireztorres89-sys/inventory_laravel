@extends('componentes.main')

@section('title', 'Usuarios | Inventory System')
@section('page_title', 'Usuarios')
@section('page_subtitle', 'Gestión de usuarios registrados y control de acceso')

@section('content')

<div class="space-y-6">

    {{-- Barra superior: total + botón agregar --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center">
                <i class="bi bi-people-fill text-base"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">{{ $usuarios->count() }} usuarios registrados</p>
                <p class="text-xs text-slate-500">Administradores y vendedores del sistema</p>
            </div>
        </div>

        {{-- Botón agregar usuario --}}
        <button type="button" onclick="abrirModalCrear()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm">
            <i class="bi bi-plus-lg"></i> Agregar Usuario
        </button>
    </div>

    {{-- Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Buscador --}}
        <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
            <div class="relative flex-1 max-w-xs">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                <input
                    type="text"
                    id="buscador"
                    placeholder="Buscar por nombre o correo..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-slate-400 focus:bg-white transition-all"
                    onkeyup="filtrarTabla()"
                >
            </div>
        </div>

        {{-- Encabezado tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full" id="tablaUsuarios">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">#</th>
                        <th class="text-left px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Usuario</th>
                        <th class="text-left px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Correo</th>
                        <th class="text-left px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Rol</th>
                        <th class="text-left px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="text-right px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($usuarios as $usuario)
                        <tr class="hover:bg-slate-50 transition-colors {{ !$usuario->activo ? 'opacity-70 bg-slate-50/50' : '' }}"
                            data-nombre="{{ strtolower($usuario->nombre) }}"
                            data-correo="{{ strtolower($usuario->correo) }}">

                            {{-- ID --}}
                            <td class="px-6 py-4 text-xs text-slate-400 font-medium">
                                {{ $usuario->id_usuario }}
                            </td>

                            {{-- Nombre + avatar --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $usuario->activo ? 'bg-slate-900' : 'bg-slate-400' }} text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-semibold text-slate-900">{{ $usuario->nombre }}</span>
                                </div>
                            </td>

                            {{-- Correo --}}
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $usuario->correo }}
                            </td>

                            {{-- Rol --}}
                            <td class="px-6 py-4">
                                @php $rol = strtolower($usuario->rol->nombre ?? 'sin rol'); @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold
                                    {{ $rol === 'administrador'
                                        ? 'bg-slate-900 text-white'
                                        : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                    <i class="bi {{ $rol === 'administrador' ? 'bi-shield-check' : 'bi-bag-check' }} text-[10px]"></i>
                                    {{ ucfirst($rol) }}
                                </span>
                            </td>

                            {{-- Estado Activo / Inactivo --}}
                            <td class="px-6 py-4">
                                @if($usuario->activo)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Botón Editar --}}
                                    <button type="button" title="Editar usuario"
                                        onclick="abrirModalEditar({{ $usuario->id_usuario }}, '{{ addslashes($usuario->nombre) }}', '{{ addslashes($usuario->correo) }}', {{ $usuario->id_rol }})"
                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
                                        <i class="bi bi-pencil text-xs"></i>
                                    </button>

                                    {{-- Botón Desactivar / Activar --}}
                                    @if($usuario->id_usuario !== auth()->id())
                                        @if($usuario->activo)
                                            <button type="button" title="Desactivar usuario"
                                                onclick="abrirModalToggle({{ $usuario->id_usuario }}, '{{ addslashes($usuario->nombre) }}', true)"
                                                class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-600 flex items-center justify-center transition-colors cursor-pointer">
                                                <i class="bi bi-person-slash text-xs"></i>
                                            </button>
                                        @else
                                            <button type="button" title="Activar usuario"
                                                onclick="abrirModalToggle({{ $usuario->id_usuario }}, '{{ addslashes($usuario->nombre) }}', false)"
                                                class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-600 flex items-center justify-center transition-colors cursor-pointer">
                                                <i class="bi bi-person-check text-xs"></i>
                                            </button>
                                        @endif

                                        {{-- Botón Eliminar --}}
                                        @php
                                            $tieneHistorial = ($usuario->ventas_count > 0 || $usuario->compras_count > 0);
                                        @endphp
                                        <button type="button"
                                            title="{{ $tieneHistorial ? 'No se puede eliminar: tiene ' . $usuario->ventas_count . ' venta(s) y ' . $usuario->compras_count . ' compra(s) registradas' : 'Eliminar usuario permanentemente' }}"
                                            onclick="abrirModalEliminar({{ $usuario->id_usuario }}, '{{ addslashes($usuario->nombre) }}', {{ $usuario->ventas_count }}, {{ $usuario->compras_count }})"
                                            class="w-8 h-8 rounded-lg {{ $tieneHistorial ? 'bg-slate-100 border-slate-200 text-slate-400 opacity-60 cursor-pointer' : 'bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600 cursor-pointer' }} flex items-center justify-center transition-colors">
                                            <i class="bi bi-trash text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">
                                        <i class="bi bi-people text-slate-400 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-500">No hay usuarios registrados</p>
                                    <p class="text-xs text-slate-400">Agrega el primer usuario con el botón de arriba</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer tabla --}}
        @if($usuarios->count() > 0)
        <div class="px-6 py-3.5 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
            <p class="text-xs text-slate-400">
                Mostrando <span class="font-semibold text-slate-600">{{ $usuarios->count() }}</span> usuario(s)
            </p>
            <div class="flex items-center gap-3 text-xs text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                    {{ $usuarios->where('activo', true)->count() }} activo(s)
                </span>
                <span class="text-slate-300">|</span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 inline-block"></span>
                    {{ $usuarios->where('activo', false)->count() }} inactivo(s)
                </span>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL CREAR USUARIO --}}
{{-- ========================================================================= --}}
<div id="modalCrear" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-sm">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Agregar Nuevo Usuario</h3>
            </div>
            <button type="button" onclick="cerrarModalCrear()" class="text-slate-400 hover:text-slate-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4" autocomplete="off">
            @csrf

            <div class="space-y-1">
                <label for="create_nombre" class="block text-xs font-bold text-slate-700">Nombre Completo</label>
                <input type="text" name="nombre" id="create_nombre" required placeholder="Ej. Carlos Mendoza" autocomplete="off" value=""
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
            </div>

            <div class="space-y-1">
                <label for="create_correo" class="block text-xs font-bold text-slate-700">Correo Electrónico</label>
                <input type="email" name="correo" id="create_correo" required placeholder="carlos@empresa.com" autocomplete="new-password" value=""
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
            </div>

            <div class="space-y-1">
                <label for="create_id_rol" class="block text-xs font-bold text-slate-700">Rol de Usuario</label>
                <select name="id_rol" id="create_id_rol" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                    <option value="">Seleccione un rol...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id_rol }}">{{ ucfirst($role->nombre) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label for="create_contrasena" class="block text-xs font-bold text-slate-700">Contraseña Inicial</label>
                <div class="relative">
                    <input type="password" name="contrasena" id="create_contrasena" required minlength="6" placeholder="Mínimo 6 caracteres" autocomplete="new-password" value=""
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3.5 pr-10 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                    <button type="button" onclick="togglePasswordVisibility('create_contrasena', 'eye_create')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors cursor-pointer p-1"
                        title="Mostrar u ocultar contraseña">
                        <i id="eye_create" class="bi bi-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="cerrarModalCrear()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm">
                    Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL EDITAR USUARIO --}}
{{-- ========================================================================= --}}
<div id="modalEditar" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-6 space-y-5 animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-sm">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-base">Editar Usuario</h3>
            </div>
            <button type="button" onclick="cerrarModalEditar()" class="text-slate-400 hover:text-slate-700 text-lg cursor-pointer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="formEditar" method="POST" class="space-y-4" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="space-y-1">
                <label for="edit_nombre" class="block text-xs font-bold text-slate-700">Nombre Completo</label>
                <input type="text" name="nombre" id="edit_nombre" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
            </div>

            <div class="space-y-1">
                <label for="edit_correo" class="block text-xs font-bold text-slate-700">Correo Electrónico</label>
                <input type="email" name="correo" id="edit_correo" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
            </div>

            <div class="space-y-1">
                <label for="edit_id_rol" class="block text-xs font-bold text-slate-700">Rol de Usuario</label>
                <select name="id_rol" id="edit_id_rol" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                    @foreach($roles as $role)
                        <option value="{{ $role->id_rol }}">{{ ucfirst($role->nombre) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label for="edit_contrasena" class="block text-xs font-bold text-slate-700">Nueva Contraseña (Opcional)</label>
                <div class="relative">
                    <input type="password" name="contrasena" id="edit_contrasena" minlength="6" placeholder="Dejar en blanco para mantener la actual"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3.5 pr-10 py-2.5 text-xs text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                    <button type="button" onclick="togglePasswordVisibility('edit_contrasena', 'eye_edit')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition-colors cursor-pointer p-1"
                        title="Mostrar u ocultar contraseña">
                        <i id="eye_edit" class="bi bi-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="cerrarModalEditar()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm">
                    Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL DESACTIVAR / ACTIVAR USUARIO --}}
{{-- ========================================================================= --}}
<div id="modalToggle" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-sm w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div id="toggle_icon_container" class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl mx-auto">
            <i id="toggle_icon" class="bi"></i>
        </div>

        <div class="text-center space-y-1">
            <h3 id="toggle_titulo" class="font-bold text-slate-900 text-base"></h3>
            <p id="toggle_descripcion" class="text-xs text-slate-500 leading-relaxed"></p>
        </div>

        <form id="formToggle" method="POST" class="pt-2 flex items-center gap-2">
            @csrf
            @method('PATCH')
            <button type="button" onclick="cerrarModalToggle()"
                class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors cursor-pointer">
                Cancelar
            </button>
            <button type="submit" id="toggle_btn_confirm"
                class="w-1/2 py-2.5 rounded-xl text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm">
            </button>
        </form>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL ELIMINAR USUARIO --}}
{{-- ========================================================================= --}}
<div id="modalEliminar" class="fixed inset-0 z-50 bg-slate-950/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-sm w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150">
        <div id="eliminar_icon_container" class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl mx-auto">
            <i id="eliminar_icon" class="bi bi-trash3-fill"></i>
        </div>

        <div class="text-center space-y-1.5">
            <h3 id="eliminar_titulo" class="font-bold text-slate-900 text-base">¿Eliminar Usuario?</h3>
            <p id="eliminar_descripcion" class="text-xs text-slate-500 leading-relaxed"></p>
        </div>

        {{-- Formulario para eliminar cuando es permitido --}}
        <form id="formEliminar" method="POST" class="pt-2 flex items-center gap-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="cerrarModalEliminar()"
                class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors cursor-pointer">
                Cancelar
            </button>
            <button type="submit" id="eliminar_btn_confirm"
                class="w-1/2 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm">
                Sí, Eliminar
            </button>
        </form>

        {{-- Botón de entendido cuando está bloqueado por tener transacciones --}}
        <div id="eliminar_btn_bloqueado_container" class="hidden pt-2">
            <button type="button" onclick="cerrarModalEliminar()"
                class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm">
                Entendido
            </button>
        </div>
    </div>
</div>

<script>
    function filtrarTabla() {
        const texto = document.getElementById('buscador').value.toLowerCase();
        const filas = document.querySelectorAll('#tablaUsuarios tbody tr[data-nombre]');
        filas.forEach(fila => {
            const nombre  = fila.dataset.nombre  || '';
            const correo  = fila.dataset.correo  || '';
            fila.style.display = (nombre.includes(texto) || correo.includes(texto)) ? '' : 'none';
        });
    }

    // Modal Crear
    function abrirModalCrear() {
        const nombre = document.getElementById('create_nombre');
        const correo = document.getElementById('create_correo');
        const rol = document.getElementById('create_id_rol');
        const pass = document.getElementById('create_contrasena');
        const eye = document.getElementById('eye_create');

        if (nombre) nombre.value = '';
        if (correo) correo.value = '';
        if (rol) rol.value = '';
        if (pass) {
            pass.value = '';
            pass.type = 'password';
        }
        if (eye) eye.className = 'bi bi-eye text-sm';

        document.getElementById('modalCrear').classList.remove('hidden');
    }
    function cerrarModalCrear() {
        document.getElementById('modalCrear').classList.add('hidden');
    }

    // Modal Editar
    function abrirModalEditar(id, nombre, correo, idRol) {
        const form = document.getElementById('formEditar');
        form.action = `/admin/usuarios/${id}`;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_correo').value = correo;
        document.getElementById('edit_id_rol').value = idRol;
        document.getElementById('edit_contrasena').value = '';
        document.getElementById('modalEditar').classList.remove('hidden');
    }
    function cerrarModalEditar() {
        document.getElementById('modalEditar').classList.add('hidden');
    }

    // Modal Desactivar / Activar
    function abrirModalToggle(id, nombre, esActivoActualmente) {
        const form = document.getElementById('formToggle');
        form.action = `/admin/usuarios/${id}/toggle`;

        const iconContainer = document.getElementById('toggle_icon_container');
        const icon = document.getElementById('toggle_icon');
        const titulo = document.getElementById('toggle_titulo');
        const descripcion = document.getElementById('toggle_descripcion');
        const btn = document.getElementById('toggle_btn_confirm');

        if (esActivoActualmente) {
            // Desactivar
            iconContainer.className = "w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mx-auto";
            icon.className = "bi bi-person-slash";
            titulo.textContent = "¿Desactivar Usuario?";
            descripcion.innerHTML = `Estás a punto de desactivar a <strong class="text-slate-800">${nombre}</strong>. El usuario no podrá iniciar sesión hasta que lo reactives.`;
            btn.className = "w-1/2 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm";
            btn.textContent = "Sí, Desactivar";
        } else {
            // Activar
            iconContainer.className = "w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mx-auto";
            icon.className = "bi bi-person-check-fill";
            titulo.textContent = "¿Activar Usuario?";
            descripcion.innerHTML = `Estás a punto de reactivar la cuenta de <strong class="text-slate-800">${nombre}</strong> para que pueda ingresar al sistema.`;
            btn.className = "w-1/2 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition-colors cursor-pointer shadow-sm";
            btn.textContent = "Sí, Activar";
        }

        document.getElementById('modalToggle').classList.remove('hidden');
    }
    function cerrarModalToggle() {
        document.getElementById('modalToggle').classList.add('hidden');
    }

    // Modal Eliminar Usuario
    function abrirModalEliminar(id, nombre, totalVentas, totalCompras) {
        const form = document.getElementById('formEliminar');
        const btnBloqueado = document.getElementById('eliminar_btn_bloqueado_container');
        const iconContainer = document.getElementById('eliminar_icon_container');
        const icon = document.getElementById('eliminar_icon');
        const titulo = document.getElementById('eliminar_titulo');
        const descripcion = document.getElementById('eliminar_descripcion');

        form.action = `/admin/usuarios/${id}`;

        if (totalVentas > 0 || totalCompras > 0) {
            // Bloqueado por integridad referencial
            let motivos = [];
            if (totalVentas > 0) motivos.push(`<strong>${totalVentas} venta(s)</strong>`);
            if (totalCompras > 0) motivos.push(`<strong>${totalCompras} compra(s)</strong>`);
            const detalle = motivos.join(' y ');

            iconContainer.className = "w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mx-auto";
            icon.className = "bi bi-shield-exclamation";
            titulo.textContent = "No se puede eliminar este usuario";
            descripcion.innerHTML = `El usuario <strong class="text-slate-800">${nombre}</strong> tiene ${detalle} registradas en el historial. Por seguridad contable no es posible eliminarlo, pero puedes <strong class="text-slate-800">desactivar su cuenta</strong> para que no pueda ingresar.`;

            form.classList.add('hidden');
            btnBloqueado.classList.remove('hidden');
        } else {
            // Permitido eliminar
            iconContainer.className = "w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl mx-auto";
            icon.className = "bi bi-trash3-fill";
            titulo.textContent = "¿Eliminar Usuario?";
            descripcion.innerHTML = `¿Estás seguro de eliminar a <strong class="text-slate-800">${nombre}</strong>? Esta acción es definitiva y removerá la cuenta del sistema.`;

            form.classList.remove('hidden');
            btnBloqueado.classList.add('hidden');
        }

        document.getElementById('modalEliminar').classList.remove('hidden');
    }
    function cerrarModalEliminar() {
        document.getElementById('modalEliminar').classList.add('hidden');
    }

    // Mostrar / Ocultar Contraseña
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash text-sm text-slate-700';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye text-sm text-slate-400';
        }
    }
</script>

@endsection
