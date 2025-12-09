<?php
$pageTitle = 'Dashboard';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-600">Bienvenido al panel de administración de Cineplanet</p>
            </div>
            
            <!-- Cards de resumen principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-building text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Sedes</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-sedes">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-film text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Películas</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-peliculas">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-calendar-alt text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Funciones</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-funciones">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-full">
                            <i class="fas fa-users text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Usuarios</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-usuarios">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Segunda fila de cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-pink-100 rounded-full">
                            <i class="fas fa-id-card text-pink-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Socios</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-socios">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-box text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Productos</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-productos">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 rounded-full">
                            <i class="fas fa-gifts text-indigo-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Combos</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-combos">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-teal-100 rounded-full">
                            <i class="fas fa-couch text-teal-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Asientos</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-asientos">
                                <i class="fas fa-spinner fa-spin text-gray-400"></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido en dos columnas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Últimas películas -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-film text-purple-600 mr-2"></i>Últimas Películas
                        </h2>
                        <a href="views/pelicula/index.php" class="text-blue-600 hover:text-blue-800 text-sm">Ver todas →</a>
                    </div>
                    <div class="p-6">
                        <div id="ultimas-peliculas" class="space-y-3">
                            <div class="text-center py-4 text-gray-400">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Últimas funciones -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-calendar-alt text-green-600 mr-2"></i>Últimas Funciones
                        </h2>
                        <a href="views/funcion/index.php" class="text-blue-600 hover:text-blue-800 text-sm">Ver todas →</a>
                    </div>
                    <div class="p-6">
                        <div id="ultimas-funciones" class="space-y-3">
                            <div class="text-center py-4 text-gray-400">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tercera fila -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Inventario por sede -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-warehouse text-yellow-600 mr-2"></i>Inventario por Sede
                        </h2>
                        <a href="views/producto_sede/index.php" class="text-blue-600 hover:text-blue-800 text-sm">Ver todo →</a>
                    </div>
                    <div class="p-6">
                        <div id="inventario-sede" class="space-y-3">
                            <div class="text-center py-4 text-gray-400">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Socios por tipo -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-crown text-pink-600 mr-2"></i>Socios por Tipo
                        </h2>
                        <a href="views/socio/index.php" class="text-blue-600 hover:text-blue-800 text-sm">Ver todos →</a>
                    </div>
                    <div class="p-6">
                        <div id="socios-tipo" class="space-y-3">
                            <div class="text-center py-4 text-gray-400">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Accesos rápidos -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-th-large text-blue-600 mr-2"></i>Accesos Rápidos
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <a href="views/ciudad/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-blue-50 hover:shadow transition">
                        <i class="fas fa-city text-2xl text-blue-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Ciudades</span>
                    </a>
                    <a href="views/sede/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-green-50 hover:shadow transition">
                        <i class="fas fa-building text-2xl text-green-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Sedes</span>
                    </a>
                    <a href="views/pelicula/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-purple-50 hover:shadow transition">
                        <i class="fas fa-film text-2xl text-purple-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Películas</span>
                    </a>
                    <a href="views/funcion/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-teal-50 hover:shadow transition">
                        <i class="fas fa-calendar-alt text-2xl text-teal-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Funciones</span>
                    </a>
                    <a href="views/producto/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-yellow-50 hover:shadow transition">
                        <i class="fas fa-box text-2xl text-yellow-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Productos</span>
                    </a>
                    <a href="views/combo/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-pink-50 hover:shadow transition">
                        <i class="fas fa-gifts text-2xl text-pink-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Combos</span>
                    </a>
                    <a href="views/sala/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-indigo-50 hover:shadow transition">
                        <i class="fas fa-door-open text-2xl text-indigo-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Salas</span>
                    </a>
                    <a href="views/usuario/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-orange-50 hover:shadow transition">
                        <i class="fas fa-users text-2xl text-orange-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Usuarios</span>
                    </a>
                    <a href="views/socio/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-red-50 hover:shadow transition">
                        <i class="fas fa-id-card text-2xl text-red-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Socios</span>
                    </a>
                    <a href="views/invitado/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-cyan-50 hover:shadow transition">
                        <i class="fas fa-user-tag text-2xl text-cyan-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Invitados</span>
                    </a>
                    <a href="views/producto_combo/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-amber-50 hover:shadow transition">
                        <i class="fas fa-box-open text-2xl text-amber-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Comp. Combos</span>
                    </a>
                    <a href="views/producto_sede/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-lime-50 hover:shadow transition">
                        <i class="fas fa-warehouse text-2xl text-lime-600 mb-2"></i>
                        <span class="text-gray-700 text-sm">Inventario</span>
                    </a>
                </div>
            </div>
        </main>
    </div>

<?php include 'partials/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
});

function cargarEstadisticas() {
    fetch('api/dashboard_api.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stats = data.data;
                
                // Actualizar contadores
                document.getElementById('count-sedes').innerHTML = 
                    `${stats.sedes_activas} <span class="text-sm text-gray-400">/ ${stats.sedes}</span>`;
                document.getElementById('count-peliculas').innerHTML = 
                    `${stats.peliculas_activas} <span class="text-sm text-gray-400">/ ${stats.peliculas}</span>`;
                document.getElementById('count-funciones').innerHTML = 
                    `${stats.funciones_activas} <span class="text-sm text-gray-400">/ ${stats.funciones}</span>`;
                document.getElementById('count-usuarios').innerHTML = 
                    `${stats.usuarios_activos} <span class="text-sm text-gray-400">/ ${stats.usuarios}</span>`;
                document.getElementById('count-socios').textContent = stats.socios;
                document.getElementById('count-productos').innerHTML = 
                    `${stats.productos_activos} <span class="text-sm text-gray-400">/ ${stats.productos}</span>`;
                document.getElementById('count-combos').innerHTML = 
                    `${stats.combos_activos} <span class="text-sm text-gray-400">/ ${stats.combos}</span>`;
                document.getElementById('count-asientos').textContent = stats.asientos;

                // Últimas películas
                renderUltimasPeliculas(stats.ultimas_peliculas);

                // Últimas funciones
                renderUltimasFunciones(stats.ultimas_funciones);

                // Inventario por sede
                renderInventarioSede(stats.inventario_por_sede);

                // Socios por tipo
                renderSociosTipo(stats.socios_por_tipo);
            } else {
                console.error('Error al cargar estadísticas:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function renderUltimasPeliculas(peliculas) {
    const container = document.getElementById('ultimas-peliculas');
    
    if (!peliculas || peliculas.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center">No hay películas registradas</p>';
        return;
    }

    container.innerHTML = peliculas.map(p => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <div class="flex items-center gap-3">
                <i class="fas fa-film text-purple-500"></i>
                <div>
                    <p class="font-medium text-gray-800">${escapeHtml(p.titulo)}</p>
                    <p class="text-xs text-gray-500">${p.duracion} min</p>
                </div>
            </div>
            <span class="px-2 py-1 text-xs rounded-full ${p.estado == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                ${p.estado == 1 ? 'Activa' : 'Inactiva'}
            </span>
        </div>
    `).join('');
}

function renderUltimasFunciones(funciones) {
    const container = document.getElementById('ultimas-funciones');
    
    if (!funciones || funciones.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center">No hay funciones registradas</p>';
        return;
    }

    container.innerHTML = funciones.map(f => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <div class="flex items-center gap-3">
                <i class="fas fa-calendar-check text-green-500"></i>
                <div>
                    <p class="font-medium text-gray-800">${escapeHtml(f.pelicula)}</p>
                    <p class="text-xs text-gray-500">${f.sala} - ${f.sede}</p>
                    <p class="text-xs text-gray-400">${f.fecha} ${f.hora}</p>
                </div>
            </div>
            <span class="px-2 py-1 text-xs rounded-full ${f.estado == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                ${f.estado == 1 ? 'Activa' : 'Inactiva'}
            </span>
        </div>
    `).join('');
}

function renderInventarioSede(inventario) {
    const container = document.getElementById('inventario-sede');
    
    if (!inventario || inventario.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center">No hay inventario registrado</p>';
        return;
    }

    container.innerHTML = inventario.map(i => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3">
                <i class="fas fa-building text-yellow-500"></i>
                <span class="font-medium text-gray-800">${escapeHtml(i.sede)}</span>
            </div>
            <div class="text-right">
                <span class="text-sm font-bold text-gray-800">${i.productos || 0} productos</span>
                <p class="text-xs text-gray-500">Stock: ${i.stock_total || 0}</p>
            </div>
        </div>
    `).join('');
}

function renderSociosTipo(socios) {
    const container = document.getElementById('socios-tipo');
    
    if (!socios || socios.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center">No hay tipos de socio</p>';
        return;
    }

    const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-orange-500', 'bg-pink-500'];
    
    container.innerHTML = socios.map((s, i) => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full ${colors[i % colors.length]}"></div>
                <span class="font-medium text-gray-800">${escapeHtml(s.tipo)}</span>
            </div>
            <span class="px-3 py-1 bg-gray-200 rounded-full text-sm font-bold text-gray-700">
                ${s.total}
            </span>
        </div>
    `).join('');
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
