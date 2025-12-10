<aside class="flex h-full w-64 flex-col bg-slate-900 text-white shadow-xl transition-all duration-300 z-20">
    <div class="flex items-center justify-center h-16 border-b border-slate-800 bg-slate-900">
        <!-- Logo de Cineplanet -->
        <div class="flex items-center gap-2">
            <div class="h-8 w-8 rounded bg-cineplanet-blue flex items-center justify-center text-white font-bold text-xl">C</div>
            <span class="font-bold text-lg tracking-tight">Cineplanet</span>
        </div>
    </div>
    
    <nav class="flex-1 px-3 py-6 space-y-1">
        <a href="/cineplanet/public_html/dashboard.php" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors <?php echo (isset($activePage) && $activePage === 'dashboard') ? 'bg-cineplanet-blue text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0 transition-colors <?php echo (isset($activePage) && $activePage === 'dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-white'; ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Dashboard
        </a>
        
        <div class="pt-4 pb-2">
            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Gestión</p>
        </div>

        <a href="/cineplanet/public_html/empleados.php" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors <?php echo (isset($activePage) && $activePage === 'empleados') ? 'bg-cineplanet-blue text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0 transition-colors <?php echo (isset($activePage) && $activePage === 'empleados') ? 'text-white' : 'text-slate-500 group-hover:text-white'; ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Empleados
        </a>
        
        <a href="/cineplanet/public_html/horarios.php" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors <?php echo (isset($activePage) && $activePage === 'horarios') ? 'bg-cineplanet-blue text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0 transition-colors <?php echo (isset($activePage) && $activePage === 'horarios') ? 'text-white' : 'text-slate-500 group-hover:text-white'; ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/></svg>
            Horarios
        </a>
        
        <a href="/cineplanet/public_html/reportes.php" class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition-colors <?php echo (isset($activePage) && $activePage === 'reportes') ? 'bg-cineplanet-blue text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0 transition-colors <?php echo (isset($activePage) && $activePage === 'reportes') ? 'text-white' : 'text-slate-500 group-hover:text-white'; ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>
            Reportes
        </a>
    </nav>
    
    <div class="p-4 border-t border-slate-800 bg-slate-900/50">
        <a href="/cineplanet/public_html/configuracion.php" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors <?php echo (isset($activePage) && $activePage === 'configuracion') ? 'bg-cineplanet-blue text-white shadow-lg shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0 transition-colors <?php echo (isset($activePage) && $activePage === 'configuracion') ? 'text-white' : 'text-slate-500 group-hover:text-white'; ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 0 2l-.15.08a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1 0-2l.15-.08a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
            Configuración
        </a>
    </div>
</aside>
