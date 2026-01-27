@extends('layouts.dashboard-layout')

@section('page-title', 'Dashboard Principal')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                <i data-lucide="globe" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+12%</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Dominios Activos</h3>
        <p class="text-2xl font-bold text-slate-800">128</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Clientes Totales</h3>
        <p class="text-2xl font-bold text-slate-800">842</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                <i data-lucide="credit-card" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-medium text-slate-400 italic">Plan Enterprise</span>
        </div>
        <h3 class="text-slate-500 text-sm font-medium">Estado de Cuenta</h3>
        <p class="text-2xl font-bold  text-emerald-600">Al Día</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
        <h3 class="font-bold text-slate-800">Últimas Instancias Creadas</h3>
        <button class="text-sm text-indigo-600 font-semibold hover:text-indigo-700">Ver todos</button>
    </div>
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Cliente</th>
                <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Dominio</th>
                <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase">Estado</th>
                <th class="px-6 py-3 text-xs font-bold text-slate-500 uppercase text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <tr>
                <td class="px-6 py-4 text-sm font-medium text-slate-800">Distribuidora Norte</td>
                <td class="px-6 py-4 text-sm text-slate-500 italic">norte.saetamaster.com</td>
                <td class="px-6 py-4"><span class="px-2 py-1 text-xs font-bold bg-green-100 text-green-700 rounded-md">Online</span></td>
                <td class="px-6 py-4 text-right"><button class="text-slate-400 hover:text-indigo-600"><i data-lucide="more-horizontal" class="w-5 h-5"></i></button></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection