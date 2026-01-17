<?php

use Livewire\Component;

new class extends Component
{
    
};
?>



<div>
    <flux:sidebar sticky collapsible="mobile" class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.header>
            <flux:sidebar.brand
                href="/auth/dash"
                logo="https://fluxui.dev/img/demo/logo.png"
                logo:dark="https://fluxui.dev/img/demo/dark-mode-logo.png"
                name="Nderasore"
            />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.search placeholder="Buscar..." />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="home" href="/auth/dash" wire:navigate>Dashboard</flux:sidebar.item>
            <flux:sidebar.item icon="globe-alt" href="/auth/dominios" wire:navigate current>Dominios</flux:sidebar.item>
            <flux:sidebar.item icon="users" href="/auth/clientes" wire:navigate>Clientes</flux:sidebar.item>
            <flux:sidebar.item icon="server" href="/auth/servidores" wire:navigate>Servidores</flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:sidebar.spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="cog-6-tooth" href="/auth/configuracion" wire:navigate>Configuración</flux:sidebar.item>
        </flux:sidebar.nav>

        <flux:dropdown position="top" align="start" class="max-lg:hidden">
            <flux:sidebar.profile avatar="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" name="{{ auth()->user()->name }}" />

            <flux:menu>
                <flux:menu.item icon="user">Perfil</flux:menu.item>
                <flux:menu.separator />
                <flux:menu.item icon="arrow-right-start-on-rectangle" href="/logout" wire:navigate>Cerrar sesión</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />
        <flux:profile avatar="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" />
    </flux:header>

    <flux:main container class="max-w-5xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <flux:heading size="xl">Crear Nuevo Dominio</flux:heading>
                <flux:subheading>Configure un nuevo subdominio con despliegue automático</flux:subheading>
            </div>
            <flux:button href="/auth/dominios" wire:navigate variant="ghost" icon="arrow-left">
                Volver
            </flux:button>
        </div>



        <flux:separator variant="subtle" class="my-8" />

        <form wire:submit="crearDominio" class="space-y-8">
            <!-- Información General -->
            <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
                <div class="w-80">
                    <flux:heading size="lg">Información General</flux:heading>
                    <flux:subheading>Datos básicos del dominio y cliente asociado</flux:subheading>
                </div>

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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:input
                                wire:model="subdominio"
                                label="Subdominio"
                                description="Sin espacios ni caracteres especiales"
                                placeholder="pole"
                                required
                            />
                            @error('subdominio') 
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        <div>
                            <flux:input
                                wire:model="dominio"
                                label="Dominio Principal"
                                placeholder="saeta.app"
                                required
                            />
                            @error('dominio') 
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>
                    </div>

                    @if($subdominio && $dominio)
                            <strong>Dominio completo:</strong> {{ $subdominio }}.{{ $dominio }}
                        
                    @endif
                </div>
            </div>

            <flux:separator variant="subtle" class="my-8" />

            <!-- Configuración DNS -->
            <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
                <div class="w-80">
                    <flux:heading size="lg">Configuración DNS</flux:heading>
                    <flux:subheading>Configuración del registro DNS en Cloudflare</flux:subheading>
                </div>

                <div class="flex-1 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:input
                                wire:model="ip"
                                label="Dirección IP"
                                description="IP donde apuntará el dominio"
                                placeholder="192.168.1.100"
                                required
                            />
                            @error('ip') 
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        <div>
                            <flux:select 
                                wire:model="type" 
                                label="Tipo de Registro"
                                required
                            >
                                <flux:select.option value="A">A (IPv4)</flux:select.option>
                                <flux:select.option value="AAAA">AAAA (IPv6)</flux:select.option>
                                <flux:select.option value="CNAME">CNAME</flux:select.option>
                            </flux:select>
                        </div>
                    </div>

                    <flux:input
                        wire:model="vencimiento"
                        label="Fecha de Vencimiento"
                        type="date"
                        required
                    />
                    @error('vencimiento') 
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    <div class="flex gap-6">
                        <flux:switch wire:model="principal" label="Dominio Principal" />
                        <flux:switch wire:model="premium" label="Plan Premium" />
                    </div>
                </div>
            </div>

            <flux:separator variant="subtle" class="my-8" />

            <!-- Configuración VPS -->
            <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
                <div class="w-80">
                    <flux:heading size="lg">Servidor VPS</flux:heading>
                    <flux:subheading>Credenciales SSH del servidor donde se desplegará</flux:subheading>
                </div>

                <div class="flex-1 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <flux:input
                                wire:model="vps_host"
                                label="IP del Servidor"
                                placeholder="192.168.1.100"
                                required
                            />
                            @error('vps_host') 
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>

                        <div>
                            <flux:input
                                wire:model="vps_user"
                                label="Usuario SSH"
                                placeholder="root"
                                required
                            />
                            @error('vps_user') 
                                <flux:error>{{ $message }}</flux:error>
                            @enderror
                        </div>
                    </div>

                    <flux:input
                        wire:model="vps_password"
                        label="Contraseña SSH"
                        type="password"
                        placeholder="••••••••"
                        required
                    />
                    @error('vps_password') 
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    <flux:text variant="warning">
                        Las credenciales SSH no se guardan, solo se usan para el despliegue inicial
                    </flux:text>
                </div>
            </div>

            <flux:separator variant="subtle" class="my-8" />

            <!-- Repositorios -->
            <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 pb-10">
                <div class="w-80">
                    <flux:heading size="lg">Repositorios</flux:heading>
                    <flux:subheading>URLs de los repositorios Git a clonar</flux:subheading>
                </div>

                <div class="flex-1 space-y-6">
                    <flux:input
                        wire:model="repo_core"
                        label="Repositorio Backend (Laravel)"
                        description="URL completa del repositorio Git"
                        placeholder="https://github.com/usuario/proyecto-laravel.git"
                        required
                    />
                    @error('repo_core') 
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    <flux:input
                        wire:model="repo_admin"
                        label="Repositorio Frontend (React)"
                        description="URL completa del repositorio Git"
                        placeholder="https://github.com/usuario/proyecto-react.git"
                        required
                    />
                    @error('repo_admin') 
                        <flux:error>{{ $message }}</flux:error>
                    @enderror

                    <flux:text color="red" variant="info">
                        <strong>Nota:</strong> El proceso de despliegue puede tardar varios minutos
                    </flux:text color="red">

                    <div class="flex justify-end gap-3">
                        <flux:button 
                            href="/auth/dominios" 
                            wire:navigate 
                            variant="ghost"
                        >
                            Cancelar
                        </flux:button>
                        
                        <flux:button 
                            type="submit" 
                            variant="primary"
                            :disabled="$loading"
                        >
                            @if($loading)
                                <flux:icon.loading class="animate-spin" />
                                Creando dominio...
                            @else
                                <flux:icon.check />
                                Crear Dominio
                            @endif
                        </flux:button>
                    </div>
                </div>
            </div>
        </form>
    </flux:main>
</div>
