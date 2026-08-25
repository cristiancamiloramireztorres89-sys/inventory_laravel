{{-- SCRIPTS Y LÓGICA DEL PUNTO DE VENTA (VENDEDOR) --}}
<script>
    // Carrito en memoria para la venta actual (Vendedor)
    let vendedorCarritoVenta = [];

    function filtrarVentas() {
        const query = document.getElementById('buscadorVentas').value.toLowerCase();
        const filas = document.querySelectorAll('#tablaVentas tbody tr[data-cliente]');
        filas.forEach(f => {
            const c = f.dataset.cliente || '';
            const items = f.dataset.items || '';
            f.style.display = (c.includes(query) || items.includes(query)) ? '' : 'none';
        });
    }

    function abrirModalCrearVentaVendedor() {
        document.getElementById('modalCrearVentaVendedor').classList.remove('hidden');
        if (vendedorCarritoVenta.length === 0) {
            renderizarCarritoVendedor();
        }
    }
    function cerrarModalCrearVentaVendedor() {
        document.getElementById('modalCrearVentaVendedor').classList.add('hidden');
        ocultarDropdownClienteVendedor();
    }

    // Funciones del Buscador de Clientes Vendedor
    function mostrarDropdownClienteVendedor() {
        document.getElementById('vendedor_dropdown_lista_clientes').classList.remove('hidden');
    }
    function ocultarDropdownClienteVendedor() {
        document.getElementById('vendedor_dropdown_lista_clientes').classList.add('hidden');
    }
    function toggleDropdownClienteVendedor() {
        const dd = document.getElementById('vendedor_dropdown_lista_clientes');
        dd.classList.toggle('hidden');
    }

    function filtrarListaClienteVendedor(query) {
        mostrarDropdownClienteVendedor();
        const q = query.toLowerCase().trim();
        const items = document.querySelectorAll('.vendedor-cliente-item');
        let visibles = 0;

        items.forEach(item => {
            const nombre = item.dataset.nombre || '';
            const tel = item.dataset.telefono || '';
            const correo = item.dataset.correo || '';
            const match = nombre.includes(q) || tel.includes(q) || correo.includes(q);
            item.style.display = match ? 'flex' : 'none';
            if (match) visibles++;
        });

        const opcNuevo = document.getElementById('vendedor_opcion_nuevo_cliente');
        if (q.length > 0) {
            opcNuevo.classList.remove('hidden');
        } else {
            opcNuevo.classList.add('hidden');
        }
    }

    function seleccionarClienteVendedor(id, nombre, telefono) {
        document.getElementById('vendedor_id_cliente').value = id;
        document.getElementById('vendedor_buscador_cliente_input').value = nombre + (telefono ? ` (${telefono})` : '');
        
        document.querySelectorAll('.check-vendedor-icon').forEach(el => el.classList.add('hidden'));
        const check = document.getElementById(`check_vendedor_${id}`);
        if (check) check.classList.remove('hidden');

        ocultarDropdownClienteVendedor();
        cancelarModoNuevoClienteVendedor();
    }

    function activarModoNuevoClienteVendedor() {
        document.getElementById('vendedor_id_cliente').value = 'nuevo';
        ocultarDropdownClienteVendedor();
        
        const box = document.getElementById('vendedor_box_nuevo_cliente');
        box.classList.remove('hidden');
        
        const inputBuscador = document.getElementById('vendedor_buscador_cliente_input');
        const inputNombre = document.getElementById('vendedor_nuevo_cliente_nombre');
        
        if (inputBuscador.value && !inputBuscador.value.includes('(')) {
            inputNombre.value = inputBuscador.value;
        }
        inputNombre.focus();
    }

    function cancelarModoNuevoClienteVendedor() {
        document.getElementById('vendedor_box_nuevo_cliente').classList.add('hidden');
        document.getElementById('vendedor_nuevo_cliente_nombre').value = '';
        document.getElementById('vendedor_nuevo_cliente_telefono').value = '';
        const correoInput = document.getElementById('vendedor_nuevo_cliente_correo');
        if (correoInput) correoInput.value = '';
        
        const actualId = document.getElementById('vendedor_id_cliente').value;
        if (actualId === 'nuevo') {
            const primerItem = document.querySelector('.vendedor-cliente-item');
            if (primerItem) {
                primerItem.click();
            }
        }
    }

    // Cerrar dropdown al hacer clic afuera
    document.addEventListener('click', function(e) {
        const containerCli = document.getElementById('vendedor_cliente_selector_container');
        if (containerCli && !containerCli.contains(e.target)) {
            ocultarDropdownClienteVendedor();
        }

        const containerProd = document.getElementById('vendedor_producto_selector_container');
        if (containerProd && !containerProd.contains(e.target)) {
            ocultarDropdownProductosVendedor();
        }
    });

    // BUSCADOR INTERACTIVO DE PRODUCTOS (VENDEDOR)
    function mostrarDropdownProductosVendedor() {
        document.getElementById('vendedor_dropdown_lista_productos').classList.remove('hidden');
    }
    function ocultarDropdownProductosVendedor() {
        document.getElementById('vendedor_dropdown_lista_productos').classList.add('hidden');
    }
    function filtrarListaProductosVendedor(query) {
        mostrarDropdownProductosVendedor();
        const q = query.toLowerCase().trim();
        const items = document.querySelectorAll('.vendedor-prod-item');
        let visibles = 0;

        items.forEach(item => {
            const nombre = item.dataset.nombre || '';
            const marca = item.dataset.marca || '';
            const cat = item.dataset.categoria || '';
            const match = nombre.includes(q) || marca.includes(q) || cat.includes(q);
            item.style.display = match ? 'flex' : 'none';
            if (match) visibles++;
        });

        const noRes = document.getElementById('vendedor_prod_no_results');
        if (noRes) {
            noRes.classList.toggle('hidden', visibles > 0);
        }

        const btnLimpiar = document.getElementById('vendedor_btn_limpiar_producto');
        if (btnLimpiar) {
            btnLimpiar.classList.toggle('hidden', q.length === 0);
        }
    }

    function seleccionarProductoVendedor(id, nombre, precio, stock) {
        document.getElementById('vendedor_pos_id_producto_seleccionado').value = id;
        document.getElementById('vendedor_pos_nombre_seleccionado').value = nombre;
        document.getElementById('vendedor_pos_precio_seleccionado').value = precio;
        document.getElementById('vendedor_pos_stock_seleccionado').value = stock;

        document.getElementById('vendedor_pos_buscador_producto').value = nombre;
        
        const badge = document.getElementById('vendedor_stock_badge');
        badge.textContent = `Stock disponible: ${stock} unidades | Precio: $${parseFloat(precio).toLocaleString('es-CO')}`;
        badge.className = 'text-[11px] font-bold ' + (stock > 0 ? 'text-emerald-700' : 'text-rose-600');

        const inputCant = document.getElementById('vendedor_pos_cantidad');
        inputCant.max = stock;
        inputCant.value = 1;
        inputCant.focus();
        inputCant.select();

        const btnLimpiar = document.getElementById('vendedor_btn_limpiar_producto');
        if (btnLimpiar) btnLimpiar.classList.remove('hidden');

        ocultarDropdownProductosVendedor();
    }

    function limpiarSeleccionProductoVendedor() {
        document.getElementById('vendedor_pos_id_producto_seleccionado').value = '';
        document.getElementById('vendedor_pos_nombre_seleccionado').value = '';
        document.getElementById('vendedor_pos_precio_seleccionado').value = '0';
        document.getElementById('vendedor_pos_stock_seleccionado').value = '0';
        document.getElementById('vendedor_pos_buscador_producto').value = '';
        document.getElementById('vendedor_pos_cantidad').value = 1;

        const badge = document.getElementById('vendedor_stock_badge');
        badge.textContent = 'Stock: Selecciona un producto';
        badge.className = 'text-[11px] font-bold text-slate-500';

        const btnLimpiar = document.getElementById('vendedor_btn_limpiar_producto');
        if (btnLimpiar) btnLimpiar.classList.add('hidden');

        filtrarListaProductosVendedor('');
        document.getElementById('vendedor_pos_buscador_producto').focus();
    }

    function handleKeydownProductoVendedor(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const primerVisible = Array.from(document.querySelectorAll('.vendedor-prod-item')).find(el => el.style.display !== 'none');
            if (primerVisible) {
                primerVisible.click();
            }
        }
    }

    // Sistema de Notificaciones Toast Modernas (sin alerts nativos)
    function mostrarNotificacionPOS(mensaje, tipo = 'warning') {
        let container = document.getElementById('pos_toast_container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'pos_toast_container';
            container.className = 'fixed top-5 right-5 z-[999999] flex flex-col gap-2.5 pointer-events-none max-w-sm w-full px-4';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl border backdrop-blur-md transition-all duration-300 transform -translate-y-2 opacity-0 text-xs font-semibold';

        let iconClass = 'bi-exclamation-triangle-fill text-amber-400';
        let bgBorder = 'bg-slate-900/95 border-amber-500/40 text-slate-100 shadow-amber-500/10';

        if (tipo === 'warning') {
            iconClass = 'bi-exclamation-triangle-fill text-amber-400';
            bgBorder = 'bg-slate-900/95 border-amber-500/40 text-slate-100 shadow-amber-500/10';
        } else if (tipo === 'error') {
            iconClass = 'bi-x-circle-fill text-rose-400';
            bgBorder = 'bg-slate-900/95 border-rose-500/40 text-slate-100 shadow-rose-500/10';
        } else if (tipo === 'success') {
            iconClass = 'bi-check-circle-fill text-emerald-400';
            bgBorder = 'bg-slate-900/95 border-emerald-500/40 text-slate-100 shadow-emerald-500/10';
        }

        toast.className += ` ${bgBorder}`;
        toast.innerHTML = `
            <div class="w-7 h-7 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0 text-sm">
                <i class="bi ${iconClass}"></i>
            </div>
            <div class="flex-1 leading-snug font-medium text-slate-100">
                ${mensaje}
            </div>
            <button type="button" class="w-6 h-6 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 flex items-center justify-center text-sm cursor-pointer transition-colors flex-shrink-0" onclick="this.closest('.pointer-events-auto').remove()">
                <i class="bi bi-x"></i>
            </button>
        `;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.remove('-translate-y-2', 'opacity-0');
        });

        setTimeout(() => {
            toast.classList.add('-translate-y-2', 'opacity-0');
            setTimeout(() => {
                if (toast.parentElement) toast.remove();
            }, 300);
        }, 4000);
    }

    function agregarProductoAlCarritoVendedor() {
        const idProdVal = document.getElementById('vendedor_pos_id_producto_seleccionado').value;

        if (!idProdVal) {
            mostrarNotificacionPOS('Por favor busca y selecciona un producto de la lista primero.', 'warning');
            document.getElementById('vendedor_pos_buscador_producto').focus();
            return;
        }

        const idProducto = parseInt(idProdVal);
        const nombre = document.getElementById('vendedor_pos_nombre_seleccionado').value || 'Producto';
        const precio = parseFloat(document.getElementById('vendedor_pos_precio_seleccionado').value) || 0;
        const stockMax = parseInt(document.getElementById('vendedor_pos_stock_seleccionado').value) || 0;
        const cantidadSolicitada = parseInt(document.getElementById('vendedor_pos_cantidad').value) || 1;

        if (cantidadSolicitada <= 0) {
            mostrarNotificacionPOS('La cantidad a vender debe ser de al menos 1 unidad.', 'warning');
            return;
        }

        if (stockMax <= 0) {
            mostrarNotificacionPOS('Este producto no tiene existencias disponibles en inventario.', 'error');
            return;
        }

        const indexExistente = vendedorCarritoVenta.findIndex(item => item.id_producto === idProducto);

        if (indexExistente !== -1) {
            const nuevaCantidad = vendedorCarritoVenta[indexExistente].cantidad + cantidadSolicitada;
            if (nuevaCantidad > stockMax) {
                mostrarNotificacionPOS(`No puedes agregar ${cantidadSolicitada} unidad(es) más. Stock disponible: ${stockMax} (Ya tienes ${vendedorCarritoVenta[indexExistente].cantidad} en la venta).`, 'warning');
                return;
            }
            vendedorCarritoVenta[indexExistente].cantidad = nuevaCantidad;
        } else {
            if (cantidadSolicitada > stockMax) {
                mostrarNotificacionPOS(`Stock insuficiente. Solo hay ${stockMax} unidad(es) disponibles.`, 'warning');
                return;
            }
            vendedorCarritoVenta.push({
                id_producto: idProducto,
                nombre: nombre,
                precio_unitario: precio,
                cantidad: cantidadSolicitada,
                stock_max: stockMax
            });
        }

        limpiarSeleccionProductoVendedor();
        renderizarCarritoVendedor();
    }

    function cambiarCantidadItemVendedor(index, nuevaCantidad) {
        let cant = parseInt(nuevaCantidad);
        if (isNaN(cant) || cant < 1) cant = 1;
        
        const item = vendedorCarritoVenta[index];
        if (item) {
            if (cant > item.stock_max) {
                mostrarNotificacionPOS(`Stock máximo disponible: ${item.stock_max} unidades.`, 'warning');
                cant = item.stock_max;
            }
            item.cantidad = cant;
            renderizarCarritoVendedor();
        }
    }

    function modificarCantidadItemDeltaVendedor(index, delta) {
        const item = vendedorCarritoVenta[index];
        if (item) {
            let nuevaCant = item.cantidad + delta;
            if (nuevaCant < 1) nuevaCant = 1;
            if (nuevaCant > item.stock_max) {
                mostrarNotificacionPOS(`Stock máximo disponible: ${item.stock_max} unidades.`, 'warning');
                nuevaCant = item.stock_max;
            }
            item.cantidad = nuevaCant;
            renderizarCarritoVendedor();
        }
    }

    function eliminarDelCarritoVendedor(index) {
        vendedorCarritoVenta.splice(index, 1);
        renderizarCarritoVendedor();
    }

    function renderizarCarritoVendedor() {
        const tbody = document.getElementById('vendedor_carrito_tbody');
        const hiddenContainer = document.getElementById('vendedor_hidden_items_inputs');
        const totalTexto = document.getElementById('vendedor_venta_total_preview');
        const resumenCount = document.getElementById('vendedor_resumen_items_count');
        const btnSubmit = document.getElementById('vendedor_btn_confirmar_venta');

        tbody.innerHTML = '';
        hiddenContainer.innerHTML = '';

        if (vendedorCarritoVenta.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                        <i class="bi bi-cart-x text-2xl block mb-1 text-slate-300"></i>
                        Aún no has agregado ningún producto a esta venta.
                    </td>
                </tr>
            `;
            if (totalTexto) totalTexto.textContent = '$0.00';
            if (resumenCount) resumenCount.textContent = '0 productos';
            if (btnSubmit) btnSubmit.disabled = true;
            return;
        }

        if (btnSubmit) btnSubmit.disabled = false;
        let totalDinero = 0;
        let totalUnidades = 0;

        vendedorCarritoVenta.forEach((item, index) => {
            const subtotal = item.cantidad * item.precio_unitario;
            totalDinero += subtotal;
            totalUnidades += item.cantidad;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';
            tr.innerHTML = `
                <td class="px-3 py-2 font-bold text-slate-900 align-middle">
                    <span class="block truncate max-w-[150px] sm:max-w-[190px]">${item.nombre}</span>
                    <span class="block text-[10px] text-slate-400 font-normal">Disp: ${item.stock_max}</span>
                </td>
                <td class="px-2 py-2 align-middle text-center">
                    <div class="inline-flex items-center border border-slate-200 rounded-lg overflow-hidden bg-white shadow-2xs">
                        <button type="button" onclick="modificarCantidadItemDeltaVendedor(${index}, -1)" class="px-2 py-0.5 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold cursor-pointer transition-colors">-</button>
                        <input type="number" min="1" max="${item.stock_max}" value="${item.cantidad}"
                            onchange="cambiarCantidadItemVendedor(${index}, this.value)"
                            class="w-10 text-center text-xs font-bold text-slate-900 py-0.5 focus:outline-none bg-transparent">
                        <button type="button" onclick="modificarCantidadItemDeltaVendedor(${index}, 1)" class="px-2 py-0.5 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold cursor-pointer transition-colors">+</button>
                    </div>
                </td>
                <td class="px-2 py-2 text-right font-medium text-slate-600 align-middle">
                    $${item.precio_unitario.toLocaleString('es-CO')}
                </td>
                <td class="px-3 py-2 text-right font-black text-slate-900 align-middle">
                    $${subtotal.toLocaleString('es-CO')}
                </td>
                <td class="px-2 py-2 text-center align-middle">
                    <button type="button" onclick="eliminarDelCarritoVendedor(${index})"
                        class="w-7 h-7 rounded-lg text-rose-500 hover:text-white hover:bg-rose-600 flex items-center justify-center transition-colors cursor-pointer mx-auto"
                        title="Eliminar de la venta">
                        <i class="bi bi-trash3-fill text-xs"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);

            hiddenContainer.innerHTML += `
                <input type="hidden" name="items[${index}][id_producto]" value="${item.id_producto}">
                <input type="hidden" name="items[${index}][cantidad]" value="${item.cantidad}">
                <input type="hidden" name="items[${index}][precio_unitario]" value="${item.precio_unitario}">
            `;
        });

        if (resumenCount) resumenCount.textContent = `${vendedorCarritoVenta.length} producto(s) (${totalUnidades} unidades)`;
        if (totalTexto) totalTexto.textContent = '$' + totalDinero.toLocaleString('es-CO');
    }

    function validarEnvioVentaVendedor() {
        if (vendedorCarritoVenta.length === 0) {
            mostrarNotificacionPOS('Debes agregar al menos un producto al carrito de venta antes de confirmar.', 'warning');
            return false;
        }
        return true;
    }

    function abrirModalEliminarVentaVendedor(id, total, cliente) {
        document.getElementById('vendedor_eliminar_id_txt').textContent = id;
        document.getElementById('vendedor_eliminar_cliente_txt').textContent = cliente;
        document.getElementById('vendedor_eliminar_total_txt').textContent = '$' + total;
        document.getElementById('formEliminarVentaVendedor').action = `/vendedor/ventas/${id}`;
        document.getElementById('modalEliminarVentaVendedor').classList.remove('hidden');
    }

    function cerrarModalEliminarVentaVendedor() {
        document.getElementById('modalEliminarVentaVendedor').classList.add('hidden');
    }
</script>
