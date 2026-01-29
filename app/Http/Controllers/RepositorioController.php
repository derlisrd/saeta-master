<?php

namespace App\Http\Controllers;

use App\Models\Repositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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

        return view('admin.repositorios.crear', [
            'repositorios' => $repos, // Ahora vienen de GitHub
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'clone_url' => 'required|url', // Validamos la URL oculta que sí es una URL
            'branch' => 'required|string',
            'tipo' => 'required|in:laravel,nodejs,static,wordpress',
        ]);

        // Creamos un array con los datos limpios
        Repositorio::create([
            'nombre'   => $request->nombre,
            'url_git'  => $request->clone_url, // Guardamos la URL de clonación real
            'branch'   => $request->branch,
            'tipo'     => $request->tipo,
            'install_commands' => $request->install_commands,
            'build_commands' => $request->build_commands ,
            'setup_commands' => $request->setup_commands,
            'output_path' => $request->output_path,
        ]);

        return redirect()->route('repositorios-lista')->with('success', 'Repositorio registrado.');
    }
    
    public function destroy($id)
    {
        $repo = Repositorio::findOrFail($id);
        $repo->delete();
        return redirect()->back()->with('success', 'Repositorio eliminado.');
    }
}
