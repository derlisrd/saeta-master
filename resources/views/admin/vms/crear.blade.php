@extends('layouts.admin')

@section('page-title', 'Registrar Servidor')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Nueva Instancia (VM)</h1>
        <p class="text-zinc-500">Configura el acceso SSH y el stack de software para despliegues automáticos.</p>
    </div>

    <form action="{{ route('vms-store') }}" method="POST" enctype="multipart/form-data" 
        class="bg-zinc-900 border border-zinc-800 rounded-3xl p-8 shadow-2xl">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- SECCIÓN 1: IDENTIFICACIÓN --}}
            <div class="space-y-6">
                <h3 class="text-sky-500 font-bold text-xs uppercase tracking-widest border-b border-zinc-800 pb-2">Información Básica</h3>
                
                <div>
                    <label class="text-zinc-400 text-[10px] font-bold uppercase mb-2 block">Nombre del Servidor</label>
                    <input type="text" name="nombre" placeholder="Ej: DO-NYC-Production" required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:ring-2 focus:ring-sky-500/50 outline-none transition-all">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="text-zinc-400 text-[10px] font-bold uppercase mb-2 block">Dirección IP</label>
                        <input type="text" name="ip" placeholder="0.0.0.0" required
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                    </div>
                    <div>
                        <label class="text-zinc-400 text-[10px] font-bold uppercase mb-2 block">Puerto</label>
                        <input type="number" name="puerto" value="22" required
                            class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                    </div>
                </div>

                <div>
                    <label class="text-zinc-400 text-[10px] font-bold uppercase mb-2 block">Sistema Operativo</label>
                    <select name="so" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                        <option value="ubuntu">Ubuntu (Recomendado)</option>
                        <option value="debian">Debian</option>
                        <option value="centos">CentOS</option>
                    </select>
                </div>
            </div>

            {{-- SECCIÓN 2: ACCESO SSH --}}
            <div class="space-y-6">
                <h3 class="text-amber-500 font-bold text-xs uppercase tracking-widest border-b border-zinc-800 pb-2">Credenciales SSH</h3>
                
                <div>
                    <label class="text-zinc-400 text-[10px] font-bold uppercase mb-2 block">Usuario del Sistema</label>
                    <input type="text" name="usuario" value="root" required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                </div>

                <div>
                    <label class="text-zinc-400 text-[10px] font-bold uppercase mb-2 block">Archivo de Llave Privada</label>
                    <div class="border-2 border-dashed border-zinc-700 rounded-2xl p-6 text-center hover:border-sky-500 transition-colors bg-zinc-800/30">
                        <input type="file" name="ssh_key_file" id="ssh_key_file" class="hidden" required onchange="updateFileName(this)">
                        <label for="ssh_key_file" class="cursor-pointer">
                            <svg class="w-8 h-8 text-zinc-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            <span id="file-name" class="text-xs text-zinc-500 font-mono">Seleccionar id_rsa...</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECCIÓN 3: STACK DE SOFTWARE --}}
        <div class="mt-8 pt-8 border-t border-zinc-800">
            <h3 class="text-emerald-500 font-bold text-xs uppercase tracking-widest mb-6">Stack de Software Instalado</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="text-zinc-400 text-[10px] font-bold uppercase mb-4 block">Servidor Web</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer group">
                            <input type="radio" name="web_server_type" value="nginx" checked class="hidden peer">
                            <div class="p-4 border border-zinc-700 rounded-2xl bg-zinc-800/50 text-center peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 transition-all">
                                <span class="block text-white font-bold text-sm">NGINX</span>
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="web_server_type" value="apache" class="hidden peer">
                            <div class="p-4 border border-zinc-700 rounded-2xl bg-zinc-800/50 text-center peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all">
                                <span class="block text-white font-bold text-sm">Apache2</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-zinc-400 text-[10px] font-bold uppercase mb-2 block">Versión de PHP</label>
                    <select name="php_version" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                        <option value="8.3">PHP 8.3 (Recomendado)</option>
                        <option value="8.2" selected>PHP 8.2</option>
                        <option value="8.1">PHP 8.1</option>
                        <option value="7.4">PHP 7.4</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-10 flex gap-4">
            <button type="submit" 
                class="flex-1 bg-sky-600 hover:bg-sky-500 text-white font-black py-4 rounded-2xl uppercase text-xs tracking-widest transition-all shadow-lg shadow-sky-900/40">
                Registrar Servidor en Inventario
            </button>
            <a href="{{ route('vms-lista') }}" 
                class="px-8 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 font-bold py-4 rounded-2xl uppercase text-xs tracking-widest transition-all text-center">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function updateFileName(input) {
        const name = input.files[0] ? input.files[0].name : "Seleccionar id_rsa...";
        document.getElementById('file-name').textContent = name;
        document.getElementById('file-name').classList.add('text-sky-400');
    }
</script>
@endsection