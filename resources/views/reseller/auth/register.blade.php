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
                    <h2 class="fs-20 fw-bolder mb-4">Register for {{ $reseller->panel_name }}</h2>
                    <h4 class="fs-13 fw-bold mb-2">Create your account</h4>

                    @if(session('alert'))
                        <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show mb-4" role="alert">
                            {{ session('alert')['message'] }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('storefront.register.attempt') }}" class="w-100 mt-4 pt-2">
                        @csrf
                        
                        <div class="mb-3">
                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                placeholder="Full Name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                placeholder="Email Address"
                                value="{{ old('email') }}"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <input
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
                        
                        <div class="mb-4">
                            <input
                                type="password"
                                class="form-control"
                                name="password_confirmation"
                                placeholder="Confirm Password"
                                required
                            >
                        </div>
                        
                        <div class="mb-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="termsConditions" name="terms" value="1" required>
                                <label class="custom-control-label c-pointer" for="termsConditions">
                                    I agree to the
                                    <a href="{{ route('terms-of-use') }}" class="text-primary">Terms &amp; Conditions</a>
                                </label>
                            </div>
                            @error('terms')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-lg btn-primary w-100" style="background-color: {{ $reseller->primary_color }}; border-color: {{ $reseller->primary_color }}">
                                Register
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-5 text-muted">
                        <span>Already have an account?</span>
                        <a href="{{ route('storefront.login') }}" class="fw-bold">
                            Login Here
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('reseller.components.g-footer')