<?php
$pageTitle = 'Composición de Combos';
include '../../partials/header.php';
include '../../partials/sidebar.php';
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Composición de Combos</h1>
                    <p class="text-gray-600">Gestiona los productos que componen cada combo</p>
                </div>
            </div>

            <!-- Mensajes -->
            <div id="alertContainer"></div>

            <!-- Lista de Combos -->
            <div id="combosContainer" class="space-y-4">
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Cargando combos...</p>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para agregar productos -->
    <div id="modalAgregarProductos" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden">
            <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Agregar Productos a: <span id="comboNombre"></span>
                </h3>
                <button onclick="cerrarModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto" style="max-height: 60vh;">
                <input type="hidden" id="comboIdActual">
                <p class="text-gray-600 mb-4">Seleccione productos para agregar:</p>
                <div id="productosDisponiblesContainer" class="space-y-3">
                    <p class="text-center text-gray-500">Cargando productos...</p>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button onclick="cerrarModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
                    Cancelar
                </button>
                <button onclick="agregarProductosSeleccionados()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    <i class="fas fa-plus-circle mr-1"></i>Agregar Seleccionados
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación para eliminar -->
    <div id="modalConfirmDelete" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="bg-red-600 text-white px-6 py-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Confirmar Eliminación
                </h3>
            </div>
            <div class="p-6">
                <p>¿Está seguro que desea quitar el producto <strong id="productoNombreDelete"></strong> del combo?</p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button onclick="cerrarModalDelete()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
                    Cancelar
                </button>
                <button id="btnConfirmDelete" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                    <i class="fas fa-trash mr-1"></i>Eliminar
                </button>
            </div>
        </div>
    </div>

<?php include '../../partials/footer.php'; ?>

<script>
    const API_URL = '../../api/producto_combo_admin_api.php';
    let deleteRelacionId = null;

    document.addEventListener('DOMContentLoaded', function() {
        cargarCombos();
    });

    function cargarCombos() {
        fetch(`${API_URL}?action=getCombosConProductos`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderCombos(data.data);
                } else {
                    showAlert('error', data.message || 'Error al cargar combos');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error de conexión al servidor');
            });
    }

    function renderCombos(combos) {
        const container = document.getElementById('combosContainer');
        
        if (!combos || combos.length === 0) {
            container.innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                    <i class="fas fa-info-circle text-blue-500 text-2xl mb-2"></i>
                    <p class="text-blue-700">No hay combos registrados en el sistema.</p>
                </div>
            `;
            return;
        }

        let html = '';
        combos.forEach(combo => {
            const productos = combo.productos || [];
            const estadoClass = combo.estado == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            const estadoText = combo.estado == 1 ? 'Activo' : 'Inactivo';
            
            html += `
                <div class="bg-white rounded-lg shadow overflow-hidden border-l-4 border-blue-500 hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-gifts text-pink-600"></i>
                                    ${escapeHtml(combo.nombre)}
                                </h3>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-800 font-semibold">
                                        S/ ${parseFloat(combo.precio).toFixed(2)}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full ${estadoClass}">
                                        ${estadoText}
                                    </span>
                                    <span class="text-gray-500">
                                        <i class="fas fa-box mr-1"></i>${productos.length} producto(s)
                                    </span>
                                </div>
                            </div>
                            <button onclick="abrirModalAgregar(${combo.id_combo}, '${escapeHtml(combo.nombre)}')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm">
                                <i class="fas fa-plus"></i>Agregar Productos
                            </button>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            ${productos.length > 0 ? productos.map(p => `
                                <span class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-tag text-gray-500"></i>
                                    <span>${escapeHtml(p.producto_nombre)}</span>
                                    <span class="text-xs text-gray-500">(${escapeHtml(p.sede_nombre)})</span>
                                    <button onclick="confirmarEliminar(${p.id_productos_combos}, '${escapeHtml(p.producto_nombre)}')"
                                            class="text-red-500 hover:text-red-700 ml-1" title="Quitar del combo">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </span>
                            `).join('') : '<span class="text-gray-400 italic">Este combo no tiene productos asignados</span>'}
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function abrirModalAgregar(idCombo, nombreCombo) {
        document.getElementById('comboIdActual').value = idCombo;
        document.getElementById('comboNombre').textContent = nombreCombo;
        cargarProductosDisponibles(idCombo);
        document.getElementById('modalAgregarProductos').classList.remove('hidden');
        document.getElementById('modalAgregarProductos').classList.add('flex');
    }

    function cerrarModal() {
        document.getElementById('modalAgregarProductos').classList.add('hidden');
        document.getElementById('modalAgregarProductos').classList.remove('flex');
    }

    function cargarProductosDisponibles(idCombo) {
        const container = document.getElementById('productosDisponiblesContainer');
        container.innerHTML = '<p class="text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando productos...</p>';

        fetch(`${API_URL}?action=getProductosDisponibles&id_combo=${idCombo}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderProductosDisponibles(data.data);
                } else {
                    container.innerHTML = '<p class="text-center text-red-500">Error al cargar productos</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<p class="text-center text-red-500">Error de conexión</p>';
            });
    }

    function renderProductosDisponibles(productos) {
        const container = document.getElementById('productosDisponiblesContainer');
        
        if (!productos || productos.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500">No hay productos disponibles para agregar</p>';
            return;
        }

        // Agrupar por sede
        const porSede = {};
        productos.forEach(p => {
            const sedeNombre = p.sede_nombre || 'Sin Sede';
            if (!porSede[sedeNombre]) {
                porSede[sedeNombre] = [];
            }
            porSede[sedeNombre].push(p);
        });

        let html = '';
        for (const sede in porSede) {
            html += `
                <div class="mb-4">
                    <h4 class="font-semibold text-blue-600 border-b border-blue-200 pb-2 mb-3">
                        <i class="fas fa-building mr-2"></i>${escapeHtml(sede)}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            `;
            porSede[sede].forEach(p => {
                html += `
                    <label class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors">
                        <input type="checkbox" class="producto-check w-5 h-5 text-blue-600 rounded" value="${p.id_producto_sede}">
                        <div class="flex-1">
                            <span class="font-medium">${escapeHtml(p.producto_nombre)}</span>
                            <span class="text-sm text-gray-500 ml-2">S/ ${parseFloat(p.precio_unitario).toFixed(2)}</span>
                        </div>
                    </label>
                `;
            });
            html += '</div></div>';
        }

        container.innerHTML = html;
    }

    function agregarProductosSeleccionados() {
        const idCombo = document.getElementById('comboIdActual').value;
        const checkboxes = document.querySelectorAll('.producto-check:checked');
        
        if (checkboxes.length === 0) {
            showAlert('warning', 'Seleccione al menos un producto');
            return;
        }

        const productos = Array.from(checkboxes).map(cb => parseInt(cb.value));

        fetch(`${API_URL}?action=addMultiples`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_combo: parseInt(idCombo),
                productos: productos
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message || 'Productos agregados correctamente');
                cerrarModal();
                cargarCombos();
            } else {
                showAlert('error', data.message || 'Error al agregar productos');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error de conexión');
        });
    }

    function confirmarEliminar(idRelacion, nombreProducto) {
        deleteRelacionId = idRelacion;
        document.getElementById('productoNombreDelete').textContent = nombreProducto;
        document.getElementById('modalConfirmDelete').classList.remove('hidden');
        document.getElementById('modalConfirmDelete').classList.add('flex');

        document.getElementById('btnConfirmDelete').onclick = function() {
            eliminarProductoDeCombo(deleteRelacionId);
        };
    }

    function cerrarModalDelete() {
        document.getElementById('modalConfirmDelete').classList.add('hidden');
        document.getElementById('modalConfirmDelete').classList.remove('flex');
    }

    function eliminarProductoDeCombo(idRelacion) {
        fetch(`${API_URL}?id=${idRelacion}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            cerrarModalDelete();
            
            if (data.success) {
                showAlert('success', 'Producto quitado del combo correctamente');
                cargarCombos();
            } else {
                showAlert('error', data.message || 'Error al eliminar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error de conexión');
        });
    }

    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const colors = {
            success: 'bg-green-100 border-green-500 text-green-700',
            error: 'bg-red-100 border-red-500 text-red-700',
            warning: 'bg-yellow-100 border-yellow-500 text-yellow-700'
        };
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle'
        };

        const alert = document.createElement('div');
        alert.className = `${colors[type]} border-l-4 p-4 mb-4 rounded-r-lg flex items-center justify-between`;
        alert.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${icons[type]} mr-3"></i>
                <span>${message}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        `;
        alertContainer.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
