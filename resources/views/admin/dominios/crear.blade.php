@extends('layouts.admin')

@section('page-title', 'Crear un dominio')
@section('content')

    <div>
        <form>
            @csrf

            <flux:field name='subdominio'>
                <flux:label>Subdominio</flux:label>
                <flux:input placeholder='hola' name='subdominio' />
                <flux:error name="subdominio" />
            </flux:field>

            <flux:select placeholder="Selecciona zona" name>
                @foreach ($zonas as $zona)
                    <flux:select.option>{{ $zona['name'] }}</flux:select.option>
                @endforeach
            </flux:select>

        </form>
    </div>
@endsection
