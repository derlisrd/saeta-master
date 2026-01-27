@extends('layouts.admin')

@section('page-title', 'Zonas DNS')

@section('content')
<div class="max-w-5xl mx-auto">
    {{-- Notificación de éxito --}}
    @if (session('success'))
        <div class="mb-6 flex items-center p-4 border-l-4 border-emerald-500 bg-emerald-500/10 text-emerald-400 rounded-r-lg">
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-zinc-900/50 border border-zinc-700 rounded-2xl shadow-xl backdrop-blur-sm overflow-hidden">
        <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-white">Zonas Configuradas</h2>
                <p class="text-zinc-500 text-xs mt-1">Gestión de zonas raíz para registros DNS</p>
            </div>
            <a href="{{ route('zonas-crear') }}" class="bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-sky-500/20 uppercase tracking-widest">
                + Nueva Zona
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-800/30">
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Zone ID (API)</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Dominio Principal</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse ($zones as $zone)
                    <tr class="hover:bg-zinc-800/40 transition-colors group">
                        <td class="px-6 py-4 text-sm text-zinc-500 font-mono">
                            #{{ $zone->id }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-zinc-800 text-sky-400 px-3 py-1 rounded-md text-xs font-mono border border-zinc-700">
                                {{ $zone->zone_id }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-white uppercase tracking-tight">
                                {{ $zone->dominio }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-2 w-2 rounded-full bg-emerald-500 mr-2 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                                <span class="text-xs text-zinc-400 uppercase font-medium italic">Activo</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-3">
                                <button class="p-2 text-zinc-500 hover:text-white hover:bg-zinc-700 rounded-lg transition-all" title="Sincronizar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                </button>
                                <button class="p-2 text-zinc-500 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-all" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <svg class="w-12 h-12 text-zinc-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-zinc-500 text-sm">No hay zonas configuradas todavía.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection