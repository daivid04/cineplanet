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
                    <div class="rounded-lg bg-white p-6 shadow max-w-2xl mx-auto">
                        <?php if (!empty($error)): ?>
                            <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700 border border-red-200">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <form action="asignar_turno.php" method="POST" class="space-y-6">
                            <div>
                                <label for="employee" class="block text-sm font-medium text-gray-700">Empleado</label>
                                <select id="employee" name="id_trabajador" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue p-2 border" required>
                                    <option value="">Buscar y seleccionar empleado...</option>
                                    <?php foreach ($workers as $worker): ?>
                                        <option value="<?php echo $worker['id_trabajador']; ?>">
                                            <?php echo htmlspecialchars($worker['nombre'] . ' ' . $worker['apellido'] . ' (' . $worker['tipo'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">Fecha</label>
                                <input type="date" id="date" name="fecha" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue p-2 border" required value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label for="shift-type" class="block text-sm font-medium text-gray-700">Tipo de Turno</label>
                                    <select id="shift-type" name="tipo_turno" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue p-2 border">
                                        <option value="Apertura">Apertura</option>
                                        <option value="Intermedio">Intermedio</option>
                                        <option value="Cierre">Cierre</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="start-time" class="block text-sm font-medium text-gray-700">Hora de Inicio</label>
                                    <input type="time" id="start-time" name="hora_inicio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue p-2 border" required>
                                </div>
                                <div>
                                    <label for="end-time" class="block text-sm font-medium text-gray-700">Hora de Fin</label>
                                    <input type="time" id="end-time" name="hora_fin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue p-2 border" required>
                                </div>
                            </div>
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notas (Opcional)</label>
                                <textarea id="notes" name="notas" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue p-2 border"></textarea>
                            </div>
                            
                            <div class="flex justify-end gap-4 pt-4">
                                <a href="../horarios.php" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Cancelar</a>
                                <button type="submit" class="rounded-md border border-transparent bg-cineplanet-blue py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700">Asignar Turno</button>
                            </div>
                        </form>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
