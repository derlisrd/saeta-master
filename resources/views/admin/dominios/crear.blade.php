@extends('layouts.admin')

@section('page-title', 'Crear un Nuevo Dominio')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form method="post" action="{{ route('dominios-store') }}"
            class="bg-zinc-900/50 border border-zinc-700 p-8 rounded-2xl shadow-xl backdrop-blur-sm">
            @csrf

            @if ($errors->any())
                <div class="mb-6 flex items-center p-4 border-l-4 border-red-500 bg-red-500/10 text-red-400 rounded-r-lg">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-medium">{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- NUEVO: Select Cliente (Ocupa 2 columnas para destacar) --}}
                <div class="flex flex-col gap-2 md:col-span-2 bg-sky-500/5 p-4 rounded-xl border border-sky-500/10 mb-2">
                    <label class="text-sky-400 text-xs font-bold uppercase tracking-wider ml-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Asignar Propietario (Cliente)
                    </label>
                    <select name="user_id" required
                        class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all">
                        <option value="" disabled {{ old('user_id') ? '' : 'selected' }}>Seleccione el cliente responsable...</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('user_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->name }} ({{ $cliente->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Input: Nombre --}}
                <div class="flex flex-col gap-2">
                    <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Nombre del Proyecto</label>
                    <input name="nombre" value="{{ old('nombre') }}" placeholder="Ej. Mi App Increíble"
                        class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-all" />
                </div>

                {{-- Input: Subdominio --}}
                <div class="flex flex-col gap-2">
                    <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Subdominio</label>
                    <input name="subdominio" value="{{ old('subdominio') }}" placeholder="Ej. api"
                        class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-all" />
                </div>

                {{-- Select: Zona --}}
                <div class="flex flex-col gap-2">
                    <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Zona Cloudflare</label>
                    <select name="zone_id"
                        class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all">
                        <option value="" disabled {{ old('zone_id') ? '' : 'selected' }}>Seleccione la zona raíz...</option>
                        @foreach ($zonas as $zona)
                            <option value="{{ $zona->zone_id }}" {{ old('zone_id') == $zona->zone_id ? 'selected' : '' }}>
                                {{ $zona->dominio }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Select: Protocolo --}}
                <div class="flex flex-col gap-2">
                    <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Protocolo</label>
                    <select name="protocolo"
                        class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all">
                        <option value="https://" {{ old('protocolo') == 'https://' ? 'selected' : '' }}>HTTPS (Recomendado)</option>
                        <option value="http://" {{ old('protocolo') == 'http://' ? 'selected' : '' }}>HTTP (No seguro)</option>
                    </select>
                </div>

                {{-- Select: Tipo Registro --}}
                <div class="flex flex-col gap-2">
                    <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Tipo de Registro</label>
                    <select name="type"
                        class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all">
                        <option value="A" {{ old('type') == 'A' ? 'selected' : '' }}>Registro A (IPv4)</option>
                        <option value="AAAA" {{ old('type') == 'AAAA' ? 'selected' : '' }}>Registro AAAA (IPv6)</option>
                        <option value="CNAME" {{ old('type') == 'CNAME' ? 'selected' : '' }}>CNAME (Alias)</option>
                    </select>
                </div>

                {{-- Input: IP --}}
                <div class="flex flex-col gap-2">
                    <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Dirección IP</label>
                    <input name="ip" type="text" value="{{ old('ip') }}" placeholder="0.0.0.0"
                        class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-sky-500/50 focus:border-sky-500 transition-all" />
                </div>

                {{-- Input: Vencimiento --}}
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Fecha de Vencimiento</label>
                    <input name="vencimiento" type="date" value="{{ old('vencimiento') }}"
                        class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all" />
                </div>

            </div>

            <div class="mt-10 flex justify-center">
                <button type="submit"
                    class="group relative w-full max-w-md flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-sky-600 hover:bg-sky-500 transition-all duration-200 shadow-lg shadow-sky-500/20 uppercase tracking-widest">
                    Crear Nuevo Dominio
                </button>
            </div>
        </form>
    </div>
@endsection