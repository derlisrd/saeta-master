@extends('layouts.admin')

@section('page-title', 'Gestión de Dominios')

@section('content')
@if (session('success'))
    <div class="mb-6 flex items-center p-4 border-l-4 border-emerald-500 bg-emerald-500/10 text-emerald-400 rounded-r-lg animate-bounce-short">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
@endif
<div class="bg-zinc-900/50 border border-zinc-700 rounded-2xl shadow-xl backdrop-blur-sm overflow-hidden">
    <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-white">Lista de Dominios</h2>
        <a href="{{ route('dominios-crear') }}" class="bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-sky-500/20 uppercase">
            + Nuevo Dominio
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-800/50">
                    <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Proyecto</th>
                    <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Host</th>
                    <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Protocolo</th>
                    <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">IP / Destino</th>
                    <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse ($dominios as $dominio)
                <tr class="hover:bg-zinc-800/30 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-white group-hover:text-sky-400 transition-colors">
                            {{ $dominio->nombre }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-zinc-300">
                        <span class="text-zinc-500 italic">{{ $dominio->subdominio }}.</span>{{ $dominio->dominio }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $dominio->protocolo == 'https://' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                            {{ str_replace('://', '', $dominio->protocolo) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 rounded bg-zinc-800 text-zinc-400 border border-zinc-700 text-[10px] font-mono">
                            {{ $dominio->type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-mono text-sky-300/80">
                        {{ $dominio->ip }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button class="text-zinc-400 hover:text-white transition-colors" title="Editar">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </button>
                        <button class="text-zinc-400 hover:text-red-400 transition-colors" title="Eliminar">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-zinc-500 italic">
                        No hay dominios registrados aún.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection