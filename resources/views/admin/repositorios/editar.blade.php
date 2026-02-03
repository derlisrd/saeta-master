@extends('layouts.admin')

@section('content')
@if (session('error'))
        <div class="mb-4 flex items-center p-4 border-l-4 border-red-500 bg-red-500/10 text-red-400 rounded-r-lg">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-sm font-bold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Alerta de Warning (Restricción de Negocio) --}}
    @if (session('warning'))
        <div class="mb-4 flex items-center p-4 border-l-4 border-amber-500 bg-amber-500/10 text-amber-400 rounded-r-lg">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span class="text-sm font-bold">{{ session('warning') }}</span>
        </div>
    @endif
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Editar Pipeline</h1>
            <p class="text-zinc-500 text-sm">Modifica los comandos de despliegue para <b>{{ $repositorio->nombre }}</b></p>
        </div>
        <a href="{{ route('repositorios-lista') }}" class="text-zinc-400 hover:text-white transition-colors text-sm uppercase font-bold tracking-widest">
            ← Volver
        </a>
    </div>

    <form action="{{ route('repositorios-update', $repositorio->id) }}" method="POST"
        class="bg-zinc-900 border border-zinc-800 p-8 rounded-3xl shadow-2xl">
        @csrf
        @method('PUT')

        {{-- Información Básica (Read Only o Editable) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div>
                <label class="text-zinc-500 text-[10px] font-bold uppercase mb-2 block">Nombre del Proyecto</label>
                <input name="nombre" type="text" value="{{ $repositorio->nombre }}" required
                    class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:ring-2 focus:ring-emerald-500/50 outline-none">
            </div>
            <div>
                <label class="text-zinc-500 text-[10px] font-bold uppercase mb-2 block">Rama de Despliegue</label>
                <input name="branch" type="text" value="{{ $repositorio->branch }}" required
                    class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:ring-2 focus:ring-emerald-500/50 outline-none font-mono">
            </div>
            <div>
                <label class="text-zinc-500 text-[10px] font-bold uppercase mb-2 block">Carpeta de Build (Output)</label>
                <input name="output_path" type="text" value="{{ $repositorio->output_path }}"
                    class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-sky-400 focus:ring-2 focus:ring-sky-500/50 outline-none font-mono">
            </div>
        </div>

        {{-- Editor de Comandos --}}
        <div class="space-y-6 border-t border-zinc-800 pt-8">
            <h3 class="text-emerald-500 font-bold text-xs uppercase tracking-widest flex items-center gap-2 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                Secuencia de Comandos SSH
            </h3>

            {{-- Fase 1 --}}
            <div class="group">
                <label class="text-zinc-400 text-[10px] font-bold uppercase ml-1 flex justify-between">
                    <span>1. Instalación de Dependencias</span>
                    <span class="text-zinc-600 group-hover:text-emerald-500 transition-colors">Fase Inicial</span>
                </label>
                <textarea name="install_commands" rows="3" 
                    class="w-full bg-zinc-950 border border-zinc-700 rounded-xl p-4 text-emerald-400 font-mono text-sm focus:border-emerald-500 outline-none mt-2 shadow-inner"
                    placeholder="Ej: composer install">{{ $repositorio->install_commands }}</textarea>
            </div>

            {{-- Fase 2 --}}
            <div class="group">
                <label class="text-zinc-400 text-[10px] font-bold uppercase ml-1 flex justify-between">
                    <span>2. Compilación / Build</span>
                    <span class="text-zinc-600 group-hover:text-amber-500 transition-colors">Frontend / Assets</span>
                </label>
                <textarea name="build_commands" rows="3" 
                    class="w-full bg-zinc-950 border border-zinc-700 rounded-xl p-4 text-amber-400 font-mono text-sm focus:border-amber-500 outline-none mt-2 shadow-inner"
                    placeholder="Ej: npm run build">{{ $repositorio->build_commands }}</textarea>
            </div>

            {{-- Fase 3 --}}
            <div class="group">
                <label class="text-zinc-400 text-[10px] font-bold uppercase ml-1 flex justify-between">
                    <span>3. Configuración Final</span>
                    <span class="text-zinc-600 group-hover:text-sky-500 transition-colors">Migraciones & Caché</span>
                </label>
                <textarea name="setup_commands" rows="3" 
                    class="w-full bg-zinc-950 border border-zinc-700 rounded-xl p-4 text-sky-400 font-mono text-sm focus:border-sky-500 outline-none mt-2 shadow-inner"
                    placeholder="Ej: php artisan migrate --force">{{ $repositorio->setup_commands }}</textarea>
            </div>
        </div>

        <div class="mt-10">
            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black py-4 rounded-2xl uppercase text-xs tracking-widest transition-all shadow-lg shadow-emerald-900/40">
                Guardar Cambios en el Pipeline
            </button>
        </div>
    </form>
</div>
@endsection