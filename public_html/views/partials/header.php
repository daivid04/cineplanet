<header class="flex items-center justify-between border-b border-slate-200 bg-white px-8 py-4 sticky top-0 z-10">
    <h1 class="text-xl font-bold text-slate-800 tracking-tight"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
    <div class="flex items-center gap-6">
        <button class="text-slate-400 hover:text-slate-600 transition-colors relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
        </button>
        <div class="flex items-center gap-3 pl-6 border-l border-slate-200">
            <div class="text-right hidden sm:block">
                <div class="text-sm font-semibold text-slate-700">Administrador</div>
                <div class="text-xs text-slate-500">Sede Central</div>
            </div>
            <img class="h-9 w-9 rounded-full object-cover border border-slate-200 shadow-sm" src="https://i.pravatar.cc/150?u=a042581f4e29026704d" alt="Foto de perfil">
        </div>
    </div>
</header>
