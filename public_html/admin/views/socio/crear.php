<?php
$pageTitle = 'Crear Socio';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SocioAdminModel.php';

$model = new SocioAdminModel($conn);
$tiposSocio = $model->getTiposSocio(true); // Solo activos
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Crear Nuevo Socio</h1>
                    <p class="text-gray-600">Complete el formulario para registrar un nuevo socio</p>
                </div>
                <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Volver
                </a>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6">
                <form id="formSocio" class="space-y-6">
                    <!-- Sección: Datos de Cuenta -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                            Datos de Cuenta
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">
                                    Correo Electrónico <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="correo" name="correo" required
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="ejemplo@correo.com">
                            </div>
                            <div>
                                <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1">
                                    Contraseña <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="contrasena" name="contrasena" required minlength="6"
                                           class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Mínimo 6 caracteres">
                                    <button type="button" onclick="togglePassword()" 
                                            class="absolute right-3 top-2 text-gray-500 hover:text-gray-700">
                                        <i id="iconPassword" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Datos Personales -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-id-card mr-2 text-green-600"></i>
                            Datos Personales
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nombre" name="nombre" required maxlength="50"
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Nombre del socio">
                            </div>
                            <div>
                                <label for="apellido" class="block text-sm font-medium text-gray-700 mb-1">
                                    Apellido <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="apellido" name="apellido" required maxlength="50"
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Apellido del socio">
                            </div>
                            <div>
                                <label for="documento" class="block text-sm font-medium text-gray-700 mb-1">
                                    Documento (DNI) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="documento" name="documento" required 
                                       pattern="[0-9]{8}" maxlength="8"
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="8 dígitos numéricos">
                                <p class="mt-1 text-xs text-gray-500">Ingrese exactamente 8 dígitos</p>
                            </div>
                            <div>
                                <label for="genero" class="block text-sm font-medium text-gray-700 mb-1">
                                    Género <span class="text-red-500">*</span>
                                </label>
                                <select id="genero" name="genero" required
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Seleccione género</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div>
                                <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha de Nacimiento <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-xs text-gray-500">El socio debe tener al menos 13 años</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Membresía -->
                    <div class="pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-crown mr-2 text-yellow-600"></i>
                            Tipo de Membresía
                        </h3>
                        
                        <?php if (empty($tiposSocio)): ?>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-yellow-700">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    No hay tipos de socio activos. 
                                    <a href="../tipo_socio/crear.php" class="underline">Crear uno primero</a>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <?php foreach ($tiposSocio as $tipo): ?>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="id_tipo_socio" value="<?php echo $tipo['id_socio']; ?>" 
                                               class="sr-only peer" required>
                                        <div class="border-2 rounded-lg p-4 transition-all
                                                    peer-checked:border-blue-500 peer-checked:bg-blue-50
                                                    hover:border-gray-400">
                                            <div class="flex items-center justify-between mb-2">
                                                <h4 class="font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                                                </h4>
                                                <i class="fas fa-check-circle text-blue-500 hidden peer-checked:block"></i>
                                            </div>
                                            <div class="text-sm text-gray-600 space-y-1">
                                                <p>
                                                    <i class="fas fa-candy-cane mr-1 text-pink-500"></i>
                                                    Desc. Dulcería: <strong><?php echo $tipo['desc_dulces']; ?>%</strong>
                                                </p>
                                                <p>
                                                    <i class="fas fa-ticket-alt mr-1 text-blue-500"></i>
                                                    Desc. Boletos: <strong><?php echo $tipo['desc_boleto']; ?>%</strong>
                                                </p>
                                                <p>
                                                    <i class="fas fa-coins mr-1 text-yellow-500"></i>
                                                    Puntos/Sol: <strong><?php echo $tipo['puntos_por_sol']; ?></strong>
                                                </p>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4 pt-4 border-t">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center gap-2"
                                <?php echo empty($tiposSocio) ? 'disabled' : ''; ?>>
                            <i class="fas fa-save"></i>
                            Guardar Socio
                        </button>
                        <a href="index.php" 
                           class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        const API_URL = '../../api/socio_admin_api.php';

        function togglePassword() {
            const input = document.getElementById('contrasena');
            const icon = document.getElementById('iconPassword');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validar DNI solo números
        document.getElementById('documento').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);
        });

        // Validar fecha máxima (13 años atrás)
        const fechaInput = document.getElementById('fecha_nacimiento');
        const hoy = new Date();
        const fechaMax = new Date(hoy.getFullYear() - 13, hoy.getMonth(), hoy.getDate());
        fechaInput.max = fechaMax.toISOString().split('T')[0];
        
        const fechaMin = new Date(hoy.getFullYear() - 120, hoy.getMonth(), hoy.getDate());
        fechaInput.min = fechaMin.toISOString().split('T')[0];

        // Enviar formulario
        document.getElementById('formSocio').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = {
                correo: document.getElementById('correo').value.trim(),
                contrasena: document.getElementById('contrasena').value,
                nombre: document.getElementById('nombre').value.trim(),
                apellido: document.getElementById('apellido').value.trim(),
                documento: document.getElementById('documento').value,
                genero: document.getElementById('genero').value,
                fecha_nacimiento: document.getElementById('fecha_nacimiento').value,
                id_tipo_socio: document.querySelector('input[name="id_tipo_socio"]:checked')?.value
            };

            // Validaciones adicionales
            if (!formData.id_tipo_socio) {
                alert('Debe seleccionar un tipo de membresía');
                return;
            }

            if (formData.documento.length !== 8) {
                alert('El documento debe tener exactamente 8 dígitos');
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
                    alert('Socio creado correctamente');
                    window.location.href = 'index.php';
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al crear el socio');
            }
        });
    </script>

<?php include '../../partials/footer.php'; ?>
