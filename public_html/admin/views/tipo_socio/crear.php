<?php
$pageTitle = 'Nuevo Tipo de Socio';
include '../../partials/header.php';
include '../../partials/sidebar.php';
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Tipos de Socio</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Nuevo Tipo</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Nuevo Tipo de Socio</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formTipoSocio" onsubmit="return guardarTipoSocio(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre del Tipo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Silver, Gold, Platinum">
                            <p class="mt-1 text-xs text-gray-500">Ejemplos: Silver, Gold, Platinum, VIP</p>
                        </div>

                        <div>
                            <label for="desc_dulces" class="block text-sm font-medium text-gray-700 mb-1">
                                Descuento en Dulces (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="desc_dulces" 
                                   name="desc_dulces" 
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   value="0"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00">
                        </div>

                        <div>
                            <label for="desc_boleto" class="block text-sm font-medium text-gray-700 mb-1">
                                Descuento en Boletos (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="desc_boleto" 
                                   name="desc_boleto" 
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   value="0"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0.00">
                        </div>

                        <div class="md:col-span-2">
                            <label for="puntos_por_sol" class="block text-sm font-medium text-gray-700 mb-1">
                                Puntos por Sol Gastado <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="puntos_por_sol" 
                                   name="puntos_por_sol" 
                                   step="0.01"
                                   min="0"
                                   value="1"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="1.00">
                            <p class="mt-1 text-xs text-gray-500">Cantidad de puntos que gana por cada sol gastado (Ej: 1.00, 1.50, 2.00)</p>
                        </div>
                    </div>

                    <!-- Info de beneficios -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">
                            <i class="fas fa-info-circle"></i> Ejemplos de Configuracion
                        </h3>
                        <div class="text-xs text-blue-800 space-y-1">
                            <p><strong>Silver:</strong> 5% dulces, 0% boletos, 1 punto/sol</p>
                            <p><strong>Gold:</strong> 10% dulces, 5% boletos, 1.5 puntos/sol</p>
                            <p><strong>Platinum:</strong> 15% dulces, 10% boletos, 2 puntos/sol</p>
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
            function guardarTipoSocio(event) {
                event.preventDefault();
                
                const nombre = document.getElementById('nombre').value.trim();
                const descDulces = parseFloat(document.getElementById('desc_dulces').value);
                const descBoleto = parseFloat(document.getElementById('desc_boleto').value);
                const puntosPorSol = parseFloat(document.getElementById('puntos_por_sol').value);
                
                if (!nombre) {
                    showAlert('El nombre del tipo de socio es requerido', 'error');
                    return false;
                }

                if (descDulces < 0 || descDulces > 100) {
                    showAlert('El descuento en dulces debe estar entre 0 y 100', 'error');
                    return false;
                }

                if (descBoleto < 0 || descBoleto > 100) {
                    showAlert('El descuento en boletos debe estar entre 0 y 100', 'error');
                    return false;
                }

                if (puntosPorSol < 0) {
                    showAlert('Los puntos por sol deben ser mayor o igual a 0', 'error');
                    return false;
                }
                
                const data = { 
                    nombre: nombre,
                    desc_dulces: descDulces,
                    desc_boleto: descBoleto,
                    puntos_por_sol: puntosPorSol
                };
                
                fetch(`${API_BASE}/tipo_socio_api.php`, {
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
