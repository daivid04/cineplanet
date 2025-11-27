<header class="flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4">
    <h1 class="text-2xl font-bold text-cineplanet-dark"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard de Control'; ?></h1>
    <div class="flex items-center gap-4">
        <div class="relative">
            <input type="text" placeholder="Buscar empleado..." class="rounded-lg border border-gray-300 py-2 pl-10 pr-4 focus:border-cineplanet-blue focus:outline-none focus:ring-1 focus:ring-cineplanet-blue">
            <svg class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/></svg>
        </div>
        <div class="flex items-center">
            <span class="font-semibold mr-3 text-gray-700">Admin</span>
            <img class="h-10 w-10 rounded-full object-cover" src="https://i.pravatar.cc/150?u=a042581f4e29026704d" alt="Foto de perfil">
        </div>
    </div>
</header>
