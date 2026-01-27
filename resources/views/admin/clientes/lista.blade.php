@extends('layouts.admin')

@section('page-title', 'Gestión de Clientes')

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
                <h2 class="text-xl font-semibold text-white">Clientes Registrados</h2>
                <p class="text-zinc-500 text-xs mt-1">Usuarios con acceso al sistema y sus activos</p>
            </div>
            <a href="{{ route('clientes-crear') }}"
                class="bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-sky-500/20 uppercase">
                + Nuevo cliente
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-zinc-800/50">
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Username</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider text-center">Dominios
                        </th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider">Registro</th>
                        <th class="px-6 py-4 text-zinc-400 text-xs font-bold uppercase tracking-wider text-right">Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse ($clientes as $cliente)
                        <tr class="hover:bg-zinc-800/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="h-10 w-10 rounded-full bg-zinc-800 border border-zinc-700 flex items-center justify-center text-sky-500 font-bold mr-3 shadow-inner">
                                        {{ strtoupper(substr($cliente->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('clientes-detalle', $cliente->id) }}"
                                            class="text-sm font-medium text-white hover:text-sky-400 transition-colors">
                                            {{ $cliente->name }}
                                        </a>
                                        <div class="text-xs text-zinc-500">{{ $cliente->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm text-zinc-300 font-mono bg-zinc-800/50 px-2 py-1 rounded border border-zinc-700/50">
                                    {{ $cliente->username ?? '---' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold {{ $cliente->dominios_count > 0 ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'bg-zinc-800 text-zinc-500 border border-zinc-700' }}">
                                    {{ $cliente->dominios_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-zinc-400 italic">
                                {{ $cliente->created_at->format('d M, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button class="p-2 text-zinc-400 hover:text-white transition-colors" title="Ver Perfil">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button class="p-2 text-zinc-400 hover:text-sky-400 transition-colors" title="Editar">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 pull-right 2 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-zinc-500 italic">
                                No se encontraron clientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
