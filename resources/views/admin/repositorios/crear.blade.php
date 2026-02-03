@extends('layouts.admin')

@section('content')
    <div class="max-w-5xl mx-auto">
        <form action="{{ route('repositorios-store') }}" method="POST"
            class="bg-zinc-900 border border-zinc-700 p-8 rounded-3xl shadow-2xl backdrop-blur-sm">
            @csrf
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-white font-bold text-lg uppercase tracking-widest">Configurar Stack Base</h2>
                {{-- Botón para refrescar la lista desde GitHub --}}
                <button type="button" onclick="refreshRepos()" class="text-zinc-500 hover:text-sky-400 transition-colors">
                    <svg id="sync-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>

            <div class="space-y-5 grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Selector con Buscador --}}
                <div>
                    <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1 mb-2 block">Repositorio GitHub</label>
                    <select name="url_git" id="repo-select" placeholder="Escribe para buscar un repositorio..."
                        autocomplete="off">
                        <option value="">Buscar repositorio...</option>
                        @foreach ($repositorios as $repo)
                            <option value="{{ $repo['full_name'] }}" data-clone="{{ $repo['clone_url'] }}">
                                {{ $repo['full_name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Selector de Ramas --}}
                <div>
                    <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">Rama (Branch)</label>
                    <select name="branch" id="branch-select" required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                        <option value="main">Esperando repositorio...</option>
                    </select>
                </div>

                {{-- Campo oculto para guardar la URL de clonación real --}}
                <input type="hidden" name="clone_url" id="clone-url-input">



                {{-- Selector de Stack Base --}}
                <div>
                    <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1 mb-2 block">Tecnología (Stack)</label>
                    <select name="stack_id" id="stack-select" required onchange="updateDefaultCommands(this)"
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none focus:ring-2 focus:ring-sky-500/50">
                        <option value="" disabled selected>Seleccione Tecnología...</option>
                        @foreach ($stacks as $stack)
                            <option value="{{ $stack->id }}" data-slug="{{ $stack->slug }}">
                                {{ $stack->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">Nombre del Stack</label>
                    <input name="nombre" id="stack-nombre" type="text" placeholder="Ej: Laravel Core v10" required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all">
                </div>

                <div>
                    <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1 mb-2 block">Tipo stack</label>
                    {{-- CAMBIADO: name="tipo_stack" e ID único --}}
                    <select name="tipo_stack" id="tipo-stack-select" required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none focus:ring-2 focus:ring-sky-500/50">
                        <option value="" disabled selected>Seleccione tipo de stack...</option>
                        @foreach ($tipos_stacks as $stack)
                            <option value="{{ $stack['tipo'] }}">
                                {{ $stack['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                </div>



            </div>

            <div class="grid grid-cols-1 gap-6 mt-8 border-t border-zinc-800 pt-8">
                <h3 class="text-emerald-500 font-bold text-xs uppercase tracking-widest flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Pipeline de Despliegue
                </h3>

                <div class="space-y-4">
                    {{-- Fase de Instalación --}}
                    <div>
                        <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">1. Comandos de Instalación
                            (Dependencias)</label>
                        <textarea name="install_commands" rows="2" placeholder="composer install --no-dev&#10;npm install"
                            class="w-full bg-zinc-950 border border-zinc-700 rounded-xl p-3 text-emerald-400 font-mono text-xs focus:ring-1 focus:ring-emerald-500 outline-none"></textarea>
                    </div>

                    {{-- Fase de Build --}}
                    <div>
                        <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">2. Comandos de Compilación
                            (Build)</label>
                        <textarea name="build_commands" rows="2" placeholder="npm run build"
                            class="w-full bg-zinc-950 border border-zinc-700 rounded-xl p-3 text-amber-400 font-mono text-xs focus:ring-1 focus:ring-amber-500 outline-none"></textarea>
                    </div>

                    {{-- Fase de Configuración --}}
                    <div>
                        <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">3. Otros (Migraciones, Seeds,
                            Caché)</label>
                        <textarea name="setup_commands" rows="2" placeholder="php artisan migrate --force&#10;php artisan optimize"
                            class="w-full bg-zinc-950 border border-zinc-700 rounded-xl p-3 text-sky-400 font-mono text-xs focus:ring-1 focus:ring-sky-500 outline-none"></textarea>
                    </div>
                    <div>
                        <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">4. Carpeta de build</label>
                        <textarea name="output_path" rows="2" placeholder="public... dist... prod..."
                            class="w-full bg-zinc-950 border border-zinc-700 rounded-xl p-3 text-sky-400 font-mono text-xs focus:ring-1 focus:ring-sky-500 outline-none"></textarea>
                    </div>
                </div>
            </div>
            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-4 rounded-2xl uppercase text-xs tracking-widest transition-all mt-4 shadow-lg shadow-emerald-900/20">
                REGISTRAR STACK
            </button>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        async function fetchBranches(selectElement) {
            const repoFullname = selectElement.value;
            const branchSelect = document.getElementById('branch-select');
            const cloneUrlInput = document.getElementById('clone-url-input');

            // Guardar la URL de clonación en el input oculto
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            cloneUrlInput.value = selectedOption.getAttribute('data-clone');

            // Estado de carga
            branchSelect.innerHTML = '<option value="">Cargando ramas...</option>';

            try {
                const params = new URLSearchParams({
                    repo: repoFullname
                });
                const response = await fetch("{{ route('github-branches') }}?" + params);
                const branches = await response.json();

                if (response.ok) {
                    branchSelect.innerHTML = ''; // Limpiar
                    branches.forEach(branch => {
                        const option = document.createElement('option');
                        option.value = branch.name;
                        option.textContent = branch.name;
                        if (branch.name === 'main' || branch.name === 'master') option.selected = true;
                        branchSelect.appendChild(option);
                    });

                    Toast.fire({
                        icon: 'success',
                        title: `Ramas cargadas para ${repoFullname}`,
                        timer: 1500
                    });
                } else {
                    throw new Error();
                }
            } catch (error) {
                branchSelect.innerHTML = '<option value="main">Error al cargar</option>';
                Toast.fire({
                    icon: 'error',
                    title: 'No se pudieron obtener las ramas'
                });
            }
        }


        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#18181b',
            color: '#fff'
        });

        // Autocompletar el nombre del stack según el repo seleccionado
        function updateNombreStack(select) {
            const selectedOption = select.options[select.selectedIndex];
            const repoName = selectedOption.getAttribute('data-name');
            const inputNombre = document.getElementById('stack-nombre');

            if (repoName && !inputNombre.value) {
                inputNombre.value = repoName.charAt(0).toUpperCase() + repoName.slice(1);
            }
        }

        // Simulación de refresco (Aquí podrías hacer una petición AJAX a tu controlador)
        function refreshRepos() {
            const icon = document.getElementById('sync-icon');
            icon.classList.add('animate-spin');

            Toast.fire({
                icon: 'info',
                title: 'Sincronizando con GitHub...'
            });

            // Recargar la página para obtener la nueva lista cacheada
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif
    </script>


    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Inicializar el buscador en el select
        var repoSelect = new TomSelect("#repo-select", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            onChange: function(value) {
                // value es el full_name del repo
                if (!value) return;
                const option = this.options[value];
                const cloneUrl = option.dataClone;

                document.getElementById('clone-url-input').value = cloneUrl;
                // Llamar a tu función de ramas
                fetchBranchesFromValue(value, cloneUrl);

                // Autocompletar nombre
                const stackNombre = document.getElementById('stack-nombre');
                if (!stackNombre.value) {
                    stackNombre.value = value.split('/').pop().toUpperCase();
                }
            },
            // Mapeo manual de data attributes para TomSelect
            render: {
                option: function(data, escape) {
                    return `<div>${escape(data.text)}</div>`;
                }
            },
            onInitialize: function() {
                // Guardamos los clones en un mapa interno para acceder fácil
                const self = this;
                document.querySelectorAll('#repo-select option').forEach(opt => {
                    if (opt.value) {
                        self.options[opt.value].dataClone = opt.dataset.clone;
                    }
                });
            }
        });

        async function fetchBranchesFromValue(repoFullname, cloneUrl) {
            const branchSelect = document.getElementById('branch-select');
            const cloneUrlInput = document.getElementById('clone-url-input');

            cloneUrlInput.value = cloneUrl;
            branchSelect.innerHTML = '<option value="">Cargando ramas...</option>';

            try {
                const params = new URLSearchParams({
                    repo: repoFullname
                });
                const response = await fetch("{{ route('github-branches') }}?" + params);
                const branches = await response.json();

                if (response.ok) {
                    branchSelect.innerHTML = '';
                    branches.forEach(branch => {
                        const option = document.createElement('option');
                        option.value = branch.name;
                        option.textContent = branch.name;
                        if (branch.name === 'main' || branch.name === 'master') option.selected = true;
                        branchSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error(error);
            }
        }
    </script>
@endsection

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        /* Personalización para que coincida con tu diseño Zinc */
        .ts-control {
            background-color: #27272a !important;
            /* zinc-800 */
            border: 1px solid #3f3f46 !important;
            /* zinc-700 */
            color: white !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem !important;
        }

        .ts-dropdown {
            background-color: #18181b !important;
            /* zinc-900 */
            border: 1px solid #3f3f46 !important;
            color: white !important;
        }

        .ts-dropdown .active {
            background-color: #3f3f46 !important;
            /* zinc-700 */
            color: #10b981 !important;
            /* emerald-500 */
        }

        .ts-control input {
            color: white !important;
        }

        .clear-button {
            color: #ef4444 !important;
        }
    </style>
@endsection
