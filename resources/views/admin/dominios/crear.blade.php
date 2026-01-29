@extends('layouts.admin')

@section('page-title', 'Crear un Nuevo Dominio')

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Indicador de Pasos --}}
        <div class="flex items-center justify-center mb-8 gap-4">
            <div id="step-1-indicator" class="h-2 w-24 rounded-full bg-sky-600 transition-all duration-300"></div>
            <div id="step-2-indicator" class="h-2 w-24 rounded-full bg-zinc-700 transition-all duration-300"></div>
        </div>
        @if ($errors->any())
            <div class="mb-6 flex items-center p-4 border-l-4 border-red-500 bg-red-500/10 text-red-400 rounded-r-lg">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"></path>
                </svg>
                <div>
                    <span class="text-sm font-bold block">Revisa los siguientes campos:</span>
                    <ul class="text-xs list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form id="multi-step-form" method="post" action="{{ route('dominios-store') }}"
            class="bg-zinc-900/50 border border-zinc-700 p-8 rounded-2xl shadow-xl backdrop-blur-sm">
            @csrf

            {{-- PASO 1: INFRAESTRUCTURA Y DOMINIO --}}
            <div id="step-1" class="space-y-6">
                <h2 class="text-white font-bold text-lg border-b border-zinc-800 pb-2">Paso 1: Dominio e Infraestructura
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6" >
                    {{-- Cliente --}}
                    <div class="flex flex-col gap-2 ">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Cliente
                            Responsable</label>
                        <select name="user_id" required
                            class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white focus:ring-2 focus:ring-sky-500/50 outline-none">
                            <option value="" disabled selected>Seleccione cliente...</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->name }} {{ $cliente->email }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-3 md:col-span-2">
                        <label class="text-zinc-500 text-[10px] font-bold uppercase ml-1">Selecciona el tipo de
                            Stack</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Opción Backend --}}
                            <label
                                class="relative flex flex-col p-4 bg-zinc-800/50 border border-zinc-700 rounded-2xl cursor-pointer hover:bg-zinc-800 transition-all group">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-white font-bold text-sm">Backend / Fullstack</span>
                                    <input type="radio" name="stack" value="backend" checked
                                        onchange="toggleStackFields(this.value)"
                                        class="w-4 h-4">
                                </div>
                            </label>

                            {{-- Opción Frontend --}}
                            <label
                                class="relative flex flex-col p-4 bg-zinc-800/50 border border-zinc-700 rounded-2xl cursor-pointer hover:bg-zinc-800 transition-all group">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-white font-bold text-sm">Frontend SPA</span>
                                    <input type="radio" name="stack" value="frontend"
                                        onchange="toggleStackFields(this.value)"
                                        class="w-4 h-4">
                                </div>
                            </label>
                        </div>
                    </div>


                    <div class="flex flex-col gap-2 ">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Repositorio</label>
                        <select name="repositorio_id" id="select-repo" required
                            class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                            <option value="" disabled selected>Seleccione repo...</option>
                            @foreach ($repositorios as $repo)
                                <option value="{{ $repo->id }}" data-nombre="{{ $repo->nombre }}">{{ $repo->nombre }}
                                    branch: {{ $repo->branch }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Servidor (VM)</label>
                        <select name="vm_id" required
                            class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                            @foreach ($vms as $vm)
                                <option value="{{ $vm->id }}">{{ $vm->nombre }} ({{ $vm->ip }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Zona Cloudflare</label>
                        <select name="zone_id"
                            class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                            @foreach ($zonas as $zona)
                                <option value="{{ $zona->id }}">{{ $zona->dominio }}</option>
                            @endforeach
                        </select>
                    </div>



                    {{-- Nombre y Subdominio --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Nombre Proyecto</label>
                        <input name="nombre" id="input-nombre" value="{{ old('nombre') }}" placeholder="Nombre"
                            class="transition-all duration-200 bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none focus:border-sky-500" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Subdominio</label>
                        <input name="subdominio" id="input-subdominio" value="{{ old('subdominio') }}" placeholder="api"
                            class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none focus:border-sky-500" />
                    </div>

                    {{-- Input: Path (Ruta en el Servidor) --}}
                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label
                            class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            Ruta de Instalación (Server Path)
                        </label>
                        <div class="flex">
                            <span
                                class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-zinc-700 bg-zinc-800 text-zinc-500 text-sm">
                                /var/www/html/
                            </span>
                            <input name="path" id="input-path" name="path"
                                class="bg-zinc-800/50 border border-zinc-700 rounded-r-xl p-3 text-zinc-400 flex-1 outline-none focus:ring-2 focus:ring-sky-500/50"
                                placeholder="core" />
                        </div>
                    </div>




                </div>

                <div class="flex justify-end mt-8">
                    <button type="button" onclick="nextStep()"
                        class="bg-sky-600 hover:bg-sky-500 text-white px-8 py-3 rounded-xl font-bold transition-all uppercase tracking-widest text-sm">
                        Siguiente: Configurar DB
                    </button>
                </div>
            </div>

            {{-- PASO 2: DATABASE Y API --}}
            <div id="step-2" class="hidden space-y-6" >
                <h2 class="text-white font-bold text-lg border-b border-zinc-800 pb-2">Paso 2: Base de Datos y Seguridad
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="db-fields-container">

                    {{-- Connection & Port --}}
                    <div class="flex flex-col gap-2">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Motor DB</label>
                        <select name="db_connection"
                            class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none">
                            <option value="pgsql">PostgreSQL</option>
                            <option value="mysql">MySQL</option>
                            <option value="sqlite">SQLite</option>
                        </select>
                    </div>

                    {{-- DB User y Pass (Generados automáticamente) --}}
                    <div class="flex flex-col gap-2 ">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Nombre de la Base de
                            Datos</label>
                        <input name="db_name" id="db_name" readonly
                            class="bg-zinc-800/50 border border-zinc-700 rounded-xl p-3 text-zinc-400 outline-none"
                            value="{{ old('db_name') }}" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Host DB</label>
                        <input name="db_host" id="db_host" value="127.0.0.1"
                            class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none focus:border-sky-500" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Puerto</label>
                        <input name="db_port" value="5432"
                            class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none" />
                    </div>



                    {{-- Usuario de la Base de Datos --}}
                    <div class="flex flex-col gap-2 ">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Usuario
                            Generado</label>
                        <input name="db_user" id="db_user" readonly
                            class="bg-zinc-800/50 border border-zinc-700 rounded-xl p-3 text-zinc-400 outline-none"
                            value="{{ old('db_user') }}" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Contraseña DB</label>
                        <div class="relative">
                            <input name="db_pass" id="db_pass" type="text"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none pr-12" />
                            <button type="button" onclick="copyValue('db_pass')"
                                class="absolute right-3 top-3 text-zinc-500 hover:text-sky-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    
                </div>

                <div class="grid grid-col-1 md:grid-cols-2 gap-4">
                    {{-- API KEY --}}
                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">API Key del
                            Proyecto</label>
                        <div class="relative">
                            <input name="api_key" id="api_key" type="text"
                                class="w-full bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none pr-12" />
                            <button type="button" onclick="copyValue('api_key')"
                                class="absolute right-3 top-3 text-zinc-500 hover:text-sky-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-zinc-400 text-xs font-bold uppercase tracking-wider ml-1">Vencimiento</label>
                        <input name="vencimiento" type="date" value="{{ date('Y-m-d', strtotime('+1 year')) }}"
                            class="bg-zinc-800 border border-zinc-700 rounded-xl p-3 text-white outline-none" />
                    </div>
                    <div class="md:col-span-2 mt-4">
                        <label
                            class="text-zinc-400 text-xs font-bold uppercase tracking-wider mb-2 flex justify-between items-center">
                            Variables de Entorno Personalizadas
                            <button type="button" onclick="addEnvRow()"
                                class="text-sky-500 hover:text-sky-400 text-md border border-sky-500/30 px-4 py-2 rounded-lg transition-all">+
                                Agregar Variable</button>
                        </label>

                        <div id="env-container" class="space-y-3">
                            {{-- Aquí se insertarán las filas --}}
                        </div>
                    </div>
                </div>

                <div class="flex justify-between mt-8">
                    <button type="button" onclick="prevStep()"
                        class="text-zinc-400 hover:text-white font-bold px-6 py-3 transition-all">
                        ← Volver
                    </button>
                    <button type="button" onclick="confirmFinalSubmit()"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white px-10 py-3 rounded-xl font-bold transition-all shadow-lg shadow-emerald-500/20 uppercase tracking-widest text-sm">
                        Finalizar y Desplegar
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>

function toggleStackFields(stackValue) {
    const dbContainer = document.getElementById('db-fields-container');
    // Buscamos todos los inputs y selects dentro del contenedor de DB
    const dbInputs = dbContainer.querySelectorAll('input, select');

    if (stackValue === 'frontend') {
        // Animación de salida y ocultar
        dbContainer.style.opacity = '0';
        setTimeout(() => {
            dbContainer.classList.add('hidden');
            // Quitamos el atributo required para que el form sea válido
            dbInputs.forEach(el => el.required = false);
        }, 200);

        Toast.fire({
            icon: 'info',
            title: 'Modo Frontend Activo',
            text: 'Se han deshabilitado los campos de base de datos.'
        });
    } else {
        // Mostrar y volver a poner required
        dbContainer.classList.remove('hidden');
        setTimeout(() => dbContainer.style.opacity = '1', 10);
        dbInputs.forEach(el => el.required = true);
    }
}

        function confirmFinalSubmit() {
            const envContainer = document.getElementById('env-container');
            const hasEnvs = envContainer.children.length > 0;

            if (!hasEnvs) {
                Swal.fire({
                    title: '¿Continuar sin variables?',
                    text: "No has agregado variables de entorno personalizadas. ¿Deseas desplegar el dominio así?",
                    icon: 'question',
                    showCancelButton: true,
                    background: '#18181b',
                    color: '#fff',
                    confirmButtonColor: '#059669', // emerald-600
                    cancelButtonColor: '#3f3f46', // zinc-700
                    confirmButtonText: 'Sí, desplegar',
                    cancelButtonText: 'Revisar',
                    customClass: {
                        popup: 'rounded-3xl border border-zinc-800 shadow-2xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        processAndSubmit();
                    }
                });
            } else {
                processAndSubmit();
            }
        }

        function processAndSubmit() {
            // Mostramos un toast de carga
            Toast.fire({
                icon: 'info',
                title: 'Procesando despliegue...',
                text: 'Instalando configuración en el servidor',
                timer: 10000, // Tiempo largo mientras carga
                timerProgressBar: true,
            });

            // Enviamos el formulario
            document.getElementById('multi-step-form').submit();
        }


        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#18181b',
            color: '#fff',
            customClass: {
                popup: 'border border-zinc-700 shadow-2xl rounded-2xl'
            }
        });

        window.onload = function() {
            @if ($errors->any())
                generateCredentials();
            @endif
        };



        function nextStep() {

            const fields = [{
                    id: 'user_id',
                    name: 'el Cliente'
                },
                {
                    id: 'input-nombre',
                    name: 'el Nombre'
                },
                {
                    id: 'input-subdominio',
                    name: 'el Subdominio'
                },
                {
                    id: 'select-repo',
                    name: 'el Repositorio'
                },
                {
                    id: 'vm_id',
                    name: 'el Servidor'
                }
            ];
            let missing = [];

            const nombre = document.getElementById('input-nombre').value;
            const sub = document.getElementById('input-subdominio').value;

            fields.forEach(field => {
                const input = document.getElementById(field.id) || document.querySelector(`[name="${field.id}"]`);
                if (!input || !input.value || input.value.trim() === "") {
                    missing.push(field.name);
                    input.classList.add('border-red-500', 'bg-red-500/5');
                } else {
                    input.classList.remove('border-red-500', 'bg-red-500/5');
                }
            });

            if (missing.length > 0) {
                // Disparamos el Toast de error
                Toast.fire({
                    icon: 'error',
                    title: 'Faltan campos',
                    text: `Por favor indica ${missing.join(', ')}.`
                });
                return;
            }

            generateCredentials();

            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');
            document.getElementById('step-1-indicator').classList.replace('bg-sky-600', 'bg-zinc-700');
            document.getElementById('step-2-indicator').classList.replace('bg-zinc-700', 'bg-emerald-500');

        }

        function prevStep() {
            document.getElementById('step-2').classList.add('hidden');
            document.getElementById('step-1').classList.remove('hidden');
            document.getElementById('step-2-indicator').classList.replace('bg-emerald-500', 'bg-zinc-700');
            document.getElementById('step-1-indicator').classList.replace('bg-zinc-700', 'bg-sky-600');
        }

        function generateCredentials() {
            const sub = document.getElementById('input-subdominio').value || 'app';
            const repoSelect = document.getElementById('select-repo');
            const repoName = repoSelect.options[repoSelect.selectedIndex].getAttribute('data-nombre') || 'repo';

            // Limpiar strings para DB
            const cleanSub = sub.toLowerCase().replace(/[^a-z0-9]/g, '');
            const cleanRepo = repoName.toLowerCase().replace(/[^a-z0-9]/g, '');

            const baseName = `${cleanRepo}_${cleanSub}`;

            document.getElementById('db_name').value = `${baseName}_db`;
            // Generar Usuario
            document.getElementById('db_user').value = `${cleanRepo}_${cleanSub}_user`;

            // Generar Password si está vacía
            if (!document.getElementById('db_pass').value) {
                document.getElementById('db_pass').value = 'sc_' + Math.random().toString(36).slice(5) + Math.random()
                    .toString(
                        36).slice(-4).toUpperCase();
            }

            // Generar API KEY si está vacía
            if (!document.getElementById('api_key').value) {
                document.getElementById('api_key').value = 'sca_' + [...Array(32)].map(() => Math.floor(Math.random() * 16)
                    .toString(16)).join('');
            }
        }

        function copyValue(id) {
            const input = document.getElementById(id);
            input.select();
            document.execCommand("copy");
            alert("Copiado: " + input.value);
        }
        document.querySelector('select[name="db_connection"]').addEventListener('change', function() {
            const portInput = document.getElementById('db_port');
            if (this.value === 'pgsql') portInput.value = '5432';
            if (this.value === 'mysql') portInput.value = '3306';
            if (this.value === 'sqlite') portInput.value = '';
        });

        function addEnvRow() {
            const container = document.getElementById('env-container');
            const index = container.children.length;
            const html = `
            <div class="flex gap-2 animate-fadeIn" id="env-row-${index}">
                <input name="custom_envs[${index}][key]" placeholder="KEY (Ej: MAIL_HOST)" class="w-1/3 bg-zinc-800 border border-zinc-700 rounded-xl p-2 text-white text-sm outline-none focus:border-sky-500" />
                <input name="custom_envs[${index}][value]" placeholder="VALUE" class="flex-1 bg-zinc-800 border border-zinc-700 rounded-xl p-2 text-white text-sm outline-none focus:border-sky-500" />
                <button type="button" onclick="document.getElementById('env-row-${index}').remove()" class="text-red-500 hover:bg-red-500/10 p-2 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        `;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
@endsection
