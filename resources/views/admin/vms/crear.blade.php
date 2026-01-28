@extends('layouts.admin')

@section('page-title', 'Infraestructura: Servidores (VMs)')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Formulario Lateral para Nueva VM --}}
        <div class="lg:col-span-1">
            <form method="POST" action="{{ route('vms-store') }}" enctype="multipart/form-data"
                class="bg-zinc-900/50 border border-zinc-700 p-6 rounded-2xl shadow-xl backdrop-blur-sm sticky top-6">
                @csrf
                <h3 class="text-white font-bold uppercase text-xs tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Registrar Servidor
                </h3>

                <div class="space-y-4">
                    @if ($errors->any())
                        <div
                            class="mb-6 flex items-center p-4 border-l-4 border-red-500 bg-red-500/10 text-red-400 rounded-r-lg">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">{{ $errors->first() }}</span>
                        </div>
                    @endif
                    <div>
                        <label class="text-zinc-500 text-[10px] font-bold uppercase">Nombre Identificador</label>
                        <input name="nombre" type="text" placeholder="Ej. Producción 01" required
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg p-2.5 text-sm text-white focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="text-zinc-500 text-[10px] font-bold uppercase">Dirección IP</label>
                        <input name="ip" type="text" placeholder="1.2.3.4" required
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-lg p-2.5 text-sm text-white font-mono outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-zinc-500 text-[10px] font-bold uppercase">Usuario</label>
                            <input name="usuario" type="text" value="root"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg p-2.5 text-sm text-white">
                        </div>
                        <div>
                            <label class="text-zinc-500 text-[10px] font-bold uppercase">Puerto</label>
                            <input name="puerto" type="number" value="22"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-lg p-2.5 text-sm text-white">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="text-zinc-500 text-[10px] font-bold uppercase">Llave Privada SSH (id_rsa)</label>
                        <div class="flex items-center justify-center w-full">
                            <label
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-zinc-700 border-dashed rounded-lg cursor-pointer bg-zinc-800/50 hover:bg-zinc-800 transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-zinc-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="mb-2 text-sm text-zinc-400"><span class="font-semibold">Click para
                                            subir</span> o arrastra tu id_rsa</p>
                                    <p class="text-xs text-zinc-500 uppercase">OpenSSH Private Key</p>
                                </div>
                                <input name="ssh_key_file" type="file" class="hidden" />
                            </label>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl text-xs uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/10">
                        Guardar VM
                    </button>
                </div>
            </form>
        </div>

        {{-- Listado de Servidores --}}
        <div class="lg:col-span-3">
            <div class="bg-zinc-900/50 border border-zinc-700 rounded-2xl overflow-hidden shadow-xl">
                <table class="w-full text-left">
                    <thead class="bg-zinc-800/50 border-b border-zinc-700">
                        <tr>
                            <th class="px-6 py-4 text-zinc-400 text-[10px] font-bold uppercase">Servidor</th>
                            <th class="px-6 py-4 text-zinc-400 text-[10px] font-bold uppercase">Acceso</th>
                            <th class="px-6 py-4 text-zinc-400 text-[10px] font-bold uppercase text-center">Instancias</th>
                            <th class="px-6 py-4 text-zinc-400 text-[10px] font-bold uppercase text-right">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800">
                        @forelse($vms as $vm)
                            <tr class="hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-zinc-800 rounded-lg border border-zinc-700">
                                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-white">{{ $vm->nombre }}</div>
                                            <div class="text-[10px] text-zinc-500 uppercase">{{ $vm->so ?? 'Ubuntu Linux' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-emerald-400">
                                    {{ $vm->usuario }}@<span
                                        class="text-zinc-300">{{ $vm->ip }}</span>:{{ $vm->puerto }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="bg-zinc-800 text-zinc-400 text-xs font-bold px-2.5 py-1 rounded-full border border-zinc-700">
                                        {{ $vm->dominios_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Conectado
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-zinc-500 text-sm italic">No hay
                                    servidores registrados aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
