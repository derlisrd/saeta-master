@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
    
    @if($needsOnboarding)
        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-white tracking-tight">¡Bienvenido a tu Panel de Despliegue!</h1>
            <p class="text-zinc-500 mt-2 text-lg">Para comenzar a desplegar proyectos, necesitamos configurar los cimientos.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- Paso 1: Máquinas Virtuales --}}
            <div class="group relative bg-zinc-900 border {{ $stats['vms'] > 0 ? 'border-emerald-500/50' : 'border-zinc-800' }} p-8 rounded-3xl transition-all hover:border-sky-500/50 shadow-2xl">
                <div class="absolute -top-4 left-8 px-3 py-1 bg-zinc-900 border border-zinc-800 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $stats['vms'] > 0 ? 'text-emerald-500' : 'text-zinc-500' }}">
                    Paso 01
                </div>
                
                <div class="mb-6 flex justify-between items-start">
                    <div class="p-3 bg-sky-500/10 rounded-2xl">
                        <svg class="w-8 h-8 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                        </svg>
                    </div>
                    @if($stats['vms'] > 0)
                        <span class="bg-emerald-500/20 text-emerald-400 p-1 rounded-full">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </span>
                    @endif
                </div>

                <h3 class="text-white font-bold text-xl mb-2">Servidores (VM)</h3>
                <p class="text-zinc-500 text-sm mb-6">Conecta tus servidores vía IP para poder instalar tus aplicaciones.</p>
                
                <a href="{{ route('vms-create') }}" class="inline-flex items-center gap-2 text-sm font-bold {{ $stats['vms'] > 0 ? 'text-zinc-500 hover:text-white' : 'text-sky-400 hover:text-sky-300' }} transition-colors">
                    {{ $stats['vms'] > 0 ? 'Gestionar Servidores' : 'Agregar mi primera VM' }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </div>

            {{-- Paso 2: Zonas Cloudflare --}}
            <div class="group relative bg-zinc-900 border {{ $stats['zonas'] > 0 ? 'border-emerald-500/50' : 'border-zinc-800' }} p-8 rounded-3xl transition-all hover:border-sky-500/50 shadow-2xl">
                <div class="absolute -top-4 left-8 px-3 py-1 bg-zinc-900 border border-zinc-800 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $stats['zonas'] > 0 ? 'text-emerald-500' : 'text-zinc-500' }}">
                    Paso 02
                </div>
                
                <div class="mb-6 flex justify-between items-start">
                    <div class="p-3 bg-amber-500/10 rounded-2xl">
                        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                    @if($stats['zonas'] > 0)
                        <span class="bg-emerald-500/20 text-emerald-400 p-1 rounded-full">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </span>
                    @endif
                </div>

                <h3 class="text-white font-bold text-xl mb-2">Zonas DNS</h3>
                <p class="text-zinc-500 text-sm mb-6">Agrega tus dominios de Cloudflare para automatizar los subdominios.</p>
                
                <a href="{{ route('zonas-create') }}" class="inline-flex items-center gap-2 text-sm font-bold {{ $stats['zonas'] > 0 ? 'text-zinc-500 hover:text-white' : 'text-amber-400 hover:text-amber-300' }} transition-colors">
                    {{ $stats['zonas'] > 0 ? 'Gestionar Dominios' : 'Conectar Cloudflare' }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </div>

            {{-- Paso 3: Stacks de Repositorios --}}
            <div class="group relative bg-zinc-900 border {{ $stats['repos'] > 0 ? 'border-emerald-500/50' : 'border-zinc-800' }} p-8 rounded-3xl transition-all hover:border-sky-500/50 shadow-2xl">
                <div class="absolute -top-4 left-8 px-3 py-1 bg-zinc-900 border border-zinc-800 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $stats['repos'] > 0 ? 'text-emerald-500' : 'text-zinc-500' }}">
                    Paso 03
                </div>
                
                <div class="mb-6 flex justify-between items-start">
                    <div class="p-3 bg-emerald-500/10 rounded-2xl">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    @if($stats['repos'] > 0)
                        <span class="bg-emerald-500/20 text-emerald-400 p-1 rounded-full">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </span>
                    @endif
                </div>

                <h3 class="text-white font-bold text-xl mb-2">Stack Base</h3>
                <p class="text-zinc-500 text-sm mb-6">Configura tus repositorios de GitHub y sus comandos de compilación.</p>
                
                <a href="{{ route('repositorios-create') }}" class="inline-flex items-center gap-2 text-sm font-bold {{ $stats['repos'] > 0 ? 'text-zinc-500 hover:text-white' : 'text-emerald-400 hover:text-emerald-300' }} transition-colors">
                    {{ $stats['repos'] > 0 ? 'Gestionar Repos' : 'Configurar Stacks' }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </div>
        </div>

        {{-- Botón de Acción Principal (Sólo si todo está listo) --}}
        @if(!$needsOnboarding)
            <div class="mt-12 text-center animate-bounce">
                <a href="{{ route('dominios-create') }}" class="bg-white text-black px-10 py-4 rounded-2xl font-black uppercase tracking-tighter hover:bg-sky-400 transition-all shadow-xl shadow-sky-500/20">
                    🚀 ¡Todo listo! Desplegar mi primer Dominio
                </a>
            </div>
        @endif

    @else
        {{-- Dashboard Normal cuando ya hay datos --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- Aquí irían tus widgets de estadísticas normales --}}
            <div class="bg-zinc-900 p-6 rounded-2xl border border-zinc-800">
                <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest">Total Dominios</p>
                <h2 class="text-white text-3xl font-bold mt-1">24</h2>
            </div>
            {{-- ... --}}
        </div>
    @endif

</div>
@endsection