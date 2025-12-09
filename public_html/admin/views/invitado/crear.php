<?php
$pageTitle = 'Crear Invitado';
include '../../partials/header.php';
include '../../partials/sidebar.php';
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Crear Nuevo Invitado</h1>
                    <p class="text-gray-600">Complete el formulario para registrar un nuevo invitado</p>
                </div>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>

            <!-- Info Panel -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <p class="text-blue-700 font-medium">Acerca de los Invitados</p>
                        <p class="text-blue-600 text-sm mt-1">
                            Los invitados son usuarios que realizan compras sin registrarse como socios. 
                            Solo necesitan un nombre y opcionalmente un correo electrónico. 
                            Un invitado puede ser convertido a socio posteriormente si desea obtener beneficios.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formInvitado" class="space-y-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre" required maxlength="100"
                               class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Nombre completo del invitado">
                        <p class="mt-1 text-xs text-gray-500">Este nombre aparecerá en los tickets y facturas</p>
                    </div>

                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">
                            Correo Electrónico
                            <span class="text-gray-400 text-xs">(opcional)</span>
                        </label>
                        <input type="email" id="correo" name="correo"
                               class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="ejemplo@correo.com">
                        <p class="mt-1 text-xs text-gray-500">Si se proporciona, se enviará confirmación de compras a este correo</p>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4 pt-4 border-t">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Guardar Invitado
                        </button>
                        <a href="index.php" 
                           class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Tip -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4 max-w-2xl">
                <div class="flex items-start">
                    <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3"></i>
                    <div>
                        <p class="text-yellow-700 font-medium">Consejo</p>
                        <p class="text-yellow-600 text-sm mt-1">
                            Si el cliente desea beneficios como descuentos y acumulación de puntos, 
                            considere crear un <a href="../socio/crear.php" class="underline font-medium">Socio</a> en su lugar.
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const API_URL = '../../api/invitado_admin_api.php';

        document.getElementById('formInvitado').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = {
                nombre: document.getElementById('nombre').value.trim(),
                correo: document.getElementById('correo').value.trim() || null
            };

            if (formData.nombre.length < 2) {
                alert('El nombre debe tener al menos 2 caracteres');
                return;
            }

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    alert('Invitado creado correctamente');
                    window.location.href = 'index.php';
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al crear el invitado');
            }
        });
    </script>

<?php include '../../partials/footer.php'; ?>
