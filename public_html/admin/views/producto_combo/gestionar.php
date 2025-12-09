<?php
$pageTitle = 'Gestionar Combo';
include '../../partials/header.php';
include '../../partials/sidebar.php';
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestionar Combo</h1>
                    <p class="text-gray-600" id="comboInfo">Seleccione un combo para gestionar sus productos</p>
                </div>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>

            <!-- Mensajes -->
            <div id="alertContainer"></div>

            <!-- Selector de Combo -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="selectCombo" class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar Combo:
                        </label>
                        <select id="selectCombo" onchange="cargarProductosCombo()"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Seleccione un combo --</option>
                        </select>
                    </div>
                    <div id="comboDetails" class="flex items-center text-gray-500">
                        <i class="fas fa-info-circle mr-2"></i>
                        Seleccione un combo para ver sus productos
                    </div>
                </div>
            </div>

            <!-- Contenedores de productos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Productos en el Combo -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-green-600 text-white px-6 py-4 flex justify-between items-center">
                        <h3 class="font-semibold flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            Productos en el Combo
                        </h3>
                        <span id="contadorEnCombo" class="bg-white text-green-600 px-3 py-1 rounded-full text-sm font-bold">0</span>
                    </div>
                    <div id="productosEnCombo" class="p-4 max-h-96 overflow-y-auto">
                        <p class="text-gray-500 text-center py-8">Seleccione un combo primero</p>
                    </div>
                </div>

                <!-- Productos Disponibles -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
                        <h3 class="font-semibold flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i>
                            Productos Disponibles
                        </h3>
                        <span id="contadorDisponibles" class="bg-white text-blue-600 px-3 py-1 rounded-full text-sm font-bold">0</span>
                    </div>
                    <div id="productosDisponibles" class="p-4 max-h-96 overflow-y-auto">
                        <p class="text-gray-500 text-center py-8">Seleccione un combo primero</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

<?php include '../../partials/footer.php'; ?>

<script>
    const API_URL = '../../api/producto_combo_admin_api.php';

    document.addEventListener('DOMContentLoaded', function() {
        cargarCombos();
        
        // Verificar si viene un ID de combo en la URL
        const urlParams = new URLSearchParams(window.location.search);
        const idCombo = urlParams.get('id');
        if (idCombo) {
            setTimeout(() => {
                document.getElementById('selectCombo').value = idCombo;
                cargarProductosCombo();
            }, 500);
        }
    });

    function cargarCombos() {
        fetch(`${API_URL}?action=getCombos`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('selectCombo');
                    data.data.forEach(combo => {
                        const option = document.createElement('option');
                        option.value = combo.id_combo;
                        option.textContent = `${combo.nombre} - S/ ${parseFloat(combo.precio).toFixed(2)}`;
                        option.dataset.precio = combo.precio;
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function cargarProductosCombo() {
        const idCombo = document.getElementById('selectCombo').value;
        
        if (!idCombo) {
            document.getElementById('productosEnCombo').innerHTML = '<p class="text-gray-500 text-center py-8">Seleccione un combo primero</p>';
            document.getElementById('productosDisponibles').innerHTML = '<p class="text-gray-500 text-center py-8">Seleccione un combo primero</p>';
            document.getElementById('comboDetails').innerHTML = '<i class="fas fa-info-circle mr-2"></i>Seleccione un combo para ver sus productos';
            document.getElementById('contadorEnCombo').textContent = '0';
            document.getElementById('contadorDisponibles').textContent = '0';
            return;
        }

        // Actualizar info del combo
        const selectOption = document.getElementById('selectCombo').selectedOptions[0];
        document.getElementById('comboInfo').textContent = selectOption.textContent;
        document.getElementById('comboDetails').innerHTML = `
            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 font-medium">
                <i class="fas fa-gifts mr-2"></i>${selectOption.textContent}
            </span>
        `;

        // Cargar productos en combo y disponibles en paralelo
        Promise.all([
            fetch(`${API_URL}?action=getByCombo&id_combo=${idCombo}`).then(r => r.json()),
            fetch(`${API_URL}?action=getProductosDisponibles&id_combo=${idCombo}`).then(r => r.json())
        ])
        .then(([enCombo, disponibles]) => {
            if (enCombo.success) {
                renderProductosEnCombo(enCombo.data);
            }
            if (disponibles.success) {
                renderProductosDisponibles(disponibles.data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error al cargar productos');
        });
    }

    function renderProductosEnCombo(productos) {
        const container = document.getElementById('productosEnCombo');
        document.getElementById('contadorEnCombo').textContent = productos.length;

        if (!productos || productos.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>Este combo no tiene productos</p>
                </div>
            `;
            return;
        }

        let html = '<div class="space-y-2">';
        productos.forEach(p => {
            html += `
                <div class="flex items-center justify-between p-3 bg-green-50 hover:bg-green-100 rounded-lg border-l-4 border-green-500 transition-colors">
                    <div>
                        <p class="font-medium text-gray-800">${escapeHtml(p.producto_nombre)}</p>
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-building mr-1"></i>${escapeHtml(p.sede_nombre)} - 
                            S/ ${parseFloat(p.precio_unitario).toFixed(2)}
                        </p>
                    </div>
                    <button onclick="quitarProducto(${p.id_productos_combos}, '${escapeHtml(p.producto_nombre)}')"
                            class="text-red-500 hover:text-red-700 hover:bg-red-100 p-2 rounded-lg transition-colors"
                            title="Quitar del combo">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });
        html += '</div>';

        container.innerHTML = html;
    }

    function renderProductosDisponibles(productos) {
        const container = document.getElementById('productosDisponibles');
        document.getElementById('contadorDisponibles').textContent = productos.length;

        if (!productos || productos.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-green-500">
                    <i class="fas fa-check-circle text-4xl mb-3"></i>
                    <p>Todos los productos ya están en el combo</p>
                </div>
            `;
            return;
        }

        // Agrupar por sede
        const porSede = {};
        productos.forEach(p => {
            const sede = p.sede_nombre || 'Sin Sede';
            if (!porSede[sede]) porSede[sede] = [];
            porSede[sede].push(p);
        });

        let html = '';
        for (const sede in porSede) {
            html += `
                <div class="mb-4">
                    <h4 class="font-semibold text-blue-600 border-b border-blue-200 pb-2 mb-2">
                        <i class="fas fa-building mr-2"></i>${escapeHtml(sede)}
                    </h4>
                    <div class="space-y-2">
            `;
            porSede[sede].forEach(p => {
                html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 hover:bg-blue-50 rounded-lg transition-colors">
                        <div>
                            <p class="font-medium text-gray-800">${escapeHtml(p.producto_nombre)}</p>
                            <p class="text-sm text-gray-500">S/ ${parseFloat(p.precio_unitario).toFixed(2)}</p>
                        </div>
                        <button onclick="agregarProducto(${p.id_producto_sede}, '${escapeHtml(p.producto_nombre)}')"
                                class="text-green-500 hover:text-green-700 hover:bg-green-100 p-2 rounded-lg transition-colors"
                                title="Agregar al combo">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                `;
            });
            html += '</div></div>';
        }

        container.innerHTML = html;
    }

    function agregarProducto(idProductoSede, nombreProducto) {
        const idCombo = document.getElementById('selectCombo').value;
        
        if (!idCombo) {
            showAlert('warning', 'Seleccione un combo primero');
            return;
        }

        fetch(`${API_URL}?action=addProducto`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_combo: parseInt(idCombo),
                id_producto_sede: idProductoSede
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', `Producto "${nombreProducto}" agregado al combo`);
                cargarProductosCombo();
            } else {
                showAlert('error', data.message || 'Error al agregar producto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Error de conexión');
        });
    }

    function quitarProducto(idRelacion, nombreProducto) {
        if (!confirm(`¿Quitar "${nombreProducto}" del combo?`)) {
            return;
        }

        fetch(`${API_URL}?id=${idRelacion}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', `Producto "${nombreProducto}" quitado del combo`);
                cargarProductosCombo();
            } else {
                showAlert('error', data.message || 'Error al quitar producto');
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
        }, 4000);
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
