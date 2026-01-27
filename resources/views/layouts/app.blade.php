<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    @fluxAppearance
</head>
<body class="font-sans">

    <main>
        @yield('content')
    </main>
@fluxScripts
</body>
</html>