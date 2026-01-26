@extends('layouts.app')

@section('title', 'Login')

@section('content')

    <div class="flex min-h-screen items-center justify-center p-6">
        <div class="w-full max-w-md">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-2xl shadow-indigo-500/20 mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight">Saeta<span class="text-indigo-500">Master</span></h1>
                <p class="text-slate-400 mt-2 font-medium">Control Central de Operaciones</p>
            </div>

            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-8 rounded-[2rem] shadow-2xl">
                
                @if ($errors->any())
                    <div class="mb-6 flex items-center gap-3 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-300 ml-1">Email Corporativo</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                            class="mt-2 w-full bg-slate-950/50 border border-slate-700 text-white rounded-2xl px-5 py-3.5 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-600"
                            placeholder="nombre@saeta.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-300 ml-1">Contraseña</label>
                        <input id="password" name="password" type="password" required 
                            class="mt-2 w-full bg-slate-950/50 border border-slate-700 text-white rounded-2xl px-5 py-3.5 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-600"
                            placeholder="••••••••">
                    </div>

                    <div class="flex items-center justify-between px-1">
                        <label class="flex items-center group cursor-pointer">
                            <input type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-slate-700 bg-slate-950 text-indigo-500 focus:ring-offset-slate-900 transition">
                            <span class="ml-3 text-sm text-slate-400 group-hover:text-slate-300 transition-colors">Recordar acceso</span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-600/20 transition-all active:scale-[0.97] flex items-center justify-center gap-2">
                        <span>Ingresar al Sistema</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </form>
            </div>

            <div class="mt-8 text-center">
                <p class="text-slate-500 text-sm font-medium">Saeta Master Enterprise &copy; {{ date('Y') }}</p>
            </div>
        </div>
    </div>
@endsection