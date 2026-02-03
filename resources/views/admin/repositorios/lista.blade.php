@extends('layouts.admin')
@section('content')
    {{-- Alerta de Éxito --}}
    @if (session('success'))
        <div class="mb-4 flex items-center p-4 border-l-4 border-emerald-500 bg-emerald-500/10 text-emerald-400 rounded-r-lg animate-fade-in">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Alerta de Error (Fallo Crítico) --}}
    @if (session('error'))
        <div class="mb-4 flex items-center p-4 border-l-4 border-red-500 bg-red-500/10 text-red-400 rounded-r-lg">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-sm font-bold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Alerta de Warning (Restricción de Negocio) --}}
    @if (session('warning'))
        <div class="mb-4 flex items-center p-4 border-l-4 border-amber-500 bg-amber-500/10 text-amber-400 rounded-r-lg">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span class="text-sm font-bold">{{ session('warning') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-white font-bold text-xl uppercase tracking-tighter">Stacks / Repositorios</h2>
            <p class="text-zinc-500 text-xs">Gestiona tus configuraciones de despliegue y pipelines.</p>
        </div>
        <a href="{{ route('repositorios-formulario') }}"
            class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-lg shadow-emerald-900/20">
            NUEVO REPOSITORIO
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($repositorios as $repo)
            <div
                class="bg-zinc-900/50 border border-zinc-700/50 p-6 rounded-3xl relative group hover:border-emerald-500/30 transition-all">

                {{-- Acciones superiores --}}
                <div class="absolute top-4 right-4 flex gap-2">
                    {{-- BOTÓN EDITAR --}}
                    <a href="{{ route('repositorios-editar', $repo->id) }}"
                        class="p-2 bg-zinc-800 text-zinc-400 hover:text-sky-400 rounded-lg border border-zinc-700 transition-colors"
                        title="Editar Pipeline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </a>

                    <form action="{{ route('repositorios-destroy', $repo->id) }}" method="POST" class="form-eliminar">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            class="btn-delete p-2 bg-zinc-800 text-zinc-600 hover:text-red-500 rounded-lg border border-zinc-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="flex items-start gap-4">
                    <div class="p-3 bg-emerald-500/10 rounded-2xl border border-emerald-500/20 text-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <div class="overflow-hidden">
                        <span
                            class="text-[9px] font-black px-2 py-0.5 rounded-full bg-zinc-800 text-emerald-400 uppercase border border-emerald-500/20">
                            {{ $repo->tipo_stack ?? 'Standard' }}
                        </span>
                        <h3 class="text-white font-bold mt-1 truncate">{{ $repo->nombre }}</h3>
                    </div>
                </div>

                <p
                    class="text-zinc-500 text-[10px] font-mono mt-4 bg-zinc-950 p-2 rounded-lg border border-zinc-800 truncate">
                    {{ $repo->url_git }}
                </p>

                <div class="mt-4 flex flex-wrap gap-2">
                    {{-- Badge Branch --}}
                    <div
                        class="flex items-center gap-1.5 text-[10px] text-sky-400 font-bold bg-sky-500/5 px-2.5 py-1 rounded-md border border-sky-500/10">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        {{ $repo->branch }}
                    </div>

                    {{-- Contador de Comandos --}}
                    <div
                        class="flex items-center gap-1.5 text-[10px] text-amber-400 font-bold bg-amber-500/5 px-2.5 py-1 rounded-md border border-amber-500/10">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        {{ $repo->comandos->count() }} COMANDOS
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-zinc-800/50 flex justify-between items-center">
                    <span class="text-zinc-600 text-[9px] uppercase font-bold tracking-widest">Pipeline Ready</span>
                    <div class="flex -space-x-2">
                        {{-- Indicadores visuales de fases --}}
                        <div class="w-2 h-2 rounded-full {{ $repo->install_commands ? 'bg-emerald-500' : 'bg-zinc-700' }}"
                            title="Instalación"></div>
                        <div class="w-2 h-2 rounded-full ml-1 {{ $repo->build_commands ? 'bg-amber-500' : 'bg-zinc-700' }}"
                            title="Build"></div>
                        <div class="w-2 h-2 rounded-full ml-1 {{ $repo->setup_commands ? 'bg-sky-500' : 'bg-zinc-700' }}"
                            title="Setup"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionamos todos los botones de eliminar
        const deleteButtons = document.querySelectorAll('.btn-delete');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.form-eliminar');
                
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Se eliminarán el repositorio y todos sus comandos asociados.",
                    icon: 'warning',
                    showCancelButton: true,
                    background: '#18181b', // zinc-900
                    color: '#fff',
                    confirmButtonColor: '#10b981', // emerald-500
                    cancelButtonColor: '#3f3f46',  // zinc-700
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
