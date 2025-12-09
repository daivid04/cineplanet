<?php
$pageTitle = 'Invitados';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/InvitadoAdminModel.php';

$model = new InvitadoAdminModel($conn);

// Filtros
$soloActivos = isset($_GET['activos']) && $_GET['activos'] === '1';
$busqueda = $_GET['busqueda'] ?? null;

$invitados = $model->getAll($soloActivos, $busqueda);
$estadisticas = $model->getEstadisticas();
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Invitados</h1>
                    <p class="text-gray-600">Gestiona los usuarios invitados del sistema</p>
                </div>
                <a href="crear.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Nuevo Invitado
                </a>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-user text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Invitados</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas['total'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-user-check text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Activos</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas['activos'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-envelope text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Con Correo</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas['con_correo'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-shopping-cart text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Con Compras</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas['con_compras'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select id="filtroActivos" onchange="aplicarFiltros()" 
                                class="border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos</option>
                            <option value="1" <?php echo $soloActivos ? 'selected' : ''; ?>>Solo activos</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                        <div class="relative">
                            <input type="text" id="busqueda" placeholder="Buscar por nombre o correo..." 
                                   value="<?php echo htmlspecialchars($busqueda ?? ''); ?>"
                                   onkeyup="debounceSearch()"
                                   class="w-full border rounded-lg px-3 py-2 pl-10 focus:ring-blue-500 focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="self-end">
                        <button onclick="limpiarFiltros()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                            <i class="fas fa-times mr-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de Invitados -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($invitados)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-user text-4xl mb-2"></i>
                                    <p>No se encontraron invitados</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($invitados as $invitado): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $invitado['id_usuario']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                                    <span class="text-yellow-600 font-medium text-sm">
                                                        <?php echo strtoupper(substr($invitado['nombre'], 0, 2)); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($invitado['nombre']); ?>
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    <i class="fas fa-user-tag mr-1"></i>Invitado
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if (!empty($invitado['correo'])): ?>
                                            <i class="fas fa-envelope mr-1 text-gray-400"></i>
                                            <?php echo htmlspecialchars($invitado['correo']); ?>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">Sin correo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button onclick="toggleEstadoInvitado(<?php echo $invitado['id_usuario']; ?>, <?php echo $invitado['estado']; ?>)" 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer hover:opacity-80 transition <?php echo $invitado['estado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <i class="fas <?php echo $invitado['estado'] == 1 ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-1"></i>
                                            <?php echo $invitado['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="ver.php?id=<?php echo $invitado['id_usuario']; ?>" 
                                           class="text-gray-600 hover:text-gray-900 mr-3" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="editar.php?id=<?php echo $invitado['id_usuario']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="showConvertirModal(<?php echo $invitado['id_usuario']; ?>, '<?php echo addslashes($invitado['nombre']); ?>')" 
                                                class="text-purple-600 hover:text-purple-900 mr-3" title="Convertir a Socio">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <button onclick="confirmDeleteInvitado(<?php echo $invitado['id_usuario']; ?>, '<?php echo addslashes($invitado['nombre']); ?>')" 
                                                class="text-red-600 hover:text-red-900" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Info de resultados -->
            <div class="mt-4 text-sm text-gray-500">
                Mostrando <?php echo count($invitados); ?> invitado(s)
            </div>
        </main>
    </div>

    <!-- Modal de confirmación -->
    <div id="modalConfirm" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2" id="modalTitle">Confirmar acción</h3>
                <p class="text-gray-500 mb-6" id="modalMessage">¿Estás seguro?</p>
            </div>
            <div class="flex gap-3 justify-center">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancelar
                </button>
                <button id="btnConfirm" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Conversión a Socio -->
    <div id="modalConvertir" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-user-plus text-purple-600 mr-2"></i>
                    Convertir a Socio
                </h3>
                <button onclick="closeConvertirModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-600 mb-4">
                Convirtiendo al invitado: <strong id="nombreInvitado"></strong>
            </p>
            <form id="formConvertir" class="space-y-4">
                <input type="hidden" id="convertirId">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido *</label>
                    <input type="text" id="conv_apellido" required class="w-full border rounded-lg px-3 py-2">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Documento (DNI) *</label>
                        <input type="text" id="conv_documento" required maxlength="8" pattern="[0-9]{8}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                        <select id="conv_genero" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Seleccione</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                    <input type="date" id="conv_fecha_nacimiento" required class="w-full border rounded-lg px-3 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Membresía *</label>
                    <select id="conv_tipo_socio" required class="w-full border rounded-lg px-3 py-2">
                        <option value="">Cargando...</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                    <input type="password" id="conv_contrasena" required minlength="6" class="w-full border rounded-lg px-3 py-2" placeholder="Mínimo 6 caracteres">
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700">
                        <i class="fas fa-user-plus mr-2"></i>Convertir a Socio
                    </button>
                    <button type="button" onclick="closeConvertirModal()" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_URL = '../../api/invitado_admin_api.php';
        const SOCIO_API_URL = '../../api/socio_admin_api.php';
        let searchTimeout;

        function aplicarFiltros() {
            const activos = document.getElementById('filtroActivos').value;
            const busqueda = document.getElementById('busqueda').value;
            
            let url = 'index.php?';
            if (activos) url += 'activos=' + activos + '&';
            if (busqueda) url += 'busqueda=' + encodeURIComponent(busqueda) + '&';
            
            window.location.href = url.slice(0, -1);
        }

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(aplicarFiltros, 500);
        }

        function limpiarFiltros() {
            window.location.href = 'index.php';
        }

        function toggleEstadoInvitado(id, estadoActual) {
            const nuevoEstado = estadoActual == 1 ? 'inactivo' : 'activo';
            
            showModal(
                'Cambiar estado',
                `¿Deseas cambiar el estado del invitado a "${nuevoEstado}"?`,
                () => {
                    fetch(`${API_URL}?id=${id}&action=toggle`, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al cambiar el estado');
                    });
                }
            );
        }

        function confirmDeleteInvitado(id, nombre) {
            fetch(`${API_URL}?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.compras_count > 0) {
                        showModal(
                            'No se puede eliminar',
                            `El invitado "${nombre}" tiene ${data.data.compras_count} compra(s) registrada(s). Considere desactivarlo en lugar de eliminarlo.`,
                            null,
                            true
                        );
                    } else {
                        showModal(
                            'Eliminar invitado',
                            `¿Estás seguro de eliminar al invitado "${nombre}"? Esta acción no se puede deshacer.`,
                            () => deleteInvitado(id)
                        );
                    }
                });
        }

        function deleteInvitado(id) {
            fetch(`${API_URL}?id=${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el invitado');
            });
        }

        async function showConvertirModal(id, nombre) {
            document.getElementById('convertirId').value = id;
            document.getElementById('nombreInvitado').textContent = nombre;
            
            // Cargar tipos de socio
            try {
                const response = await fetch(`${SOCIO_API_URL}?tipos_socio=1&activos=1`);
                const data = await response.json();
                
                const select = document.getElementById('conv_tipo_socio');
                select.innerHTML = '<option value="">Seleccione tipo</option>';
                
                if (data.success && data.data) {
                    data.data.forEach(tipo => {
                        select.innerHTML += `<option value="${tipo.id_socio}">${tipo.nombre} (${tipo.desc_boleto}% desc. boletos)</option>`;
                    });
                }
            } catch (error) {
                console.error('Error cargando tipos:', error);
            }
            
            // Configurar fecha máxima
            const fechaInput = document.getElementById('conv_fecha_nacimiento');
            const hoy = new Date();
            const fechaMax = new Date(hoy.getFullYear() - 13, hoy.getMonth(), hoy.getDate());
            fechaInput.max = fechaMax.toISOString().split('T')[0];
            
            document.getElementById('modalConvertir').classList.remove('hidden');
            document.getElementById('modalConvertir').classList.add('flex');
        }

        function closeConvertirModal() {
            document.getElementById('modalConvertir').classList.add('hidden');
            document.getElementById('modalConvertir').classList.remove('flex');
            document.getElementById('formConvertir').reset();
        }

        // Validar DNI solo números
        document.getElementById('conv_documento').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);
        });

        document.getElementById('formConvertir').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const id = document.getElementById('convertirId').value;
            const data = {
                apellido: document.getElementById('conv_apellido').value.trim(),
                documento: document.getElementById('conv_documento').value,
                genero: document.getElementById('conv_genero').value,
                fecha_nacimiento: document.getElementById('conv_fecha_nacimiento').value,
                id_tipo_socio: document.getElementById('conv_tipo_socio').value,
                contrasena: document.getElementById('conv_contrasena').value
            };
            
            if (data.documento.length !== 8) {
                alert('El documento debe tener exactamente 8 dígitos');
                return;
            }
            
            try {
                const response = await fetch(`${API_URL}?id=${id}&convertir=1`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Invitado convertido a socio correctamente');
                    window.location.href = '../socio/index.php';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al convertir');
            }
        });

        function showModal(title, message, onConfirm, infoOnly = false) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            
            const btnConfirm = document.getElementById('btnConfirm');
            
            if (infoOnly) {
                btnConfirm.style.display = 'none';
            } else {
                btnConfirm.style.display = 'inline-block';
                btnConfirm.onclick = () => {
                    closeModal();
                    if (onConfirm) onConfirm();
                };
            }
            
            document.getElementById('modalConfirm').classList.remove('hidden');
            document.getElementById('modalConfirm').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('modalConfirm').classList.add('hidden');
            document.getElementById('modalConfirm').classList.remove('flex');
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeConvertirModal();
            }
        });
    </script>

<?php include '../../partials/footer.php'; ?>
