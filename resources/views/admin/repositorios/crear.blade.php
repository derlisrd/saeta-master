@extends('layouts.admin')

@section('content')
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('repositorios-store') }}" method="POST"
            class="bg-zinc-900/50 border border-zinc-700 p-8 rounded-3xl shadow-2xl backdrop-blur-sm">
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

            <div class="space-y-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Buscador y Selector de Repo --}}
                <div>
                    <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">Repositorio GitHub</label>
                    <select name="url_git" id="repo-select" onchange="fetchBranches(this)" required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none focus:ring-2 focus:ring-emerald-500/50">
                        <option value="" disabled selected>Seleccione un repositorio...</option>
                        @foreach ($repositorios as $repo)
                            {{-- Usamos full_name para la API y clone_url para el registro --}}
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

                <div>
                    <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">Nombre del Stack</label>
                    <input name="nombre" id="stack-nombre" type="text" placeholder="Ej: Laravel Core v10" required
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:ring-2 focus:ring-emerald-500/50 outline-none transition-all">
                </div>

                <div>

                    <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">Tipo de App</label>
                    <select name="tipo"
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                        <option value="laravel">Laravel</option>
                        <option value="nodejs">Node.js</option>
                        <option value="static">HTML Estático</option>
                        <option value="wordpress">WordPress</option>
                    </select>

                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-4 rounded-2xl uppercase text-xs tracking-widest transition-all mt-4 shadow-lg shadow-emerald-900/20">
                    REGISTRAR STACK
                </button>
            </div>
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
@endsection
