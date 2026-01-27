<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Panel | Saeta</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full antialiased">

    <div class="flex min-h-screen">
        <aside class="w-64 bg-slate-900 text-slate-300 shrink-0 flex flex-col border-r border-slate-800">
            <div class="p-6 flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center text-white">
                    <i data-lucide="zap" class="w-5 h-5"></i>
                </div>
                <span class="text-xl font-bold text-white tracking-tight">Saeta<span class="text-indigo-400">Master</span></span>
            </div>

            <nav class="flex-1 px-4 space-y-1 mt-4">
                <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Administración</p>
                
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl bg-indigo-600 text-white font-medium">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>

                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-800 hover:text-white transition-all">
                    <i data-lucide="globe" class="w-5 h-5"></i> Dominios
                </a>

                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-800 hover:text-white transition-all">
                    <i data-lucide="users" class="w-5 h-5"></i> Clientes
                </a>

                <div class="pt-4 mt-4 border-t border-slate-800">
                    <p class="px-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Usuario</p>
                    <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-800 hover:text-white transition-all">
                        <i data-lucide="user-circle" class="w-5 h-5"></i> Mi Cuenta
                    </a>
                    
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-red-400 hover:bg-red-500/10 transition-all">
                            <i data-lucide="log-out" class="w-5 h-5"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </nav>

            <div class="p-4 border-t border-slate-800 text-xs text-slate-500">
                v2.6.0 Build 2026
            </div>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8">
                <h2 class="text-lg font-semibold text-slate-800">@yield('page-title', 'Inicio')</h2>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-slate-600">{{ auth()->user()->name }}</span>
                    <div class="w-8 h-8 rounded-full bg-slate-200 border border-slate-300"></div>
                </div>
            </header>

            <main class="p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>