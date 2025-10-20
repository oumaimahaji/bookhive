@extends('layouts.user_type.guest')

@section('content')
<main class="main-content mt-0">
  <section>
    <div class="page-header min-vh-75">
      <div class="container">
        <div class="row">
          <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
            <div class="card card-plain mt-8">
              <div class="card-header pb-0 text-left bg-transparent">
                <h3 class="font-weight-bolder text-info text-gradient">Welcome back</h3>
                <p class="mb-0">Sign in with your credentials:</p>
              </div>
              <div class="card-body">
                <form method="POST" action="{{ route('login.store') }}">
                  @csrf
                  <div class="mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                      <p class="text-danger text-xs mt-2">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="mb-3">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    @error('password')
                      <p class="text-danger text-xs mt-2">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn bg-gradient-info w-100 mt-4 mb-0">Sign in</button>
                  </div>
                </form>
              </div>

              <div class="card-footer text-center pt-0 px-lg-2 px-1">
                <small class="text-muted">
                  Forgot your password? 
                  <a href="{{ route('password.request') }}" class="text-info text-gradient font-weight-bold">Reset here</a>
                </small>
                <p class="mb-4 text-sm mx-auto mt-3">
                  Don't have an account? 
                  <a href="{{ route('register') }}" class="text-info text-gradient font-weight-bold">Sign up</a>
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
              <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" 
                   style="background-image:url('../assets/img/curved-images/curved6.jpg')">
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</main>
@endsection