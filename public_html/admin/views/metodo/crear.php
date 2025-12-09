<?php
$pageTitle = 'Nuevo Metodo de Pago';
include '../../partials/header.php';
include '../../partials/sidebar.php';
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Metodos de Pago</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Nuevo Metodo</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Nuevo Metodo de Pago</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formMetodo" onsubmit="return guardarMetodo(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="md:col-span-2">
                            <label for="nombre_metodo" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre del Metodo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre_metodo" 
                                   name="nombre_metodo" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Tarjeta de Credito">
                        </div>

                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                                Descripcion
                            </label>
                            <textarea id="descripcion" 
                                      name="descripcion" 
                                      rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Ej: Pago con tarjeta de credito Visa/Mastercard"></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label for="icono" class="block text-sm font-medium text-gray-700 mb-1">
                                Icono (clase CSS)
                            </label>
                            <input type="text" 
                                   id="icono" 
                                   name="icono" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: credit-card, yape, plin">
                            <p class="mt-1 text-xs text-gray-500">Ejemplos: credit-card, debit-card, cash, yape, plin, bank-transfer</p>
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
            function guardarMetodo(event) {
                event.preventDefault();
                
                const nombre = document.getElementById('nombre_metodo').value.trim();
                const descripcion = document.getElementById('descripcion').value.trim();
                const icono = document.getElementById('icono').value.trim();
                
                if (!nombre) {
                    showAlert('El nombre del metodo es requerido', 'error');
                    return false;
                }
                
                const data = { nombre_metodo: nombre };
                if (descripcion) data.descripcion = descripcion;
                if (icono) data.icono = icono;
                
                fetch(`${API_BASE}/metodo_api.php`, {
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
