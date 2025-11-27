<aside class="flex h-full w-60 flex-col bg-cineplanet-dark text-white shadow-lg">
    <div class="flex items-center justify-center p-6 border-b border-gray-700">
        <!-- Logo de Cineplanet -->
        <img src="https://placehold.co/150x40/00539f/ffc600?text=Cineplanet" alt="Logo Cineplanet" class="h-10">
    </div>
    <nav class="flex-1 px-4 py-4">
        <a href="dashboard_tester.php" class="flex items-center rounded-lg px-4 py-2 <?php echo (isset($activePage) && $activePage === 'dashboard') ? 'bg-cineplanet-blue/30 text-white' : 'text-gray-300 hover:bg-cineplanet-blue/20'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span class="ml-3 font-semibold">Dashboard</span>
        </a>
        <a href="empleados_tester.php" class="mt-2 flex items-center rounded-lg px-4 py-2 <?php echo (isset($activePage) && $activePage === 'empleados') ? 'bg-cineplanet-blue/30 text-white' : 'text-gray-300 hover:bg-cineplanet-blue/20'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span class="ml-3">Empleados</span>
        </a>
        <a href="#" class="mt-2 flex items-center rounded-lg px-4 py-2 text-gray-300 hover:bg-cineplanet-blue/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
            <span class="ml-3">Horarios</span>
        </a>
        <a href="#" class="mt-2 flex items-center rounded-lg px-4 py-2 text-gray-300 hover:bg-cineplanet-blue/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>
            <span class="ml-3">Reportes</span>
        </a>
    </nav>
    <div class="p-4 mt-auto border-t border-gray-700">
        <a href="#" class="flex items-center rounded-lg px-4 py-2 text-gray-300 hover:bg-cineplanet-blue/20">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 0 2l-.15.08a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1 0-2l.15-.08a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
            <span class="ml-3">Configuración</span>
        </a>
    </div>
</aside>
