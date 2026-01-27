@extends('layouts.admin')

@section('page-title','Crear zona')

@section('content')
<form action="{{ route('zonas-store') }}" method="POST" class="bg-zinc-900/50 border border-zinc-700 p-8 rounded-2xl shadow-xl">
    @csrf
    <div class="flex flex-col gap-2 mb-6">
        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Seleccionar Zona de Cloudflare</label>
        <select name="zona_seleccionada" class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:outline-none focus:ring-2 focus:ring-sky-500/50 transition-all">
            <option value="" disabled selected>Elige una zona para importar...</option>
            @foreach($zonas as $zona)
                <option value="{{ $zona['id'] }}|{{ $zona['name'] }}">
                    {{ $zona['name'] }} ({{ $zona['id'] }})
                </option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="w-full bg-sky-600 hover:bg-sky-500 text-white font-bold py-3 rounded-xl uppercase tracking-widest transition-all">
        Importar / Actualizar Zona
    </button>
</form>
@endsection