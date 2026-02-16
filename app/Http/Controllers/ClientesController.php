<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientesController extends Controller
{
    public function activarCliente($id)
    {
        $negocio = Negocio::findOrFail($id);
        $negocio->activo = true;
        $negocio->save();

        return back()->with('success', 'Cliente activado exitosamente.');
    }
    public function negociosLista()
    {
        $negocios = Negocio::with('user')->get();
        $negocios = $negocios->map(function ($negocio) {
            $negocio->dominios_count = $negocio->user->dominios()->count();
            return $negocio;
        });
        return view('admin.clientes.negocios', compact('negocios'));
    }
    public function lista()
    {
        $clientes = User::withCount('dominios')->get();
        return view('admin.clientes.lista', compact('clientes'));
    }

    public function formulario(){
        return view ('admin.clientes.crear');
    }

    public function find($id)
    {
        // Cargamos el usuario con sus dominios y la zona relacionada a cada dominio
        $cliente = User::with(['dominios.zona'])->findOrFail($id);

        return view('admin.clientes.detalle', compact('cliente'));
    }

    public function store(Request $request){
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'username' => 'required|string|max:20|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password), // ¡Importante cifrarla!
        ]);

        return redirect()->route('clientes-lista')
            ->with('success', 'Cliente registrado exitosamente.');
    }
}
