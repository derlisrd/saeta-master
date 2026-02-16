@extends('layouts.admin')

@section('page-title', 'Gestión de Negocios Registrados')

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
    <div class="bg-zinc-900/50 border border-zinc-700 rounded-2xl shadow-xl backdrop-blur-sm overflow-hidden">
        <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-white">Negocios Registrados</h2>
                <p class="text-zinc-500 text-xs mt-1">Negocios registrados por el cliente externo</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-800/50">
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Nombre de negocio
                        </th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Correo</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Activo</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Dominios</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Creado</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider text-right">Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse ($negocios as $negocio)
                        <tr class="hover:bg-zinc-800/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">

                                    <div>
                                        <a href="{{ route('clientes-detalle', $negocio->id) }}"
                                            class="text-sm font-medium text-white hover:text-sky-400 transition-colors">
                                            {{ $negocio->nombre }}
                                        </a>
                                        <div class="text-xs text-zinc-500">{{ $negocio->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm text-zinc-300 font-mono bg-zinc-800/50 px-2 py-1 rounded border border-zinc-700/50">
                                    {{ $negocio->user->email ?? '---' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                               <span
                                    class="px-3 py-1 rounded-full text-xs font-bold {{ $negocio->dominios_count > 0 ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-zinc-800 text-zinc-500 border border-zinc-700' }}">
                                    {{ $negocio->dominios_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-zinc-400 italic">
                                {{ $negocio->activo ? 'Sí' : 'No' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-zinc-400 italic">
                                {{ $negocio->created_at->format('d M, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if ($negocio->activo === 0)
                                    <a href="{{ route('clientes-negocios-activar', $negocio->id) }}"
                                        class="p-1 text-zinc-400 hover:text-white transition-colors border border-zinc-300 rounded">
                                        ACTIVAR
                                    </a>
                                @else
                                    <a href="{{ route('dominios-formulario-desde-negocio', $negocio->id) }}"
                                        class="p-1 text-zinc-400 hover:text-white transition-colors border border-zinc-300 rounded">
                                        CREAR DOMINIO
                                    </a>
                                @endif
                                
                            </td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
