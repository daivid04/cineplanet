<?php
$pageTitle = 'Nuevo Combo';
include '../../partials/header.php';
include '../../partials/sidebar.php';
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Combos</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Nuevo Combo</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Nuevo Combo</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formCombo" onsubmit="return guardarCombo(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre del Combo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Combo Familiar, Combo Pareja, Combo Individual">
                            <p class="mt-1 text-xs text-gray-500">Ejemplos: Combo Familiar, Combo Pareja, Combo Dulce, Combo XL</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="precio" class="block text-sm font-medium text-gray-700 mb-1">
                                Precio del Combo (S/) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="precio" 
                                   name="precio" 
                                   step="0.01"
                                   min="0.01"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00">
                            <p class="mt-1 text-xs text-gray-500">Precio total del combo con descuento incluido</p>
                        </div>
                    </div>

                    <!-- Info de combos comunes -->
                    <div class="bg-pink-50 border border-pink-200 rounded-lg p-4 mb-4">
                        <h3 class="text-sm font-semibold text-pink-900 mb-2">
                            <i class="fas fa-lightbulb"></i> Combos Comunes
                        </h3>
                        <div class="text-xs text-pink-800 space-y-1">
                            <p><strong>Combo Individual:</strong> Pop Corn Mediano + Gaseosa 500ml (S/ 18.00)</p>
                            <p><strong>Combo Pareja:</strong> Pop Corn Grande + 2 Gaseosas 500ml (S/ 28.00)</p>
                            <p><strong>Combo Familiar:</strong> 2 Pop Corn Grande + 4 Gaseosas 500ml (S/ 45.00)</p>
                            <p><strong>Combo Dulce:</strong> Pop Corn + Gaseosa + Chocolate (S/ 22.00)</p>
                            <p><strong>Combo XL:</strong> Pop Corn XL + 2 Gaseosas 1L + Nachos (S/ 50.00)</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">
                            <i class="fas fa-info-circle"></i> Nota Importante
                        </h3>
                        <p class="text-xs text-blue-800">
                            Los productos que componen este combo se asignarán posteriormente en la gestión de productos del combo.
                            Aquí solo defines el nombre y precio total del combo.
                        </p>
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
            function guardarCombo(event) {
                event.preventDefault();
                
                const nombre = document.getElementById('nombre').value.trim();
                const precio = parseFloat(document.getElementById('precio').value);
                
                if (!nombre) {
                    showAlert('El nombre del combo es requerido', 'error');
                    return false;
                }

                if (isNaN(precio) || precio <= 0) {
                    showAlert('El precio debe ser mayor a 0', 'error');
                    return false;
                }
                
                const data = { 
                    nombre: nombre,
                    precio: precio
                };
                
                fetch(`${API_BASE}/combo_api.php`, {
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
