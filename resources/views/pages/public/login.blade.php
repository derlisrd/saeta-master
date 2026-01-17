<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Title;

new class extends Component
{
    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        // Esto ejecutará las reglas definidas en los atributos #[Validate]
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return $this->redirect('/auth/dash', navigate: true);
        }

        // Agregamos el error directamente al validador para que se muestre en el campo email o general
        $this->addError('credenciales', 'Las credenciales no coinciden con nuestros registros.');
    }
};
?>

<div class="flex min-h-screen">
    <div class="flex-1 flex justify-center items-center">
        <div class="w-80 max-w-80 space-y-6">
            <div class="flex justify-center opacity-50">
                <a href="/" class="group flex items-center gap-3">
                    <div>
                        <svg class="h-4 text-zinc-800 dark:text-white" viewBox="0 0 18 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g>
                                <line x1="1" y1="5" x2="1" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                <line x1="5" y1="1" x2="5" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                <line x1="9" y1="5" x2="9" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                <line x1="13" y1="1" x2="13" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                                <line x1="17" y1="5" x2="17" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round"></line>
                            </g>
                        </svg>
                    </div>

                    <span class="text-xl font-semibold text-zinc-800 dark:text-white">Nderasore</span>
                </a>
            </div>

            <flux:heading class="text-center" size="xl">Welcome back</flux:heading>


            <div>
                <form wire:submit="login" class="flex flex-col gap-6">
                    @if (session()->has('error'))
                        <flux:error>{{ $message }}</flux:error>
                    @endif
                <flux:input wire:model="email"  label="Email" type="email" placeholder="email@example.com" />
                @error('email') 
                    <flux:error>{{ $message }}</flux:error>
                @enderror
                <flux:field>
                    <div class="mb-3 flex justify-between">
                        <flux:label>Password</flux:label>

                        <flux:link href="#" variant="subtle" class="text-sm">Forgot password?</flux:link>
                    </div>

                    <flux:input wire:model="password"  type="password" placeholder="Your password" />
                     @error('password') 
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:checkbox label="Remember me for 30 days" />

                <flux:button type='submit' variant="primary" class="w-full">Log in</flux:button>
                </form>
            </div>

        </div>
    </div>
</div>