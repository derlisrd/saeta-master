@extends('layouts.admin')

@section('page-title', 'Perfil del Cliente')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    
    {{-- Fila Superior: Información General y Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Tarjeta de Usuario --}}
        <div class="bg-zinc-900/50 border border-zinc-700 p-6 rounded-2xl backdrop-blur-sm">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-16 w-16 rounded-2xl bg-sky-600 flex items-center justify-center text-2xl font-bold text-white shadow-lg shadow-sky-500/20">
                    {{ strtoupper(substr($cliente->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ $cliente->name }}</h2>
                    <p class="text-zinc-500 text-sm italic">{{ $cliente->username }}</p>
                </div>
            </div>
            <div class="space-y-3 border-t border-zinc-800 pt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Email:</span>
                    <span class="text-zinc-300">{{ $cliente->email }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500">Miembro desde:</span>
                    <span class="text-zinc-300">{{ $cliente->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Stats de Dominios --}}
        <div class="bg-zinc-900/50 border border-zinc-700 p-6 rounded-2xl backdrop-blur-sm flex flex-col justify-center items-center text-center">
            <span class="text-zinc-500 uppercase text-xs font-bold tracking-widest mb-2">Dominios Activos</span>
            <span class="text-5xl font-black text-white leading-none">{{ $cliente->dominios->count() }}</span>
            <div class="mt-4 px-4 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-full border border-emerald-500/20 uppercase">
                Sincronizados con Cloudflare
            </div>
        </div>

        {{-- Acción Rápida --}}
        <div class="bg-sky-600/10 border border-sky-500/20 p-6 rounded-2xl flex flex-col justify-center gap-3">
            <h3 class="text-sky-400 font-bold text-sm uppercase text-center">Gestión Rápida</h3>
            <a href="{{ route('dominios-crear', ['user_id' => $cliente->id]) }}" class="bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold py-3 rounded-xl transition-all text-center shadow-lg shadow-sky-500/20">
                + ASIGNAR NUEVO DOMINIO
            </a>
            <button class="border border-zinc-700 text-zinc-400 hover:text-white hover:bg-zinc-800 text-xs font-bold py-3 rounded-xl transition-all">
                EDITAR DATOS CLIENTE
            </button>
        </div>
    </div>

    {{-- Tabla de Dominios del Cliente --}}
    <div class="bg-zinc-900/50 border border-zinc-700 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-6 border-b border-zinc-800 bg-zinc-800/20">
            <h3 class="text-white font-bold uppercase text-sm tracking-widest">Activos Digitales del Cliente</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-zinc-800/50 text-zinc-500 text-[10px] uppercase font-bold tracking-tighter">
                    <tr>
                        <th class="px-6 py-4">Proyecto</th>
                        <th class="px-6 py-4">URL Completa</th>
                        <th class="px-6 py-4">Zona</th>
                        <th class="px-6 py-4">Vencimiento</th>
                        <th class="px-6 py-4 text-right">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($cliente->dominios as $dominio)
                        <tr class="hover:bg-zinc-800/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-white">{{ $dominio->nombre }}</td>
                            <td class="px-6 py-4 text-sm text-sky-400 font-mono">
                                {{ $dominio->protocolo }}{{ $dominio->subdominio }}.{{ $dominio->dominio }}
                            </td>
                            <td class="px-6 py-4 text-xs text-zinc-400">
                                {{ $dominio->zona->dominio ?? 'Sin zona' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-zinc-300">
                                {{ \Carbon\Carbon::parse($dominio->vencimiento)->format('d M, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2 py-1 rounded bg-zinc-800 text-zinc-400 text-[10px] uppercase font-bold border border-zinc-700">
                                    {{ $dominio->type }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-zinc-500 italic text-sm">
                                Este cliente aún no tiene dominios asignados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection