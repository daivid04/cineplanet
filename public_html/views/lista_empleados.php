<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados - Cineplanet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        cineplanet: {
                            blue: '#00539f',
                            hover: '#004280',
                            yellow: '#ffc600',
                            dark: '#0f172a', // Slate 900
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                    }
                },
            },
        };
    </script>
</head>
<body class="bg-slate-50 text-slate-600 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <!-- Header -->
            <?php include __DIR__ . '/partials/header.php'; ?>

            <main class="w-full flex-grow p-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Empleados</h2>
                        <p class="text-sm text-slate-500 mt-1">Gestiona el equipo de trabajo de tu sede.</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['action' => 'export'])); ?>" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cineplanet-blue focus:ring-offset-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Exportar CSV
                        </a>
                        <a href="/cineplanet/public_html/views/crear_empleado.php" class="inline-flex items-center justify-center rounded-lg bg-cineplanet-blue px-4 py-2.5 text-sm font-medium text-white shadow-soft hover:bg-cineplanet-hover focus:outline-none focus:ring-2 focus:ring-cineplanet-blue focus:ring-offset-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nuevo Empleado
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="mb-8 rounded-xl bg-white p-5 shadow-soft border border-slate-100">
                    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" placeholder="Buscar empleado..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="block w-full rounded-lg border-slate-200 bg-slate-50 pl-10 focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm py-2.5">
                        </div>
                        
                        <select name="sede" class="block w-full rounded-lg border-slate-200 bg-slate-50 py-2.5 text-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" onchange="this.form.submit()">
                            <option value="">Todas las Sedes</option>
                            <?php foreach ($data['sedes'] as $sede): ?>
                                <option value="<?php echo $sede['id_sede']; ?>" <?php echo (isset($_GET['sede']) && $_GET['sede'] == $sede['id_sede']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($sede['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="cargo" class="block w-full rounded-lg border-slate-200 bg-slate-50 py-2.5 text-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" onchange="this.form.submit()">
                            <option value="">Todos los Cargos</option>
                            <?php foreach ($data['cargos'] as $cargo): ?>
                                <option value="<?php echo $cargo; ?>" <?php echo (isset($_GET['cargo']) && $_GET['cargo'] == $cargo) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cargo); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="estado" class="block w-full rounded-lg border-slate-200 bg-slate-50 py-2.5 text-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" onchange="this.form.submit()">
                            <option value="">Todos los Estados</option>
                            <option value="1" <?php echo (isset($_GET['estado']) && $_GET['estado'] === '1') ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo (isset($_GET['estado']) && $_GET['estado'] === '0') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </form>
                </div>

                <!-- Table -->
                <div class="overflow-hidden rounded-xl bg-white shadow-soft border border-slate-100">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                                <tr>
                                    <th class="px-6 py-4">Empleado</th>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Sede</th>
                                    <th class="px-6 py-4">Cargo</th>
                                    <th class="px-6 py-4">Ingreso</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (isset($data['empleados']) && count($data['empleados']) > 0): ?>
                                    <?php foreach ($data['empleados'] as $empleado): ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm mr-4" src="https://i.pravatar.cc/150?u=<?php echo $empleado['id_trabajador']; ?>" alt="Avatar">
                                                    <div>
                                                        <div class="font-semibold text-slate-900"><?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']); ?></div>
                                                        <div class="text-xs text-slate-500"><?php echo htmlspecialchars($empleado['correo'] ?? ''); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                                CP-<?php echo str_pad($empleado['id_trabajador'], 4, '0', STR_PAD_LEFT); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-50 text-blue-600 mr-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                    </span>
                                                    <?php echo htmlspecialchars($empleado['sede_nombre']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                                    <?php echo htmlspecialchars($empleado['cargo']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-slate-500">
                                                <?php echo htmlspecialchars($empleado['fecha_ingreso'] ?? '--/--/----'); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if ($empleado['estado']): ?>
                                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                                        Activo
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-red-600"></span>
                                                        Inactivo
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="/cineplanet/public_html/views/editar_empleado.php?id=<?php echo $empleado['id_trabajador']; ?>" class="text-cineplanet-blue hover:text-cineplanet-hover font-medium text-sm transition-colors">Editar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <p class="text-lg font-medium text-slate-900">No se encontraron empleados</p>
                                                <p class="text-sm text-slate-500">Intenta ajustar los filtros de búsqueda.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 flex items-center justify-between">
                        <?php
                        $page = $data['page'];
                        $limit = $data['limit'];
                        $total = $data['total'];
                        $totalPages = ceil($total / $limit);
                        $start = ($page - 1) * $limit + 1;
                        $end = min($page * $limit, $total);
                        $queryParams = $_GET;
                        ?>
                        <div class="text-sm text-slate-500">
                            Mostrando <span class="font-medium text-slate-900"><?php echo $start; ?></span> a <span class="font-medium text-slate-900"><?php echo $end; ?></span> de <span class="font-medium text-slate-900"><?php echo $total; ?></span> resultados
                        </div>
                        <div class="flex gap-2">
                            <?php if ($page > 1): 
                                $queryParams['page'] = $page - 1;
                                $prevLink = '?' . http_build_query($queryParams);
                            ?>
                                <a href="<?php echo $prevLink; ?>" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Anterior</a>
                            <?php else: ?>
                                <button disabled class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-100 px-4 py-2 text-sm font-medium text-slate-400 cursor-not-allowed">Anterior</button>
                            <?php endif; ?>

                            <?php if ($page < $totalPages): 
                                $queryParams['page'] = $page + 1;
                                $nextLink = '?' . http_build_query($queryParams);
                            ?>
                                <a href="<?php echo $nextLink; ?>" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Siguiente</a>
                            <?php else: ?>
                                <button disabled class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-100 px-4 py-2 text-sm font-medium text-slate-400 cursor-not-allowed">Siguiente</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
