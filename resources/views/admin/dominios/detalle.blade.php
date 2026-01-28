@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-fadeIn">
    
    {{-- Header de Estado --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-zinc-900/50 border border-zinc-800 p-6 rounded-3xl backdrop-blur-md">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-sky-500/10 flex items-center justify-center border border-sky-500/20">
                <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">{{ $dominio->nombre }}</h1>
                <p class="text-zinc-500 font-mono text-sm">{{ $dominio->subdominio }}.{{ $dominio->dominio }}</p>
            </div>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
            <span class="px-4 py-2 {{ $dominio->desplegado ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }} rounded-xl border border-current text-xs font-bold uppercase tracking-widest">
                {{ $dominio->desplegado ? '● Activo' : '○ Pendiente' }}
            </span>
            <form action="{{ route('dominios-redesplegar', $dominio->id) }}" method="POST">
                @csrf
                <button class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white rounded-xl text-xs font-bold transition-all border border-zinc-700">
                    🔄 Re-desplegar
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Columna Izquierda: Servidor y Git --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Tarjeta Infraestructura --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6">
                <h3 class="text-zinc-400 text-xs font-bold uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-sky-500 rounded-full"></span> Infraestructura
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-zinc-600 text-[10px] uppercase font-bold">Servidor Destino</span>
                            <span class="text-white font-medium">{{ $dominio->vm->nombre }}</span>
                            <span class="text-zinc-500 text-xs font-mono">{{ $dominio->vm->ip }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-zinc-600 text-[10px] uppercase font-bold">Ruta en Disco</span>
                            <span class="text-zinc-300 text-xs font-mono bg-black/30 p-2 rounded-lg mt-1 border border-zinc-800">
                                /var/www/html/{{ $dominio->subdominio }}.{{ $dominio->dominio }}/{{ $dominio->path }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-zinc-600 text-[10px] uppercase font-bold">Repositorio GIT</span>
                            <span class="text-sky-400 font-medium">{{ $dominio->repositorio->nombre }}</span>
                            <span class="text-zinc-500 text-xs italic">Rama: {{ $dominio->repositorio->branch }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tarjeta Variables de Entorno --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6">
                <h3 class="text-zinc-400 text-xs font-bold uppercase tracking-widest mb-6">Variables de Entorno (.env)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($dominio->envs as $env)
                    <div class="flex justify-between items-center bg-black/20 border border-zinc-800/50 p-3 rounded-xl">
                        <span class="text-zinc-500 text-xs font-mono">{{ $env->key }}</span>
                        <span class="text-emerald-500 text-xs font-bold font-mono">"{{ $env->value }}"</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Columna Derecha: DB y Cliente --}}
        <div class="space-y-6">
            
            {{-- Tarjeta Base de Datos --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/><path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/><path d="M17 4c0 1.657-3.134 3-7 3S3 5.657 3 4s3.134-3 7-3 7 1.343 7 3z"/></svg>
                </div>
                <h3 class="text-zinc-400 text-xs font-bold uppercase tracking-widest mb-6">Acceso Database</h3>
                <div class="space-y-4 font-mono text-sm">
                    <div class="flex justify-between border-b border-zinc-800 pb-2">
                        <span class="text-zinc-500">DB:</span>
                        <span class="text-white">{{ $dominio->db_name }}</span>
                    </div>
                    <div class="flex justify-between border-b border-zinc-800 pb-2">
                        <span class="text-zinc-500">USER:</span>
                        <span class="text-white">{{ $dominio->db_user }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="text-zinc-500 text-xs">PASSWORD:</span>
                        <div class="flex gap-2">
                            <input type="password" readonly value="{{ $dominio->db_pass }}" class="bg-black/30 border-none text-sky-400 text-xs w-full rounded p-1" id="db-pass">
                            <button onclick="copy('db-pass')" class="text-zinc-500 hover:text-white">📋</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tarjeta Cliente --}}
            <div class="bg-sky-600 rounded-3xl p-6 text-white shadow-xl shadow-sky-600/20">
                <h3 class="text-sky-200 text-[10px] font-bold uppercase tracking-widest mb-4">Propietario</h3>
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center text-xl font-black">
                        {{ substr($dominio->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold leading-tight">{{ $dominio->user->name }}</p>
                        <p class="text-sky-200 text-xs">{{ $dominio->user->email }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function copy(id) {
        const copyText = document.getElementById(id);
        copyText.type = 'text';
        copyText.select();
        document.execCommand("copy");
        copyText.type = 'password';
        alert("Copiado al portapapeles");
    }
</script>
@endsection