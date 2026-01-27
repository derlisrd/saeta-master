<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> @yield('page-title') Master Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800 antialiased">

    @php
        $currentRoute = request()->route()->getName();
    @endphp

    <flux:sidebar sticky collapsible="mobile"
        class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>
        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="{{ route('admin-index') }}">Home</flux:sidebar.item>
            <flux:sidebar.group expandable :expanded="false" icon="globe-americas" heading="Zonas" class="grid">
                <flux:sidebar.item href="{{ route('dominios-crear') }}">Crear</flux:sidebar.item>
                <flux:sidebar.item href="{{ route('dominios-lista') }}">Lista</flux:sidebar.item>
            </flux:sidebar.group>
            <flux:sidebar.group expandable :expanded="false" icon="square-3-stack-3d" heading="Dominios" class="grid">
                <flux:sidebar.item href="{{ route('dominios-crear') }}">Crear</flux:sidebar.item>
                <flux:sidebar.item href="{{ route('dominios-lista') }}">Lista</flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>
        <flux:sidebar.spacer />
        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" href="#">Settings</flux:sidebar.item>
            <flux:sidebar.item icon="arrow-left-end-on-rectangle" href="{{ route('admin.logout') }}">Salir</flux:sidebar.item>
        </flux:sidebar.nav>
    </flux:sidebar>
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />
        <flux:profile icon="user" />
    </flux:header>


    <flux:main container>
        <flux:heading size="xl" level="1">@yield('page-title')</flux:heading>
        @yield('content')
    </flux:main>


    @fluxScripts
</body>

</html>
