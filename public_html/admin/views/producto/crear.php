<?php
$pageTitle = 'Nuevo Producto';
include '../../partials/header.php';
include '../../partials/sidebar.php';
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Productos</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Nuevo Producto</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Nuevo Producto</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formProducto" onsubmit="return guardarProducto(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre del Producto <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Pop Corn Grande, Gaseosa 500ml, Nachos">
                            <p class="mt-1 text-xs text-gray-500">Ejemplos: Pop Corn Grande, Gaseosa 500ml, Hot Dog, Nachos con Queso</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="precio_unitario" class="block text-sm font-medium text-gray-700 mb-1">
                                Precio Unitario (S/) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="precio_unitario" 
                                   name="precio_unitario" 
                                   step="0.01"
                                   min="0.01"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00">
                            <p class="mt-1 text-xs text-gray-500">Precio por unidad del producto</p>
                        </div>
                    </div>

                    <!-- Info de productos comunes -->
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4">
                        <h3 class="text-sm font-semibold text-indigo-900 mb-2">
                            <i class="fas fa-lightbulb"></i> Productos Comunes
                        </h3>
                        <div class="text-xs text-indigo-800 space-y-1">
                            <p><strong>Bebidas:</strong> Gaseosa (S/ 5.00 - S/ 8.00), Agua (S/ 3.00)</p>
                            <p><strong>Snacks:</strong> Pop Corn (S/ 10.00 - S/ 15.00), Nachos (S/ 12.00)</p>
                            <p><strong>Comida:</strong> Hot Dog (S/ 8.00), Hamburguesa (S/ 15.00)</p>
                            <p><strong>Dulces:</strong> Chocolates (S/ 4.00), Gomitas (S/ 3.00)</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Guardar
                        </button>
                        <a href="index.php" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </main>

        <script>
            function guardarProducto(event) {
                event.preventDefault();
                
                const nombre = document.getElementById('nombre').value.trim();
                const precioUnitario = parseFloat(document.getElementById('precio_unitario').value);
                
                if (!nombre) {
                    showAlert('El nombre del producto es requerido', 'error');
                    return false;
                }

                if (isNaN(precioUnitario) || precioUnitario <= 0) {
                    showAlert('El precio unitario debe ser mayor a 0', 'error');
                    return false;
                }
                
                const data = { 
                    nombre: nombre,
                    precio_unitario: precioUnitario
                };
                
                fetch(`${API_BASE}/producto_api.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => window.location.href = 'index.php', 1000);
                    } else {
                        showAlert(data.message || 'Error al guardar', 'error');
                    }
                })
                .catch(error => {
                    showAlert('Error de conexion', 'error');
                    console.error(error);
                });
                
                return false;
            }
        </script>

<?php include '../../partials/footer.php'; ?>
