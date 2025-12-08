        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-film"></i>
                    Cineplanet Admin
                </h1>
            </div>
            
            <?php 
            // Calcular la ruta base dinamicamente
            $scriptPath = $_SERVER['SCRIPT_NAME'];
            $basePath = '';
            if (preg_match('#(.*?/public_html)/admin#', $scriptPath, $matches)) {
                $basePath = $matches[1];
            }
            ?>
            
            <nav class="p-4">
                <p class="text-gray-400 text-xs uppercase mb-2">Menu Principal</p>
                
                <a href="<?php echo $basePath; ?>/admin/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentPage == 'index' && $currentDir == 'admin') ? 'active' : ''; ?>">
                    <i class="fas fa-home w-5"></i>
                    Dashboard
                </a>
                
                <p class="text-gray-400 text-xs uppercase mt-4 mb-2">Catalogos</p>
                
                <a href="<?php echo $basePath; ?>/admin/views/ciudad/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentDir == 'ciudad') ? 'active' : ''; ?>">
                    <i class="fas fa-city w-5"></i>
                    Ciudades
                </a>
                
                <a href="<?php echo $basePath; ?>/admin/views/idioma/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentDir == 'idioma') ? 'active' : ''; ?>">
                    <i class="fas fa-language w-5"></i>
                    Idiomas
                </a>
                
                <a href="<?php echo $basePath; ?>/admin/views/formato/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentDir == 'formato') ? 'active' : ''; ?>">
                    <i class="fas fa-film w-5"></i>
                    Formatos
                </a>
                
                <a href="<?php echo $basePath; ?>/admin/views/metodo/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentDir == 'metodo') ? 'active' : ''; ?>">
                    <i class="fas fa-credit-card w-5"></i>
                    Metodos de Pago
                </a>
                
                <a href="<?php echo $basePath; ?>/admin/views/tipo_socio/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentDir == 'tipo_socio') ? 'active' : ''; ?>">
                    <i class="fas fa-crown w-5"></i>
                    Tipos de Socio
                </a>
                
                <p class="text-gray-400 text-xs uppercase mt-4 mb-2">Gestion</p>
                
                <a href="<?php echo $basePath; ?>/admin/views/sede/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentDir == 'sede') ? 'active' : ''; ?>">
                    <i class="fas fa-building w-5"></i>
                    Sedes
                </a>
                
                <a href="<?php echo $basePath; ?>/admin/views/sala/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentDir == 'sala') ? 'active' : ''; ?>">
                    <i class="fas fa-door-open w-5"></i>
                    Salas
                </a>
                
                <a href="<?php echo $basePath; ?>/admin/views/pelicula/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentDir == 'pelicula') ? 'active' : ''; ?>">
                    <i class="fas fa-video w-5"></i>
                    Peliculas
                </a>
                
                <p class="text-gray-400 text-xs uppercase mt-4 mb-2">Dulceria</p>
                
                <a href="<?php echo $basePath; ?>/admin/views/producto/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentDir == 'producto') ? 'active' : ''; ?>">
                    <i class="fas fa-box w-5"></i>
                    Productos
                </a>
                
                <a href="<?php echo $basePath; ?>/admin/views/combo/index.php" 
                   class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg mb-1 text-gray-300 <?php echo ($currentDir == 'combo') ? 'active' : ''; ?>">
                    <i class="fas fa-gifts w-5"></i>
                    Combos
                </a>
            </nav>
            
            <div class="absolute bottom-0 w-64 p-4 border-t border-gray-700">
                <a href="<?php echo $basePath; ?>/views/login.html" class="flex items-center gap-3 px-3 py-2 text-gray-300 hover:text-white">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    Salir
                </a>
            </div>
        </aside>
