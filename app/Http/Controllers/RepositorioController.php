<?php

namespace App\Http\Controllers;

use App\Models\Repositorio;
use App\Models\Stack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RepositorioController extends Controller
{
    public function lista()
    {
        $repositorios = Repositorio::all();
        return view('admin.repositorios.lista', compact('repositorios'));
    }


    public function getBranches(Request $request)
    {
        $repo = $request->query('repo');

        if (!$repo) return response()->json([], 400);

        // Cacheamos las ramas por 10 minutos para que el selector sea instantáneo si cambian de repo y vuelven
        return Cache::remember("branches_{$repo}", 600, function () use ($repo) {
            $response = Http::withToken(config('services.github.token'))
                ->get("https://api.github.com/repos/{$repo}/branches");
            /** @var \Illuminate\Http\Client\Response $response */
            return $response->json();
        });
    }

    

    private function getGithubRepos()
    {
        // Cacheamos por 1 hora para no saturar la API
        return Cache::remember('github_repos', 3600, function () {
            $response = Http::withToken(config('services.github.token'))
                ->get('https://api.github.com/user/repos', [
                    'visibility' => 'all',
                    'affiliation' => 'owner',
                    'sort' => 'updated'
                ]);
            /** @var \Illuminate\Http\Client\Response $response */
            return $response->json();
        });


    }

    public function formulario()
    {
        $repos = $this->getGithubRepos();
        $stacks = Stack::all();

        $tipos_stacks = [
            [
                'nombre' => 'Back-end',
                'tipo'   => 'backend'
            ],
            [
                'nombre' => 'Front-end',
                'tipo'   => 'backend'
            ],
            [
                'nombre' => 'No aplica',
                'tipo'   => 'no aplica'
            ],
            ];

        return view('admin.repositorios.crear', [
            'repositorios' => $repos,
            'tipos_stacks' => $tipos_stacks,
            'stacks'=>$stacks
        ]);
    }

    public function store(Request $request)
    {
        // 1. Cambiamos la validación
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'clone_url' => 'required|url',
                'branch' => 'required|string',
                'stack_id' => 'required|exists:stacks,id', // Validamos que el stack exista
            ]);

            // 2. Creamos el registro con stack_id
            $repo = Repositorio::create([
                'nombre'           => $request->nombre,
                'url_git'          => $request->clone_url,
                'branch'           => $request->branch,
                'stack_id'         => $request->stack_id,
                'install_commands' => $request->install_commands,
                'build_commands'   => $request->build_commands,
                'setup_commands'   => $request->setup_commands,
                'tipo_stack'     => $request->tipo_stack,
                // Si viene vacío, ponemos 'public' por defecto
                'output_path'      => $request->output_path ?? 'public',
            ]);

            $orden = 0;
            $fases = [
                'Instalación'   => $request->install_commands,
                'Compilación'   => $request->build_commands,
                'Configuración' => $request->setup_commands,
            ];

            foreach ($fases as $descripcion => $contenido) {
                if (!empty($contenido)) {
                    // Limpiar retornos de carro y separar por saltos de línea
                    $lineas = explode("\n", str_replace("\r", "", $contenido));

                    foreach ($lineas as $linea) {
                        $cmdText = trim($linea);
                        if (!empty($cmdText)) {
                            \App\Models\Comando::create([
                                'repositorio_id' => $repo->id,
                                'orden'          => $orden++,
                                'comando'        => $cmdText,
                                'descripcion'    => "Fase de $descripcion",
                                'ignore_error'   => false, // Por defecto no ignorar
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('repositorios-lista')->with('success', 'Repositorio registrado.');
        } catch (\Throwable $th) {
            Log::error($th);
            //throw $th;
        }
    }

    public function destroy($id)
    {
        try {
            $repo = Repositorio::findOrFail($id);

            // Verificamos si algún dominio lo está usando antes de intentar borrar
            if ($repo->dominios()->exists()) {
                return redirect()->back()->with('warning', "No se puede eliminar: Este repositorio está asociado a dominios activos.");
            }

            $repo->delete(); // Al tener onDelete('set null') o delete() manual, borra comandos asociados

            return redirect()->route('repositorios-lista')->with('success', 'Repositorio eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error inesperado al intentar eliminar el registro.');
        }
    }

    // RepositorioController.php

    public function editar($id)
    {
        $repositorio = Repositorio::with('comandos')->findOrFail($id);
        $stacks = Stack::all();
        $tipos_stacks = [
            ['tipo' => 'laravel', 'nombre' => 'Laravel / PHP'],
            ['tipo' => 'nodejs', 'nombre' => 'Node.js / Express'],
            ['tipo' => 'static', 'nombre' => 'Sitio Estático (HTML/JS)'],
            ['tipo' => 'wordpress', 'nombre' => 'WordPress'],
        ];

        return view('admin.repositorios.editar', compact('repositorio', 'stacks', 'tipos_stacks'));
    }

    public function update(Request $request, $id)
    {
        $repo = Repositorio::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'branch' => 'required|string',
        ]);

        // 1. Actualizamos datos básicos del repo
        $repo->update($request->only([
            'nombre',
            'branch',
            'stack_id',
            'tipo_stack',
            'output_path',
            'install_commands',
            'build_commands',
            'setup_commands'
        ]));

        // 2. Sincronizamos la tabla 'comandos'
        // Lo más seguro es borrar los anteriores y re-insertar para mantener el orden limpio
        $repo->comandos()->delete();

        $orden = 0;
        $fases = [
            'Instalación'   => $request->install_commands,
            'Compilación'   => $request->build_commands,
            'Configuración' => $request->setup_commands,
        ];

        foreach ($fases as $desc => $contenido) {
            if (!empty($contenido)) {
                $lineas = explode("\n", str_replace("\r", "", $contenido));
                foreach ($lineas as $linea) {
                    $cmdText = trim($linea);
                    if ($cmdText) {
                        $repo->comandos()->create([
                            'orden' => $orden++,
                            'comando' => $cmdText,
                            'descripcion' => "Fase de $desc",
                        ]);
                    }
                }
            }
        }

        return redirect()->route('repositorios-lista')->with('success', 'Repositorio y pipeline actualizados.');
    }
}
