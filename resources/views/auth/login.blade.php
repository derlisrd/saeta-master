@extends('layout.outlogin')

@section('container')


<div class="main-wrapper">
<div class="auth-wrapper d-flex no-block justify-content-center align-items-center">
<div class="auth-box p-4 bg-white rounded">
  <div id="loginform">
    <div class="logo">
      <h3 class="box-title mb-3">Sign In</h3>
    </div>
    <!-- Form -->
    <div class="row">
      <div class="col-12">
        <form
          class="form-horizontal mt-3 form-material"
          id="loginform"
          action="{{ route('auth_login') }}"
          method="post"
        >
            @csrf
          <div class="form-group mb-3">
            <div class="">
              <input name="username"
                class="form-control"
                type="text"
                autofocus
                required=""
                placeholder="Username"
              />
            </div>
          </div>
          <div class="form-group mb-4">
            <div class="">
              <input
                class="form-control"
                type="password"
                required=""
                name="password"
                placeholder="Password"
              />
            </div>
          </div>
          <div class="form-group">
            <div class="d-flex">
              <div class="checkbox checkbox-info pt-0">
                <input
                  id="checkbox-signup"
                  type="checkbox"
                  class="material-inputs chk-col-indigo"
                />
                <label for="checkbox-signup"> Remember me </label>
              </div>
              <div class="ms-auto">
                <a
                  href="javascript:void(0)"
                  id="to-recover"
                  class="
                    d-flex
                    align-items-center
                    link
                    font-weight-medium
                  "
                  ><i class="ri-lock-line me-1 fs-4"></i> Forgot pwd?</a
                >
              </div>
            </div>
          </div>
          <div class="form-group text-center mt-4 mb-3">
            <div class="col-xs-12">
              <button
                class="
                  btn btn-primary
                  d-block
                  w-100
                  waves-effect waves-light
                "
                type="submit"
              >
                Log In
              </button>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2 text-center">
              <div class="social mb-3">
                <a
                  href="javascript:void(0)"
                  class="btn btn-facebook"
                  data-bs-toggle="tooltip"
                  title="Login with Facebook"
                >
                  <i
                    aria-hidden="true"
                    class="
                      ri-facebook-line
                      fs-6
                      d-flex
                      align-items-center
                    "
                  ></i>
                </a>
                <a
                  href="javascript:void(0)"
                  class="btn btn-googleplus"
                  data-bs-toggle="tooltip"
                  title="Login with Google"
                >
                  <i
                    aria-hidden="true"
                    class="
                      ri-google-line
                      fs-6
                      d-flex
                      align-items-center
                    "
                  ></i>
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
@endsection
