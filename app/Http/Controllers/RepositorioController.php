<?php

namespace App\Http\Controllers;

use App\Models\Repositorio;
use Illuminate\Http\Request;

class RepositorioController extends Controller
{
    public function lista()
    {
        $repositorios = Repositorio::all();
        return view('admin.repositorios.lista', compact('repositorios'));
    }

    public function formulario()
    {
        return view('admin.repositorios.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'url_git' => 'required|url',
            'branch' => 'required|string',
            'tipo' => 'required|in:laravel,nodejs,static,wordpress',
            //'tipo' => 'required|in:laravel,nodejs,static,wordpress',
        ]);

        Repositorio::create($request->all());

        return redirect()->route('repositorios-lista')->with('success', 'Repositorio registrado.');
    }

    public function destroy($id)
    {
        $repo = Repositorio::findOrFail($id);
        $repo->delete();
        return redirect()->back()->with('success', 'Repositorio eliminado.');
    }
}
