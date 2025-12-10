<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Cineplanet</title>
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
                            dark: '#0f172a',
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
<body class="bg-slate-50 text-slate-600 font-sans antialiased h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">
        
        <!-- Header -->
        <?php include __DIR__ . '/partials/header.php'; ?>

        <!-- Scrollable Content -->
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-4xl mx-auto space-y-8">
                
                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Configuración</h1>
                        <p class="text-slate-500 mt-1">Administra tu información personal y seguridad.</p>
                    </div>
                </div>

                <!-- Alerts -->
                <?php if (!empty($message)): ?>
                    <div class="rounded-lg p-4 mb-6 <?php echo $messageType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'; ?>">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <?php if ($messageType === 'success'): ?>
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <?php else: ?>
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                <?php endif; ?>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium"><?php echo htmlspecialchars($message); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($user): ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Profile Information -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl shadow-soft border border-slate-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                                <h3 class="text-lg font-semibold text-slate-900">Información Personal</h3>
                                <p class="text-sm text-slate-500">Actualiza tus datos de contacto y perfil.</p>
                            </div>
                            <div class="p-6">
                                <form action="configuracion.php" method="POST">
                                    <input type="hidden" name="action" value="update_profile">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="nombre" class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
                                            <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" class="w-full rounded-lg border-slate-200 text-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" required>
                                        </div>
                                        <div>
                                            <label for="apellido" class="block text-sm font-medium text-slate-700 mb-1">Apellido</label>
                                            <input type="text" name="apellido" id="apellido" value="<?php echo htmlspecialchars($user['apellido']); ?>" class="w-full rounded-lg border-slate-200 text-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="correo" class="block text-sm font-medium text-slate-700 mb-1">Correo Electrónico</label>
                                            <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($user['correo']); ?>" class="w-full rounded-lg border-slate-200 text-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="numero" class="block text-sm font-medium text-slate-700 mb-1">Teléfono (Opcional)</label>
                                            <input type="text" name="numero" id="numero" value="<?php echo htmlspecialchars($user['numero'] ?? ''); ?>" class="w-full rounded-lg border-slate-200 text-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue">
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end">
                                        <button type="submit" class="bg-cineplanet-blue text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-cineplanet-hover transition-colors shadow-sm">
                                            Guardar Cambios
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-xl shadow-soft border border-slate-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                                <h3 class="text-lg font-semibold text-slate-900">Seguridad</h3>
                                <p class="text-sm text-slate-500">Cambia tu contraseña de acceso.</p>
                            </div>
                            <div class="p-6">
                                <form action="configuracion.php" method="POST">
                                    <input type="hidden" name="action" value="update_password">
                                    <div class="space-y-4">
                                        <div>
                                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Nueva Contraseña</label>
                                            <input type="password" name="password" id="password" class="w-full rounded-lg border-slate-200 text-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" required minlength="6">
                                        </div>
                                        <div>
                                            <label for="confirm_password" class="block text-sm font-medium text-slate-700 mb-1">Confirmar Contraseña</label>
                                            <input type="password" name="confirm_password" id="confirm_password" class="w-full rounded-lg border-slate-200 text-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue" required minlength="6">
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end">
                                        <button type="submit" class="bg-white text-slate-700 border border-slate-200 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors shadow-sm">
                                            Actualizar Contraseña
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Read Only Info -->
                        <div class="bg-white rounded-xl shadow-soft border border-slate-100 overflow-hidden">
                            <div class="p-6">
                                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Detalles de Cuenta</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-500">ID Empleado</span>
                                        <span class="font-mono text-slate-900"><?php echo $user['id_trabajador']; ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-500">Cargo</span>
                                        <span class="font-medium text-cineplanet-blue bg-blue-50 px-2 py-0.5 rounded text-xs"><?php echo $user['cargo']; ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-500">Sede</span>
                                        <span class="text-slate-900"><?php echo $user['sede_nombre']; ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-slate-500">Fecha Ingreso</span>
                                        <span class="text-slate-900"><?php echo $user['fecha_ingreso']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <p class="text-slate-500">No se pudo cargar la información del usuario.</p>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

</body>
</html>
