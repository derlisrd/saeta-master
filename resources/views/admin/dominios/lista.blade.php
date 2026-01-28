@extends('layouts.admin')

@section('page-title', 'Gestión de Dominios')

@section('content')
    @if (session('success'))
        <div
            class="mb-6 flex items-center p-4 border-l-4 border-emerald-500 bg-emerald-500/10 text-emerald-400 rounded-r-lg animate-bounce-short">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('warning'))
        <div class="mb-6 flex items-center p-4 border-l-4 border-amber-500 bg-amber-500/10 text-amber-400 rounded-r-lg">
            <svg class="w-5 h-5 mr-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-sm font-bold uppercase tracking-tight">{{ session('warning') }}</span>
        </div>
    @endif
    <div class="bg-zinc-900/50 border border-zinc-700 rounded-2xl shadow-xl backdrop-blur-sm overflow-hidden">
        <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-white">Lista de Dominios</h2>
            <a href="{{ route('dominios-formulario') }}"
                class="bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-sky-500/20 uppercase">
                + Nuevo Dominio
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-800/50">
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Proyecto</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Host</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">IP / Destino</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider text-right">Acciones
                        </th>
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
                            <td class="px-6 py-4 flex items-center gap-3">
                                @if ($dominio->desplegado)
                                    <span
                                        class="text-emerald-500 text-[10px] font-bold bg-emerald-500/10 px-2 py-1 rounded-full border border-emerald-500/20 uppercase">
                                        Desplegado
                                    </span>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-amber-500 text-[10px] font-bold bg-amber-500/10 px-2 py-1 rounded-full border border-amber-500/20 uppercase">
                                            Pendiente
                                        </span>

                                        {{-- Formulario para el Reintento --}}
                                        <form action="{{ route('dominios-reintentar', $dominio->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" title="Reintentar Despliegue"
                                                class="p-1.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-sky-400 rounded-lg border border-zinc-700 transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="px-2 py-1 rounded bg-zinc-800 text-zinc-400 border border-zinc-700 text-[10px] font-mono">
                                    {{ $dominio->type }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm font-mono text-sky-300/80">
                                {{ $dominio->ip }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button class="text-zinc-400 hover:text-white transition-colors" title="Editar">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <form action="{{ route('dominios-destroy', $dominio->id) }}" method="POST"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este dominio? Esto no borrará los archivos en el servidor ni el DNS en Cloudflare automáticamente.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 bg-zinc-800 hover:bg-red-900/40 text-zinc-500 hover:text-red-500 rounded-lg border border-zinc-700 transition-all"
                                        title="Eliminar Dominio">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
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
