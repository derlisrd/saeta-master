@extends('layouts.admin')

@section('page-title', 'Registrar Nuevo Cliente')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Formulario Estilizado --}}
    <form method="post" action="{{ route('clientes-store') }}" class="bg-zinc-900/50 border border-zinc-700 p-8 rounded-2xl shadow-xl backdrop-blur-sm">
        @csrf

        {{-- Encabezado del Formulario --}}
        <div class="flex items-center gap-4 mb-8 border-b border-zinc-800 pb-6">
            <div class="p-3 bg-sky-500/10 rounded-xl border border-sky-500/20">
                <svg class="w-8 h-8 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white uppercase tracking-tight">Datos de Identidad</h2>
                <p class="text-zinc-500 text-xs">Asegúrese de que el correo sea válido para las notificaciones.</p>
            </div>
        </div>

        {{-- Manejo de Errores --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-500/10 border-l-4 border-red-500 text-red-400 rounded-r-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                <span class="text-sm font-medium">{{ $errors->first() }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Nombre Completo --}}
            <div class="flex flex-col gap-2">
                <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Nombre Completo</label>
                <input name="name" type="text" value="{{ old('name') }}" placeholder="Ej. Juan Pérez" required
                    class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all" />
            </div>

            {{-- Nombre de Usuario --}}
            <div class="flex flex-col gap-2">
                <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Username</label>
                <input name="username" type="text" value="{{ old('username') }}" placeholder="juan.perez23" required
                    class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all" />
            </div>

            {{-- Email --}}
            <div class="flex flex-col gap-2 md:col-span-2">
                <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Correo Electrónico</label>
                <input name="email" type="email" value="{{ old('email') }}" placeholder="cliente@empresa.com" required
                    class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all" />
            </div>

            {{-- Password --}}
            <div class="flex flex-col gap-2">
                <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Contraseña Temporal</label>
                <input name="password" type="password" required
                    class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all" />
            </div>

            {{-- Confirm Password --}}
            <div class="flex flex-col gap-2">
                <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Repetir Contraseña</label>
                <input name="password_confirmation" type="password" required
                    class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all" />
            </div>

        </div>

        <div class="mt-10 flex gap-4">
            <a href="{{ route('clientes-lista') }}" class="flex-1 text-center py-3 px-4 border border-zinc-700 text-zinc-400 font-bold rounded-xl hover:bg-zinc-800 transition-all uppercase text-sm tracking-widest">
                Cancelar
            </a>
            <button type="submit"
                class="flex-1 bg-sky-600 hover:bg-sky-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-sky-500/20 transition-all active:scale-[0.98] uppercase text-sm tracking-widest">
                Crear Cliente
            </button>
        </div>
    </form>
</div>
@endsection