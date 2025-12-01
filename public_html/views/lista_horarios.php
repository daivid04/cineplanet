<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Cineplanet'; ?></title>
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
                        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Horarios Semanales</h2>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($weekRange); ?></p>
                            </div>
                            <div class="flex gap-2">
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['action' => 'export'])); ?>" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Exportar CSV
                                </a>
                                <a href="asignar_turno_tester.php" class="rounded-lg bg-cineplanet-blue px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Asignar Turno
                                </a>
                            </div>
                        </div>

                        <form action="" method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg flex gap-2">
                            <input type="text" name="search" placeholder="Buscar por nombre o apellido..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue px-4 py-2">
                            <button type="submit" class="rounded-lg bg-cineplanet-blue px-4 py-2 text-white hover:bg-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>

                        <div class="rounded-lg bg-white shadow overflow-hidden border border-gray-200">
                            <!-- Header de la tabla (Días dinámicos) -->
                            <div class="grid grid-cols-8 text-center font-semibold text-gray-600 bg-gray-50">
                                <div class="py-3 border-b border-r px-2">Empleado</div>
                                <?php foreach ($dates as $date): ?>
                                    <div class="py-3 border-b border-r px-2">
                                        <?php echo $date['day_name'] . ' ' . $date['day_num']; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Cuerpo de la tabla -->
                            <div class="grid grid-cols-8 text-sm">
                                <?php if (empty($data)): ?>
                                    <div class="col-span-8 p-4 text-center text-gray-500">No hay datos disponibles.</div>
                                <?php else: ?>
                                    <?php foreach ($data as $workerId => $workerData): ?>
                                        <!-- Fila del Empleado -->
                                        <div class="p-3 border-b border-r font-medium text-gray-800 flex items-center bg-white">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 mr-2 flex-shrink-0 overflow-hidden">
                                                <img src="https://i.pravatar.cc/150?u=<?php echo $workerId; ?>" alt="Avatar" class="h-full w-full object-cover">
                                            </div>
                                            <span class="truncate"><?php echo htmlspecialchars($workerData['info']['nombre'] . ' ' . $workerData['info']['apellido']); ?></span>
                                        </div>

                                        <!-- Celdas de Días -->
                                        <?php foreach ($dates as $date): ?>
                                            <div class="p-2 border-b border-r min-h-[100px] flex flex-col justify-start items-center space-y-2">
                                                <?php 
                                                $currentDate = $date['full'];
                                                if (isset($workerData['dias'][$currentDate])): 
                                                    foreach ($workerData['dias'][$currentDate] as $shift):
                                                ?>
                                                    <div class="rounded-md <?php echo $shift['color_class']; ?> p-2 text-xs font-medium shadow-sm border w-full text-center transition-transform hover:scale-105">
                                                        <span class="block font-bold mb-1"><?php echo htmlspecialchars($shift['tipo']); ?></span>
                                                        <span class="block opacity-90"><?php echo $shift['entrada'] . ' - ' . $shift['salida']; ?></span>
                                                    </div>
                                                <?php 
                                                    endforeach;
                                                else: 
                                                ?>
                                                    <div class="h-full w-full flex items-center justify-center">
                                                        <span class="text-gray-300 italic text-xs">Libre</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
