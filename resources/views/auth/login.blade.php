@extends('layout.outlogin')

@section('container')
<section class="vh-100">
    <div class="container py-5 h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col col-xl-10">
          <div class="card" style="border-radius: 1rem;">
            <div class="row g-0">
              <div class="col-md-6 col-lg-5 d-none d-md-block bg-light">

              </div>
              <div class="col-md-6 col-lg-7 d-flex align-items-center">
                <div class="card-body p-4 p-lg-5 text-black">

                  <form method="post" action="{{ route('auth_login') }}">
                    @csrf
                    <div class="d-flex align-items-center mb-3 pb-1">
                      <i class="fas fa-cubes fa-2x me-3" style="color: #ff6219;"></i>
                      <span class="h1 fw-bold mb-0">Login</span>
                    </div>
                    <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Sign into your account</h5>




                    @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{$error}}</div>
                        @endforeach
                    </div>
                    @endif



                    <div class="form-outline mb-4">
                      <input autocomplete="off" required autofocus type="text" value="{{ old('username') }}" name="username" id="username" class="form-control form-control-lg" />
                      <label class="form-label" for="username">Username</label>
                      @error('username') {{ $message  }} @enderror
                    </div>

                    <div class="form-outline mb-4">
                      <input type="password" required id="form2Example27" name="password" class="form-control form-control-lg" />
                      <label class="form-label" for="form2Example27">Password</label>
                      @error('password') {{ $message  }} @enderror
                    </div>

                    <div class="pt-1 mb-4">
                      <button class="btn btn-primary text-white btn-lg btn-block" type="submit">Login</button>
                    </div>

                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
