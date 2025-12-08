<?php
$pageTitle = 'Dashboard';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-600">Bienvenido al panel de administracion de Cineplanet</p>
            </div>
            
            <!-- Cards de resumen -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-city text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Ciudades</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-ciudades">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-building text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Sedes</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-sedes">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-film text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Peliculas</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-peliculas">-</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-full">
                            <i class="fas fa-shopping-cart text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Productos</p>
                            <p class="text-2xl font-bold text-gray-800" id="count-productos">-</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Accesos rapidos -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Accesos Rapidos</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="/admin/views/ciudad/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                        <i class="fas fa-city text-3xl text-blue-600 mb-2"></i>
                        <span class="text-gray-700">Ciudades</span>
                    </a>
                    <a href="/admin/views/idioma/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                        <i class="fas fa-language text-3xl text-green-600 mb-2"></i>
                        <span class="text-gray-700">Idiomas</span>
                    </a>
                    <a href="/admin/views/formato/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-purple-50 transition">
                        <i class="fas fa-film text-3xl text-purple-600 mb-2"></i>
                        <span class="text-gray-700">Formatos</span>
                    </a>
                    <a href="/admin/views/metodo/index.php" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-orange-50 transition">
                        <i class="fas fa-credit-card text-3xl text-orange-600 mb-2"></i>
                        <span class="text-gray-700">Metodos Pago</span>
                    </a>
                </div>
            </div>
        </main>

<?php include 'partials/footer.php'; ?>
