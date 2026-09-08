@include('reseller.components.g-header')

<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                
                <!-- Logo -->
              <!--   <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                    <img src="{{ asset($reseller->logo_path ?? 'assets/images/B.png') }}" alt="Logo" class="img-fluid">
                </div> -->

                <div class="card-body p-sm-5">
                    <h2 class="fs-20 fw-bolder mb-4">Login to {{ $reseller->panel_name }}</h2>
                    <h4 class="fs-13 fw-bold mb-2">Welcome back</h4>

                    @if(session('alert'))
                        <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show mb-4" role="alert">
                            {{ session('alert')['message'] }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('storefront.login.attempt') }}" class="w-100 mt-4 pt-2">
                        @csrf
                        
                        <div class="mb-4">
                            <input
                                id="email"
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                placeholder="Email Address"
                                value="{{ old('email') }}"
                                required
                                autofocus
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <input
                                id="password"
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                placeholder="Password"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                <label class="custom-control-label c-pointer" for="remember">
                                    Remember me
                                </label>
                            </div>
                        </div>
                        
                        <div class="mt-5">
                            <button type="submit" class="btn btn-lg btn-primary w-100" style="background-color: {{ $reseller->primary_color }}; border-color: {{ $reseller->primary_color }}">
                                Log in
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-5 text-muted">
                        <span>Don't have an account?</span>
                        <a href="{{ route('storefront.register') }}" class="fw-bold">
                            Create an Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('reseller.components.g-footer')