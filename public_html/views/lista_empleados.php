<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024, initial-scale=1.0">
    <title>Gestión de Empleados - Cineplanet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'cineplanet-blue': '#00539f',
                        'cineplanet-yellow': '#ffc600',
                        'cineplanet-gray': '#f3f4f6',
                        'cineplanet-dark': '#1e293b',
                    },
                },
            },
        };
    </script>
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #e5e7eb; }
        .mockup-container { width: 1024px; height: 768px; box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25); overflow: hidden; }
    </style>
</head>
<body>
    <div class="mockup-container">
        <div class="flex h-full w-full bg-cineplanet-gray">
            <!-- Sidebar -->
            <?php include __DIR__ . '/partials/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="flex flex-1 flex-col overflow-hidden">
                <!-- Header -->
                <?php include __DIR__ . '/partials/header.php'; ?>

                <main class="flex-1 overflow-y-auto p-6">
                    <div class="rounded-lg bg-white p-6 shadow">
                    <div class="rounded-lg bg-white p-6 shadow">
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                            <h2 class="text-xl font-bold text-gray-800">Lista de Empleados</h2>
                            <div class="flex gap-2">
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['action' => 'export'])); ?>" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Exportar CSV
                                </a>
                                <a href="views/crear_empleado.php" class="rounded-lg bg-cineplanet-blue px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Agregar Empleado
                                </a>
                            </div>
                        </div>

                        <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="relative flex gap-2">
                                <input type="text" name="search" placeholder="Buscar por nombre o DNI..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue px-4 py-2">
                                <button type="submit" class="rounded-lg bg-cineplanet-blue px-4 py-2 text-white hover:bg-blue-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                            <select name="sede" class="rounded-lg border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" onchange="this.form.submit()">
                                <option value="">Todas las Sedes</option>
                                <?php foreach ($data['sedes'] as $sede): ?>
                                    <option value="<?php echo $sede['id_sede']; ?>" <?php echo (isset($_GET['sede']) && $_GET['sede'] == $sede['id_sede']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sede['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="cargo" class="rounded-lg border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" onchange="this.form.submit()">
                                <option value="">Todos los Cargos</option>
                                <?php foreach ($data['cargos'] as $cargo): ?>
                                    <option value="<?php echo $cargo; ?>" <?php echo (isset($_GET['cargo']) && $_GET['cargo'] == $cargo) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cargo); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="estado" class="rounded-lg border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" onchange="this.form.submit()">
                                <option value="">Todos los Estados</option>
                                <option value="1" <?php echo (isset($_GET['estado']) && $_GET['estado'] === '1') ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo (isset($_GET['estado']) && $_GET['estado'] === '0') ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </form>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-500">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                                    <tr>
                                        <th class="px-6 py-3">Nombre</th>
                                        <th class="px-6 py-3">ID Empleado</th>
                                        <th class="px-6 py-3">Sede</th>
                                        <th class="px-6 py-3">Cargo</th>
                                        <th class="px-6 py-3">Fecha Ingreso</th>
                                        <th class="px-6 py-3">Estado</th>
                                        <th class="px-6 py-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($data['empleados']) && count($data['empleados']) > 0): ?>
                                        <?php foreach ($data['empleados'] as $empleado): ?>
                                            <tr class="border-b bg-white hover:bg-gray-50">
                                                <td class="px-6 py-3 font-medium text-gray-900">
                                                    <div class="flex items-center">
                                                        <img class="h-8 w-8 rounded-full mr-3" src="https://i.pravatar.cc/150?u=<?php echo $empleado['id_trabajador']; ?>" alt="Avatar">
                                                        <?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']); ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3">CP-<?php echo str_pad($empleado['id_trabajador'], 4, '0', STR_PAD_LEFT); ?></td>
                                                <td class="px-6 py-3"><?php echo htmlspecialchars($empleado['sede_nombre']); ?></td>
                                                <td class="px-6 py-3"><?php echo htmlspecialchars($empleado['cargo']); ?></td>
                                                <td class="px-6 py-3"><?php echo htmlspecialchars($empleado['fecha_ingreso'] ?? '--/--/----'); ?></td>
                                                <td class="px-6 py-3">
                                                    <?php if ($empleado['estado']): ?>
                                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">Activo</span>
                                                    <?php else: ?>
                                                        <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-3 text-center">
                                                    <a href="views/editar_empleado.php?id=<?php echo $empleado['id_trabajador']; ?>" class="font-medium text-cineplanet-blue hover:underline">Editar</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="px-6 py-3 text-center">No hay empleados registrados.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                         <nav class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6 mt-4">
                            <?php
                            $page = $data['page'];
                            $limit = $data['limit'];
                            $total = $data['total'];
                            $totalPages = ceil($total / $limit);
                            $start = ($page - 1) * $limit + 1;
                            $end = min($page * $limit, $total);
                            
                            // Mantener filtros en la paginación
                            $queryParams = $_GET;
                            ?>
                            <div class="text-sm text-gray-700">Mostrando <span class="font-medium"><?php echo $start; ?></span> a <span class="font-medium"><?php echo $end; ?></span> de <span class="font-medium"><?php echo $total; ?></span> resultados</div>
                            <div class="flex items-center gap-2">
                                <?php if ($page > 1): 
                                    $queryParams['page'] = $page - 1;
                                    $prevLink = '?' . http_build_query($queryParams);
                                ?>
                                    <a href="<?php echo $prevLink; ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</a>
                                <?php else: ?>
                                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">Anterior</span>
                                <?php endif; ?>

                                <?php if ($page < $totalPages): 
                                    $queryParams['page'] = $page + 1;
                                    $nextLink = '?' . http_build_query($queryParams);
                                ?>
                                    <a href="<?php echo $nextLink; ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</a>
                                <?php else: ?>
                                    <span class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed">Siguiente</span>
                                <?php endif; ?>
                            </div>
                        </nav>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
