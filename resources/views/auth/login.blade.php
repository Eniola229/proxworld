@include('components.g-header')

<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                
                <!-- Logo -->
                <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                    <img src="{{ asset('assets/images/LOGO.jpeg') }}" alt="Logo" class="img-fluid">
                </div>

                <div class="card-body p-sm-5">
                    <h2 class="fs-20 fw-bolder mb-4">Login</h2>
                    <h4 class="fs-13 fw-bold mb-2">Login to your account</h4>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif 

                    <!-- Login Error -->
                     @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <strong>Login failed.</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('login') }}" class="w-100 mt-4 pt-2">
                        @csrf
                        
                        <!-- Email Address -->
                        <div class="mb-4">
                            <input
                                id="email"
                                type="email"
                                class="form-control"
                                name="email"
                                placeholder="Email or Username"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                        
                        <!-- Password -->
                        <div class="mb-3">
                            <input
                                id="password"
                                type="password"
                                class="form-control"
                                name="password"
                                placeholder="Password"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                                <label class="custom-control-label c-pointer" for="remember_me">
                                    {{ __('Remember me') }}
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="fs-11 text-primary">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>
                        
                        <div class="mt-5">
                            <button type="submit" class="btn btn-lg btn-primary w-100">
                                {{ __('Log in') }}
                            </button>
                        </div>
                    </form>

                    <div class="d-flex align-items-center my-4">
                        <hr class="flex-grow-1">
                        <span class="mx-3 text-muted fs-11">OR</span>
                        <hr class="flex-grow-1">
                    </div>

                    <a href="{{ route('auth.google.redirect') }}" class="btn btn-lg btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                        <img src="{{ asset('assets/images/google-icon.svg') }}" alt="" width="18" height="18">
                        Continue with Google
                    </a>

                    <div class="mt-5 text-muted">
                        <span>Don’t have an account?</span>
                        <a href="{{ route('register') }}" class="fw-bold">
                            Create an Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('components.g-footer')
