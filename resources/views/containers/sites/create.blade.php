@extends('layout.app')
@section('title','Sites')

@section('container_main')

<h3>Create new site</h3>


<form action="{{ route('site_store') }}">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
                <label for="name">Site name</label>
                <input class="form-control form-control-lg" id="name"placeholder="Enter name" name="name">
                <small class="form-text text-muted">  </small>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
                <label for="domain">Domain</label>
                <input class="form-control form-control-lg" id="domain" placeholder="Enter domain" name="domain">
                <small class="form-text text-muted"> example: sub.domain.com  </small>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
                <label for="name">Host</label>
                <input class="form-control form-control-lg" id="name"placeholder="Enter name" name="name">
                <small class="form-text text-muted">  </small>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
                <label for="domain">Database</label>
                <input class="form-control form-control-lg" id="domain" placeholder="Enter domain" name="domain">
                <small class="form-text text-muted"> example: sub.domain.com  </small>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
                <label for="name">User</label>
                <input class="form-control form-control-lg" id="name"placeholder="Enter name" name="name">
                <small class="form-text text-muted">  </small>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
                <label for="domain">Password</label>
                <input class="form-control form-control-lg" id="domain" placeholder="Enter domain" name="domain">
                <small class="form-text text-muted"> example: sub.domain.com  </small>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-4">
            <div class="form-group">
                <label for="name">Date expiration</label>
                <input type="date" class="form-control form-control-lg" id="name"placeholder="Date expiration" name="date_created">
                <small class="form-text text-muted">  </small>
            </div>
        </div>
    </div>
</form>

@endsection
