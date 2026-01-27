<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientesController extends Controller
{
    public function lista()
    {
        $clientes = User::withCount('dominios')->get();
        return view('admin.clientes.lista', compact('clientes'));
    }

    public function formulario(){
        return view ('admin.clientes.crear');
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
