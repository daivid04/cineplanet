<?php
$pageTitle = 'Usuarios';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/UsuarioAdminModel.php';

$model = new UsuarioAdminModel($conn);

// Filtros
$filtroTipo = $_GET['tipo'] ?? null;
$soloActivos = isset($_GET['activos']) && $_GET['activos'] === '1';

$usuarios = $model->getAll($soloActivos, $filtroTipo);

// Estadísticas
$totalUsuarios = $model->count();
$totalSocios = $model->count(false, 'socio');
$totalInvitados = $model->count(false, 'invitado');
$totalActivos = $model->count(true);
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Usuarios</h1>
                    <p class="text-gray-600">Gestiona los usuarios del sistema</p>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Usuarios</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $totalUsuarios; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-user-tie text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Socios</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $totalSocios; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-user text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Invitados</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $totalInvitados; ?></p>
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
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $totalActivos; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Usuario</label>
                        <select id="filtroTipo" onchange="aplicarFiltros()" 
                                class="border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos</option>
                            <option value="socio" <?php echo $filtroTipo === 'socio' ? 'selected' : ''; ?>>Socios</option>
                            <option value="invitado" <?php echo $filtroTipo === 'invitado' ? 'selected' : ''; ?>>Invitados</option>
                            <option value="sin_tipo" <?php echo $filtroTipo === 'sin_tipo' ? 'selected' : ''; ?>>Sin tipo</option>
                        </select>
                    </div>
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
                        <input type="text" id="busqueda" placeholder="Buscar por correo o nombre..." 
                               class="border rounded-lg px-3 py-2 w-full focus:ring-blue-500 focus:border-blue-500"
                               onkeyup="buscarUsuarios(this.value)">
                    </div>
                    <div class="flex items-end">
                        <button onclick="limpiarFiltros()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                            <i class="fas fa-times mr-2"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membresía</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaUsuarios" class="bg-white divide-y divide-gray-200">
                        <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No hay usuarios registrados
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $usuario['id_usuario']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                    <span><?php echo htmlspecialchars($usuario['correo']); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($usuario['nombre_completo']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $tipoClase = match($usuario['tipo_usuario']) {
                                    'Socio' => 'bg-purple-100 text-purple-800',
                                    'Invitado' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                $tipoIcono = match($usuario['tipo_usuario']) {
                                    'Socio' => 'fa-user-tie',
                                    'Invitado' => 'fa-user',
                                    default => 'fa-question'
                                };
                                ?>
                                <span class="px-2 py-1 text-xs font-medium <?php echo $tipoClase; ?> rounded-full">
                                    <i class="fas <?php echo $tipoIcono; ?> mr-1"></i>
                                    <?php echo $usuario['tipo_usuario']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $usuario['tipo_socio_nombre'] ?? '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($usuario['estado'] == 1): ?>
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    Activo
                                </span>
                                <?php else: ?>
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                    Inactivo
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="toggleEstadoUsuario(<?php echo $usuario['id_usuario']; ?>, <?php echo $usuario['estado']; ?>)"
                                        class="text-yellow-600 hover:text-yellow-900 mr-3" title="Cambiar estado">
                                    <i class="fas fa-toggle-<?php echo $usuario['estado'] == 1 ? 'on' : 'off'; ?>"></i>
                                </button>
                                
                                <a href="ver.php?id=<?php echo $usuario['id_usuario']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <a href="editar.php?id=<?php echo $usuario['id_usuario']; ?>" 
                                   class="text-green-600 hover:text-green-900 mr-3" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button onclick="confirmDeleteUsuario(<?php echo $usuario['id_usuario']; ?>, '<?php echo htmlspecialchars($usuario['correo']); ?>')"
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

            <div class="mt-4 text-sm text-gray-500">
                Mostrando: <?php echo count($usuarios); ?> usuarios
            </div>
        </main>

<script>
const API_URL = '../../api/usuario_admin_api.php';

function aplicarFiltros() {
    const tipo = document.getElementById('filtroTipo').value;
    const activos = document.getElementById('filtroActivos').value;
    
    let url = 'index.php?';
    if (tipo) url += `tipo=${tipo}&`;
    if (activos) url += `activos=${activos}&`;
    
    window.location.href = url;
}

function limpiarFiltros() {
    window.location.href = 'index.php';
}

let timeoutBusqueda;
function buscarUsuarios(termino) {
    clearTimeout(timeoutBusqueda);
    
    if (termino.length < 2) {
        if (termino.length === 0) {
            location.reload();
        }
        return;
    }
    
    timeoutBusqueda = setTimeout(async () => {
        try {
            const response = await fetch(`${API_URL}?search=${encodeURIComponent(termino)}`);
            const result = await response.json();
            
            if (result.success) {
                renderizarTabla(result.data);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }, 300);
}

function renderizarTabla(usuarios) {
    const tbody = document.getElementById('tablaUsuarios');
    
    if (usuarios.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    No se encontraron usuarios
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = usuarios.map(u => {
        const tipoClase = u.tipo_usuario === 'Socio' ? 'bg-purple-100 text-purple-800' : 
                          u.tipo_usuario === 'Invitado' ? 'bg-yellow-100 text-yellow-800' : 
                          'bg-gray-100 text-gray-800';
        const tipoIcono = u.tipo_usuario === 'Socio' ? 'fa-user-tie' : 
                          u.tipo_usuario === 'Invitado' ? 'fa-user' : 'fa-question';
        const estadoClase = u.estado == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        const estadoTexto = u.estado == 1 ? 'Activo' : 'Inactivo';
        
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${u.id_usuario}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-envelope text-gray-400"></i>
                        <span>${u.correo}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${u.nombre_completo || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium ${tipoClase} rounded-full">
                        <i class="fas ${tipoIcono} mr-1"></i>${u.tipo_usuario}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${u.tipo_socio_nombre || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-medium ${estadoClase} rounded-full">${estadoTexto}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button onclick="toggleEstadoUsuario(${u.id_usuario}, ${u.estado})" class="text-yellow-600 hover:text-yellow-900 mr-3">
                        <i class="fas fa-toggle-${u.estado == 1 ? 'on' : 'off'}"></i>
                    </button>
                    <a href="ver.php?id=${u.id_usuario}" class="text-blue-600 hover:text-blue-900 mr-3">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="editar.php?id=${u.id_usuario}" class="text-green-600 hover:text-green-900 mr-3">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="confirmDeleteUsuario(${u.id_usuario}, '${u.correo}')" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

async function toggleEstadoUsuario(id, estadoActual) {
    const nuevoEstado = estadoActual == 1 ? 0 : 1;
    const accion = nuevoEstado == 1 ? 'activar' : 'desactivar';
    
    if (!confirm(`¿Estás seguro de ${accion} este usuario?`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ estado: nuevoEstado })
        });
        
        const result = await response.json();
        
        if (result.success) {
            if (result.warning) {
                alert(result.message);
            }
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

async function confirmDeleteUsuario(id, correo) {
    if (!confirm(`¿Estás seguro de eliminar el usuario "${correo}"?\n\nEsta acción desactivará el usuario.`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}
</script>

<?php include '../../partials/footer.php'; ?>
