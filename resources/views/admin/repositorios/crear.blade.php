@extends('layouts.admin')
@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('repositorios-store') }}" method="POST" class="bg-zinc-900/50 border border-zinc-700 p-8 rounded-3xl shadow-2xl backdrop-blur-sm">
        @csrf
        <h2 class="text-white font-bold text-lg mb-6 uppercase tracking-widest">Configurar Stack Base</h2>

        <div class="space-y-5">
            <div>
                <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">Nombre del Stack</label>
                <input name="nombre" type="text" placeholder="Ej: Laravel Core v10" required class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:ring-2 focus:ring-emerald-500/50 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">Tipo de App</label>
                    <select name="tipo" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white">
                        <option value="laravel">Laravel</option>
                        <option value="nodejs">Node.js</option>
                        <option value="static">HTML Estático</option>
                        <option value="wordpress">WordPress</option>
                    </select>
                </div>
                <div>
                    <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">Rama (Branch)</label>
                    <input name="branch" type="text" value="main" class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white">
                </div>
            </div>

            <div>
                <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">URL Repositorio (Git)</label>
                <input name="url_git" type="url" placeholder="https://github.com/usuario/repo.git" required class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white font-mono text-sm">
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-4 rounded-2xl uppercase text-xs tracking-widest transition-all mt-4">
                REGISTRAR STACK
            </button>
        </div>
    </form>
</div>
@endsection