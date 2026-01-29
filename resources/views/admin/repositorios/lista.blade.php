@extends('layouts.admin')
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
<div class="flex justify-between items-center mb-6">
    <h2 class="text-white font-bold text-xl uppercase tracking-tighter">Stacks / Repositorios</h2>
    <a href="{{ route('repositorios-formulario') }}" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all">
        NUEVO REPOSITORIO
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($repositorios as $repo)
    <div class="bg-zinc-900/50 border border-zinc-700 p-5 rounded-2xl relative group">
        <div class="flex items-start justify-between">
            <div class="p-3 bg-zinc-800 rounded-xl border border-zinc-700 text-emerald-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded bg-zinc-800 text-zinc-400 uppercase border border-zinc-700">{{ $repo->tipo }}</span>
        </div>
        
        <h3 class="text-white font-bold mt-4">{{ $repo->nombre }}</h3>
        <p class="text-zinc-500 text-xs font-mono mt-1 truncate">{{ $repo->url_git }}</p>
        
        <div class="mt-4 flex gap-2">
            <div class="flex items-center gap-1 text-[10px] text-sky-400 font-bold bg-sky-500/10 px-2 py-1 rounded border border-sky-500/20">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                BRANCH: {{ $repo->branch }}
            </div>
        </div>

        <form action="{{ route('repositorios-destroy', $repo->id) }}" method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-all">
            @csrf @method('DELETE')
            <button class="text-zinc-600 hover:text-red-500">&times;</button>
        </form>
    </div>
    @endforeach
</div>
@endsection