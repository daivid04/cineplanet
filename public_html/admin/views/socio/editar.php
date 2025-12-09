<?php
$pageTitle = 'Editar Socio';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SocioAdminModel.php';

$model = new SocioAdminModel($conn);

// Verificar ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: index.php');
    exit;
}

$socio = $model->getById($id);
if (!$socio) {
    header('Location: index.php');
    exit;
}

$tiposSocio = $model->getTiposSocio(false); // Todos para permitir ver inactivos
$comprasCount = $model->countCompras($id);
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Editar Socio</h1>
                    <p class="text-gray-600">
                        Editando: <strong><?php echo htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido']); ?></strong>
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="ver.php?id=<?php echo $id; ?>" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-eye"></i>
                        Ver Detalle
                    </a>
                    <a href="index.php" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </a>
                </div>
            </div>

            <?php if ($comprasCount > 0): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                    <p class="text-yellow-700">
                        Este socio tiene <strong><?php echo $comprasCount; ?></strong> compra(s) registrada(s).
                        Algunos cambios pueden afectar el historial.
                    </p>
                </div>
            </div>
            <?php endif; ?>

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
                                       value="<?php echo htmlspecialchars($socio['correo']); ?>"
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="ejemplo@correo.com">
                            </div>
                            <div>
                                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                                    Estado
                                </label>
                                <div class="flex items-center mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $socio['estado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <i class="fas <?php echo $socio['estado'] == 1 ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-1"></i>
                                        <?php echo $socio['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                    <button type="button" onclick="toggleEstadoSocio()" 
                                            class="ml-3 text-sm text-blue-600 hover:text-blue-800">
                                        Cambiar estado
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Cambiar Contraseña -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-lock mr-2 text-red-600"></i>
                            Cambiar Contraseña
                            <span class="text-sm font-normal text-gray-500">(opcional)</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nueva Contraseña
                                </label>
                                <div class="relative">
                                    <input type="password" id="contrasena" name="contrasena" minlength="6"
                                           class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Dejar vacío para no cambiar">
                                    <button type="button" onclick="togglePassword()" 
                                            class="absolute right-3 top-2 text-gray-500 hover:text-gray-700">
                                        <i id="iconPassword" class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Mínimo 6 caracteres si desea cambiar</p>
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
                                       value="<?php echo htmlspecialchars($socio['nombre']); ?>"
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="apellido" class="block text-sm font-medium text-gray-700 mb-1">
                                    Apellido <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="apellido" name="apellido" required maxlength="50"
                                       value="<?php echo htmlspecialchars($socio['apellido']); ?>"
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="documento" class="block text-sm font-medium text-gray-700 mb-1">
                                    Documento (DNI) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="documento" name="documento" required 
                                       pattern="[0-9]{8}" maxlength="8"
                                       value="<?php echo htmlspecialchars($socio['documento']); ?>"
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="genero" class="block text-sm font-medium text-gray-700 mb-1">
                                    Género <span class="text-red-500">*</span>
                                </label>
                                <select id="genero" name="genero" required
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="Masculino" <?php echo $socio['genero'] === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                    <option value="Femenino" <?php echo $socio['genero'] === 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                                    <option value="Otro" <?php echo $socio['genero'] === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>
                            <div>
                                <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha de Nacimiento <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required
                                       value="<?php echo date('Y-m-d', strtotime($socio['fecha_nacimiento'])); ?>"
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-xs text-gray-500">
                                    Edad actual: <strong><?php echo $socio['edad']; ?> años</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Membresía -->
                    <div class="pb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-crown mr-2 text-yellow-600"></i>
                            Tipo de Membresía
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php foreach ($tiposSocio as $tipo): ?>
                                <label class="cursor-pointer <?php echo $tipo['estado'] != 1 ? 'opacity-50' : ''; ?>">
                                    <input type="radio" name="id_tipo_socio" value="<?php echo $tipo['id_socio']; ?>" 
                                           class="sr-only peer" required
                                           <?php echo $socio['id_tipo_socio'] == $tipo['id_socio'] ? 'checked' : ''; ?>
                                           <?php echo $tipo['estado'] != 1 ? 'disabled' : ''; ?>>
                                    <div class="border-2 rounded-lg p-4 transition-all
                                                peer-checked:border-blue-500 peer-checked:bg-blue-50
                                                hover:border-gray-400
                                                <?php echo $tipo['estado'] != 1 ? 'border-dashed' : ''; ?>">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-medium text-gray-900">
                                                <?php echo htmlspecialchars($tipo['nombre']); ?>
                                                <?php if ($tipo['estado'] != 1): ?>
                                                    <span class="text-xs text-red-500">(Inactivo)</span>
                                                <?php endif; ?>
                                            </h4>
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
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4 pt-4 border-t">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Guardar Cambios
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

    <!-- Modal de confirmación -->
    <div id="modalConfirm" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <i class="fas fa-question-circle text-4xl text-blue-500 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2" id="modalTitle">Confirmar</h3>
                <p class="text-gray-500 mb-6" id="modalMessage">¿Estás seguro?</p>
            </div>
            <div class="flex gap-3 justify-center">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancelar
                </button>
                <button id="btnConfirm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '../../api/socio_admin_api.php';
        const SOCIO_ID = <?php echo $id; ?>;
        const ESTADO_ACTUAL = <?php echo $socio['estado']; ?>;

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

        // Toggle estado
        function toggleEstadoSocio() {
            const nuevoEstado = ESTADO_ACTUAL == 1 ? 'inactivo' : 'activo';
            showModal(
                'Cambiar estado',
                `¿Deseas cambiar el estado a "${nuevoEstado}"?`,
                async () => {
                    try {
                        const response = await fetch(`${API_URL}?id=${SOCIO_ID}&action=toggle`, {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json' }
                        });
                        const data = await response.json();
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    } catch (error) {
                        alert('Error al cambiar estado');
                    }
                }
            );
        }

        // Enviar formulario
        document.getElementById('formSocio').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = {
                correo: document.getElementById('correo').value.trim(),
                nombre: document.getElementById('nombre').value.trim(),
                apellido: document.getElementById('apellido').value.trim(),
                documento: document.getElementById('documento').value,
                genero: document.getElementById('genero').value,
                fecha_nacimiento: document.getElementById('fecha_nacimiento').value,
                id_tipo_socio: document.querySelector('input[name="id_tipo_socio"]:checked')?.value
            };

            // Si hay contraseña, agregarla
            const contrasena = document.getElementById('contrasena').value;
            if (contrasena) {
                if (contrasena.length < 6) {
                    alert('La contraseña debe tener al menos 6 caracteres');
                    return;
                }
                formData.contrasena = contrasena;
            }

            if (formData.documento.length !== 8) {
                alert('El documento debe tener exactamente 8 dígitos');
                return;
            }

            try {
                const response = await fetch(`${API_URL}?id=${SOCIO_ID}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    alert('Socio actualizado correctamente');
                    window.location.href = 'index.php';
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al actualizar el socio');
            }
        });

        function showModal(title, message, onConfirm) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('btnConfirm').onclick = () => {
                closeModal();
                if (onConfirm) onConfirm();
            };
            document.getElementById('modalConfirm').classList.remove('hidden');
            document.getElementById('modalConfirm').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('modalConfirm').classList.add('hidden');
            document.getElementById('modalConfirm').classList.remove('flex');
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>

<?php include '../../partials/footer.php'; ?>
