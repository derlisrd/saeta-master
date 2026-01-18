<?php

use Livewire\Component;
use App\Models\Cliente;

new class extends Component
{
    public $clientes;

    public $cliente_id;

    public function crearDominio(){
        $this->validate([
        'nombre' => 'required|string|max:255',
        'subdominio' => 'required|string|max:50|unique:dominios,subdominio',
        'dominio' => 'required|string',
        'ip' => 'required|ip',
        'type' => 'required|in:A,CNAME,AAAA',
        'vencimiento' => 'required|date|after:today',
        'vps_host' => 'required|ip',
        'vps_user' => 'required|string',
        'vps_password' => 'required|string',
        'repo_core' => 'required|url',
        'repo_admin' => 'required|url',
    ]);
    }

    public function render(){
        $this->clientes = Cliente::all();
        return $this->view()->title('Dominios')->layout('layouts::dashboard');
    }
};
?>

<div>
    <flux:main container>
        <form wire:submit="crearDominio" class="space-y-8">
             <div class="flex-1 space-y-6">
                    <flux:select 
                        wire:model="cliente_id" 
                        label="Cliente (Opcional)"
                        description="Seleccione el cliente asociado a este dominio"
                        placeholder="Seleccionar cliente..."
                    >
                        @foreach($clientes as $cliente)
                            <flux:select.option value="{{ $cliente->id }}">{{ $cliente->nombre }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input
                        wire:model="nombre"
                        label="Nombre del Proyecto"
                        description="Nombre descriptivo del proyecto"
                        placeholder="Mi Proyecto Web"
                        required
                    />
                    @error('nombre') 
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
             </div>
                    
        </form>
    </flux:main>
</div>