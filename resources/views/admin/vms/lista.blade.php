@extends('layouts.admin')

@section('page-title', 'Gestión de servidores')

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

    @if (session('warning'))
        <div class="mb-6 flex items-center p-4 border-l-4 border-amber-500 bg-amber-500/10 text-amber-400 rounded-r-lg">
            <svg class="w-5 h-5 mr-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-sm font-bold uppercase tracking-tight">{{ session('warning') }}</span>
        </div>
    @endif

    <div class="p-6 border-b border-zinc-800 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-white">Lista de servidores</h2>
        <a href="{{ route('vms-formulario') }}"
            class="bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold py-2 px-4 rounded-lg transition-all shadow-lg shadow-sky-500/20 uppercase">
            + Nuevo servidor
        </a>
    </div>
    <div class="bg-zinc-900/50 border border-zinc-700 rounded-2xl overflow-hidden shadow-xl">
        <table class="w-full text-left">
            <thead class="bg-zinc-800/50 border-b border-zinc-700">
                <tr>
                    <th class="px-6 py-4 text-zinc-400 text-[10px] font-bold uppercase">Nombre del Servidor</th>
                    <th class="px-6 py-4 text-zinc-400 text-[10px] font-bold uppercase font-mono">Dirección IP</th>
                    <th class="px-6 py-4 text-zinc-400 text-[10px] font-bold uppercase text-center">Proyectos</th>
                    <th class="px-6 py-4 text-zinc-400 text-[10px] font-bold uppercase">Estado SSH</th>
                    <th class="px-6 py-4 text-zinc-400 text-[10px] font-bold uppercase text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($vms as $vm)
                    <tr class="hover:bg-zinc-800/30 transition-all group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-8 w-8 rounded-lg bg-zinc-800 border border-zinc-700 flex items-center justify-center group-hover:border-emerald-500/50 transition-colors">
                                    <svg class="w-4 h-4 text-zinc-500 group-hover:text-emerald-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-white">{{ $vm->nombre }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono text-zinc-400 bg-zinc-800 px-2 py-1 rounded">
                                {{ $vm->usuario }}@<span
                                    class="text-emerald-400">{{ $vm->ip }}</span>:{{ $vm->puerto }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div
                                class="inline-flex items-center justify-center h-6 min-w-6 px-1 bg-sky-500/10 text-sky-400 border border-sky-500/20 rounded text-[10px] font-bold">
                                {{ $vm->dominios_count }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                {{-- Esto es manual por ahora, pero simula un estado --}}
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span
                                    class="text-[10px] font-bold text-emerald-400 uppercase tracking-tighter">Verificado</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="openConsole({{ $vm->id }}, '{{ $vm->ip }}')"
                                    title="Abrir Consola SSH"
                                    class="p-2 hover:bg-zinc-700 rounded-lg text-zinc-400 hover:text-sky-400 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </button>

                                <button onclick="checkConnection({{ $vm->id }}, this)" title="Testear Conexión"
                                    class="p-2 hover:bg-zinc-700 rounded-lg text-zinc-400 hover:text-emerald-400 transition-all">
                                    <svg id="icon-{{ $vm->id }}" class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>

                                <form action="{{ route('vms-destroy', $vm->id) }}" method="POST"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este servidor? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Eliminar Servidor"
                                        class="p-2 hover:bg-red-500/10 rounded-lg text-zinc-400 hover:text-red-500 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-zinc-600 text-xs italic">No hay servidores en
                            el inventario.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    <div id="consoleModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 items-center justify-center p-4">
        <div class="bg-zinc-950 w-full max-w-4xl rounded-2xl border border-zinc-800 shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="bg-zinc-900 px-4 py-2 flex justify-between items-center border-b border-zinc-800">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-full bg-red-500/20 border border-red-500/50"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-500/20 border border-amber-500/50"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500/20 border border-emerald-500/50"></div>
                    <span class="ml-4 text-[10px] font-mono text-zinc-500 uppercase tracking-widest">SSH Session: <span
                            id="terminalIp" class="text-zinc-300"></span></span>
                </div>
                <button onclick="closeConsole()"
                    class="text-zinc-500 hover:text-white text-2xl transition-colors">&times;</button>
            </div>

            {{-- BARRA DE COMANDOS RÁPIDOS --}}
            <div class="bg-zinc-900/50 border-b border-zinc-800 p-2 flex flex-wrap gap-2">
                <button onclick="quickCmd('systemctl reload nginx')"
                    class="px-2 py-1 bg-zinc-800 hover:bg-sky-600/20 border border-zinc-700 hover:border-sky-500/50 rounded text-[10px] text-zinc-400 hover:text-sky-400 transition-all font-mono">
                    RELOAD NGINX
                </button>
                <button onclick="quickCmd('systemctl status postgresql')"
                    class="px-2 py-1 bg-zinc-800 hover:bg-emerald-600/20 border border-zinc-700 hover:border-emerald-500/50 rounded text-[10px] text-zinc-400 hover:text-emerald-400 transition-all font-mono">
                    STATUS POSTGRES
                </button>
                <button onclick="quickCmd('df -h')"
                    class="px-2 py-1 bg-zinc-800 hover:bg-amber-600/20 border border-zinc-700 hover:border-amber-500/50 rounded text-[10px] text-zinc-400 hover:text-amber-400 transition-all font-mono">
                    DISK USAGE
                </button>
                <button onclick="quickCmd('tail -n 20 /var/log/nginx/error.log')"
                    class="px-2 py-1 bg-zinc-800 hover:bg-red-600/20 border border-zinc-700 hover:border-red-500/50 rounded text-[10px] text-zinc-400 hover:text-red-400 transition-all font-mono">
                    NGINX ERRORS
                </button>
            </div>

            {{-- Pantalla de Salida --}}
            <div id="terminalOutput"
                class="h-96 p-6 overflow-y-auto font-mono text-sm text-emerald-500/90 space-y-2 bg-black/40">
                <div class="text-zinc-600">-- Terminal lista. Escribe un comando para empezar --</div>
            </div>

            {{-- Input --}}
            <div class="p-4 bg-zinc-900/50 border-t border-zinc-800">
                <form id="consoleForm" class="flex items-center gap-3">
                    <span class="text-emerald-500 font-mono font-bold">#</span>
                    <input type="text" id="consoleInput" autocomplete="off"
                        class="flex-1 bg-transparent border-none outline-none text-white font-mono text-sm placeholder-zinc-700 focus:ring-0"
                        placeholder="Escribe un comando...">
                    <div id="loadingSpinner"
                        class="hidden animate-spin h-4 w-4 border-2 border-emerald-500 border-t-transparent rounded-full">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        async function checkConnection(id, button) {
            const icon = document.getElementById(`icon-${id}`);
            icon.classList.add('animate-spin'); // Feedback visual de carga

            try {
                const response = await fetch(`/admin/vms/${id}/test-ssh`); // Ajusta la ruta según tu web.php
                const data = await response.json();

                if (data.success) {
                    alert("✅ " + data.message);
                } else {
                    alert("❌ " + data.message);
                }
            } catch (error) {
                alert("💥 Error técnico al intentar el test.");
            } finally {
                icon.classList.remove('animate-spin');
            }
        }


        let currentVmId = null;

        function openConsole(id, ip) {
            currentVmId = id;
            document.getElementById('terminalIp').innerText = ip;
            document.getElementById('consoleModal').classList.add('flex');
            document.getElementById('consoleModal').classList.remove('hidden');
            document.getElementById('consoleInput').focus();
        }

        function closeConsole() {
            document.getElementById('consoleModal').classList.add('hidden');
            document.getElementById('consoleModal').classList.remove('flex');
            document.getElementById('terminalOutput').innerHTML = '<div class="text-zinc-600">-- Terminal lista --</div>';
        }

        document.getElementById('consoleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = document.getElementById('consoleInput');
            const output = document.getElementById('terminalOutput');
            const cmd = input.value;

            if (!cmd) return;

            // Mostrar el comando en la pantalla
            output.innerHTML += `<div><span class="text-white">root@system:~$</span> ${cmd}</div>`;
            input.value = '';
            document.getElementById('loadingSpinner').classList.remove('hidden');

            try {
                const response = await fetch(`/vms/${currentVmId}/console`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        command: cmd
                    })
                });

                const data = await response.json();

                // Formatear saltos de línea y mostrar salida
                const formattedOutput = data.output.replace(/\n/g, '<br>');
                output.innerHTML += `<div class="text-zinc-400 mb-4 ml-2">${formattedOutput}</div>`;

                // Auto-scroll al final
                output.scrollTop = output.scrollHeight;
            } catch (error) {
                output.innerHTML +=
                    `<div class="text-red-500 italic">Error de red o servidor inalcanzable.</div>`;
            } finally {
                document.getElementById('loadingSpinner').classList.add('hidden');
            }
        });
    </script>
@endsection
