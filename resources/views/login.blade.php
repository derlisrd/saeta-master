@extends('layouts.app')

@section('title', 'Login')

@section('content')
    {{-- Fondo con un ligero gradiente radial para dar profundidad --}}
    <div class="flex min-h-screen bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-zinc-800 via-zinc-900 to-black">
        
        <div class="flex-1 flex justify-center items-center p-4">
            {{-- Tarjeta con efecto de cristal y brillo sutil --}}
            <div class="w-full max-w-md space-y-8 border border-white/10 shadow-2xl rounded-2xl p-10 bg-zinc-900/80 backdrop-blur-xl">
                
                {{-- Encabezado con Icono --}}
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-sky-500/10 mb-4 border border-sky-500/20">
                        <svg class="w-8 h-8 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white uppercase">Bienvenido</h1>
                    <p class="text-zinc-500 text-sm mt-2">Introduce tus credenciales para acceder</p>
                </div>

                {{-- Manejo de Errores Global --}}
                @if ($errors->any())
                    <div class="animate-pulse flex items-center gap-3 p-3 rounded-lg bg-red-500/10 border border-red-500/50 text-red-400">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-xs font-medium">{{ $errors->first() }}</p>
                    </div>
                @endif

                <form action="{{ route('admin.login.submit') }}" method="post" class="mt-8 space-y-6">
                    @csrf
                    <div class="space-y-4">
                        {{-- Campo Email --}}
                        <div class="relative group">
                            <label class="text-zinc-500 text-xs font-semibold uppercase ml-1 mb-1 block group-focus-within:text-sky-400 transition-colors">Correo Electrónico</label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                class="w-full bg-zinc-800/50 border border-zinc-700 rounded-xl p-4 text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500 transition-all duration-300"
                                placeholder="nombre@dominio.com" />
                        </div>

                        {{-- Campo Password --}}
                        <div class="relative group">
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-zinc-500 text-xs font-semibold uppercase ml-1 group-focus-within:text-sky-400 transition-colors">Contraseña</label>
                                <a href="#" class="text-[10px] text-sky-500 hover:text-sky-400 transition-colors uppercase font-bold">¿Olvidaste tu clave?</a>
                            </div>
                            <input type="password" name="password" required
                                class="w-full bg-zinc-800/50 border border-zinc-700 rounded-xl p-4 text-white placeholder-zinc-600 focus:outline-none focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500 transition-all duration-300"
                                placeholder="••••••••••••" />
                        </div>
                    </div>

                    {{-- Botón con Efecto de Brillo --}}
                    <button type="submit" 
                        class="w-full relative group overflow-hidden bg-sky-600 hover:bg-sky-500 text-white rounded-xl py-4 font-bold uppercase text-sm tracking-widest transition-all duration-300 shadow-lg shadow-sky-900/20 active:scale-[0.98]">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            Ingresar al Sistema
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </span>
                        {{-- Efecto de brillo interno al pasar el mouse --}}
                        <div class="absolute inset-0 from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection