<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-800 antialiased">
    @php
        $active = 'bg-sky-600 text-white';
        $inactive = 'text-gray-300 hover:bg-gray-800 hover:text-white';
    @endphp

    <div class="flex min-h-screen">

        <aside id="drawer"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-zinc-900 transform -translate-x-full transition-transform duration-300 ease-in-out 
    lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:flex lg:flex-col overflow-y-auto border-r border-zinc-800">

            <div class="flex items-center justify-between h-16 px-6 bg-zinc-900">
                <span class="text-white font-bold uppercase tracking-wider">Admin</span>
                <button onclick="toggleDrawer()" class="text-gray-400 hover:text-white lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <nav class="mt-4 px-4 space-y-2">
                <a href="{{ route('admin-index') }}"
                    class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin-index') ? $active : $inactive }}">
                    Inicio
                </a>

                <div class="space-y-1">
                    <button onclick="toggleSubmenu('sub-dominios', 'arrow-dominios')"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-lg transition {{ request()->routeIs('dominios-*') ? 'bg-gray-800 text-white' : $inactive }}">
                        <span>Dominios</span>
                        <svg id="arrow-dominios" class="w-4 h-4 transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    {{-- Si la ruta es dominios-*, quitamos la clase hidden --}}
                    <div id="sub-dominios"
                        class="{{ request()->routeIs('dominios-*') ? '' : 'hidden' }} pl-6 space-y-1">
                        <a href="{{ route('dominios-lista') }}"
                            class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('dominios-lista') ? $active : $inactive }}">
                            Lista de Dominios
                        </a>
                        <a href="{{ route('dominios-formulario') }}"
                            class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('dominios-formulario') ? $active : $inactive }}">
                            Agregar Nuevo
                        </a>
                    </div>
                </div>

                <div class="space-y-1">
                    <button onclick="toggleSubmenu('sub-zonas', 'arrow-zonas')"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-lg transition {{ $inactive }}">
                        <span>Zonas</span>
                        <svg id="arrow-zonas" class="w-4 h-4 transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    {{-- Si la ruta es zonas-*, quitamos la clase hidden --}}
                    <div id="sub-zonas" class="{{ request()->routeIs('zonas-*') ? '' : 'hidden' }} pl-6 space-y-1">
                        <a href="{{ route('zonas-lista') }}"
                            class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('zonas-lista') ? $active : $inactive }}">
                            Ver Zonas
                        </a>
                        <a href="{{ route('zonas-formulario') }}"
                            class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('zonas-formulario') ? $active : $inactive }}">
                            Crear zona
                        </a>
                    </div>
                </div>
                <div class="space-y-1">
                    <button onclick="toggleSubmenu('sub-clientes', 'arrow-clientes')"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-lg transition {{ $inactive }}">
                        <span>Clientes</span>
                        <svg id="arrow-clientes" class="w-4 h-4 transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div id="sub-clientes"
                        class="{{ request()->routeIs('clientes-*') ? '' : 'hidden' }} pl-6 space-y-1">
                        <a href="{{ route('clientes-lista') }}"
                            class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('clientes-lista') ? $active : $inactive }}">
                            Ver clientes
                        </a>
                        <a href="{{ route('clientes-formulario') }}"
                            class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('clientes-formulario') ? $active : $inactive }}">
                            Crear cliente
                        </a>
                    </div>
                </div>
                <div class="space-y-1">
                    <button onclick="toggleSubmenu('sub-repos', 'arrow-repos')"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-lg transition {{ request()->routeIs('repositorios-*') ? 'bg-gray-800 text-white' : $inactive }}">
                        <span>Stacks / Repos</span>
                        <svg id="arrow-repos"
                            class="w-4 h-4 transition-transform duration-200 {{ request()->routeIs('repositorios-*') ? 'rotate-180' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div id="sub-repos"
                        class="{{ request()->routeIs('repositorios-*') ? '' : 'hidden' }} pl-6 space-y-1">
                        <a href="{{ route('repositorios-lista') }}"
                            class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('repositorios-lista') ? $active : $inactive }}">
                            Ver Repositorios
                        </a>
                        <a href="{{ route('repositorios-formulario') }}"
                            class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('repositorios-formulario') ? $active : $inactive }}">
                            Configurar Nuevo
                        </a>
                    </div>
                </div>
                <div class="space-y-1">
                    <button onclick="toggleSubmenu('sub-vms', 'arrow-vms')"
                        class="w-full flex items-center justify-between px-4 py-2 rounded-lg transition {{ $inactive }}">
                        <span>Servidores</span>
                        <svg id="arrow-vms" class="w-4 h-4 transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div id="sub-vms" class="{{ request()->routeIs('vms-*') ? '' : 'hidden' }} pl-6 space-y-1">
                        <a href="{{ route('vms-lista') }}"
                            class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('vms-lista') ? $active : $inactive }}">
                            Ver servidores
                        </a>
                        <a href="{{ route('vms-formulario') }}"
                            class="block px-4 py-2 text-sm rounded-lg {{ request()->routeIs('vms-formulario') ? $active : $inactive }}">
                            Agregar servidor
                        </a>
                    </div>
                </div>

                <a href="{{ route('logout') }}"
                    class="block px-4 py-2 text-gray-300 hover:bg-red-900/40 hover:text-red-400 rounded-lg transition">
                    Salir
                </a>
            </nav>


        </aside>

        <div id="overlay" onclick="toggleDrawer()" class="fixed inset-0 z-40 bg-black/60 hidden lg:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="lg:hidden bg-gray-900 p-4 border-b border-gray-800 flex items-center">
                <button onclick="toggleDrawer()" class="p-2 text-gray-100 bg-gray-800 rounded-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
                <span class="ml-4 text-white font-bold uppercase">Admin</span>
            </header>

            <main class="flex-1 p-6">
                <h1 class="text-white font-bold text-md my-2">@yield('page-title')</h1>
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleDrawer() {
            const drawer = document.getElementById('drawer');
            const overlay = document.getElementById('overlay');
            drawer.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleSubmenu(menuId, arrowId) {
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(arrowId);

            // Alternar visibilidad
            menu.classList.toggle('hidden');

            // Rotar flecha
            arrow.classList.toggle('rotate-180');
        }
    </script>
    @yield('scripts')
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>
