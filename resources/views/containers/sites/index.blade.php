@extends('layout.app')
@section('title','Sites')

@section('container_main')

    <h3>Sites</h3>
    <div class="row">
        <div class="col-12">
            <a href="{{ route('v_sites_create') }}" class="btn btn-primary text-white">Create new</a>
        </div>
    </div>
@endsection
