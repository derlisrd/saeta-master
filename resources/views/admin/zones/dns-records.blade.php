@extends('layouts.admin')

@section('page-title', 'Registros DNS - Cloudflare')

@section('content')
 @if (session('success'))
        <div class="mb-6 flex items-center p-4 border-l-4 border-emerald-500 bg-emerald-500/10 text-emerald-400 rounded-r-lg">
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center bg-zinc-900/50 border border-zinc-800 p-6 rounded-3xl backdrop-blur-md">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-sky-500/10 flex items-center justify-center border border-sky-500/20">
                <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white uppercase tracking-tight">
                    DNS Records zona: <span class="text-sky-400 font-mono text-sm"> {{ $zonaId }}</span>
                </h1>
                <p class="text-zinc-500 text-xs font-mono">Total de registros (Tipo A): {{ $totalDnsRecords }}</p>
            </div>
        </div>
        <a href="{{ route('zonas-lista') }}" class="text-zinc-400 hover:text-white text-sm font-bold transition-all">← Volver a Zonas</a>
    </div>

    {{-- Tabla de Registros --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-3xl overflow-hidden shadow-2xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-zinc-800/50 border-b border-zinc-800">
                    <th class="px-6 py-4 text-zinc-400 text-[10px] uppercase font-bold tracking-widest">Tipo</th>
                    <th class="px-6 py-4 text-zinc-400 text-[10px] uppercase font-bold tracking-widest">Nombre</th>
                    <th class="px-6 py-4 text-zinc-400 text-[10px] uppercase font-bold tracking-widest">Contenido / IP</th>
                    <th class="px-6 py-4 text-zinc-400 text-[10px] uppercase font-bold tracking-widest text-center">Proxy</th>
                    <th class="px-6 py-4 text-zinc-400 text-[10px] uppercase font-bold tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800/50">
                @forelse($dnsRecords as $record)
                <tr class="hover:bg-zinc-800/30 transition-colors group">
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                            {{ $record['type'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-white font-medium text-sm">{{ $record['name'] }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-zinc-500 text-xs font-mono break-all">{{ Str::limit($record['content'], 50) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="{{ $record['proxied'] ? 'text-orange-400' : 'text-zinc-600' }}">
                            <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1a1 1 0 112 0v1a1 1 0 11-2 0zM13.464 15.05a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 14a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1z"/>
                            </svg>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        {{-- IMPORTANTE: Pasamos zone_id y record id --}}
                        <form id="delete-form-{{ $record['id'] }}" action="{{ route('zonas-dns-destroy', [$zonaId, $record['id']]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('{{ $record['id'] }}', '{{ $record['name'] }}')" class="text-zinc-600 hover:text-red-500 transition-colors p-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-zinc-500 italic text-sm">
                        No se encontraron registros tipo A en esta zona.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: '¿Eliminar registro?',
            text: `Vas a borrar el registro DNS: ${name}. Esta acción no se puede deshacer en Cloudflare.`,
            icon: 'warning',
            showCancelButton: true,
            background: '#18181b',
            color: '#fff',
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#3f3f46',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-3xl border border-zinc-800 shadow-2xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    @if(session('success'))
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#18181b',
            color: '#fff'
        });
        Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}"
        });
    @endif
</script>
@endsection