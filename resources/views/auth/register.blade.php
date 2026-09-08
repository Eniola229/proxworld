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
                    <h2 class="fs-20 fw-bolder mb-4">Register</h2>
                    <h4 class="fs-13 fw-bold mb-2">Create your account</h4>
                    <p class="fs-12 fw-medium text-muted">
                        Thank you for joining <strong>{{ config('app.name', 'ProxWorld') }}</strong>, let’s create your account to get started.
                    </p>
                    
                    <form method="POST" action="{{ route('register') }}" class="w-100 mt-4 pt-2">
                        @csrf
                        
                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label">Name</label>
                            <input
                                id="name"
                                type="text"
                                class="form-control"
                                name="name"
                                placeholder="Full Name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                            >

                            @error('name')
                                <div class="alert alert-danger mt-2 mb-0 py-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Email Address -->
                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <input
                                id="email"
                                type="email"
                                class="form-control"
                                name="email"
                                placeholder="Email Address"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                            >

                            @error('email')
                                <div class="alert alert-danger mt-2 mb-0 py-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Referral Code (Optional) -->
                        <div class="mb-4">
                            <label for="referral_code" class="form-label">Referral Code (Optional)</label>
                            <input id="referral_code" 
                                   type="text" 
                                   class="form-control @error('referral_code') is-invalid @enderror" 
                                   name="referral_code" 
                                   placeholder="Enter referral code if you have one"
                                   value="{{ old('referral_code', request('ref')) }}">
                            @error('referral_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Have a referral code? Enter it here to connect with your referrer!</small>
                        </div>
                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input
                                id="password"
                                type="password"
                                class="form-control"
                                name="password"
                                placeholder="Password"
                                required
                                autocomplete="new-password"
                            >

                            @error('password')
                                <div class="alert alert-danger mt-2 mb-0 py-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                class="form-control"
                                name="password_confirmation"
                                placeholder="Confirm Password"
                                required
                                autocomplete="new-password"
                            >

                            @error('password_confirmation')
                                <div class="alert alert-danger mt-2 mb-0 py-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <!-- Terms -->
                        <div class="mb-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="termsConditions" name="terms" value="1" required>
                                <label class="custom-control-label c-pointer" for="termsConditions">
                                    I agree to the
                                    <a href="javascript:void(0);" class="text-primary">Terms & Conditions</a>
                                </label>
                            </div>
                            <div id="termsError" class="text-danger fs-11 mt-1" style="display:none;">
                                Please accept the Terms & Conditions to continue.
                            </div>
                        </div>
                        
                        <div class="mt-5">
                            <button type="submit"  class="btn btn-lg btn-primary w-100" >
                                {{ __('Register') }}
                            </button>
                        </div>
                    </form>

                    <div class="d-flex align-items-center my-4">
                        <hr class="flex-grow-1">
                        <span class="mx-3 text-muted fs-11">OR</span>
                        <hr class="flex-grow-1">
                    </div>

                    <a href="{{ route('auth.google.redirect') }}" id="googleSignupBtn" class="btn btn-lg btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                        <img src="{{ asset('assets/images/google-icon.svg') }}" alt="" width="18" height="18">
                        Sign up with Google
                    </a>

                    <div class="mt-5 text-muted">
                        <span>{{ __('Already registered?') }}</span>
                        <a href="{{ route('login') }}" class="fw-bold">
                            {{ __('Log in') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.getElementById('googleSignupBtn').addEventListener('click', function (e) {
        var terms = document.getElementById('termsConditions');
        var termsError = document.getElementById('termsError');

        if (!terms.checked) {
            e.preventDefault();
            termsError.style.display = 'block';
            terms.focus();
            terms.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            termsError.style.display = 'none';
        }
    });

    document.getElementById('termsConditions').addEventListener('change', function () {
        if (this.checked) {
            document.getElementById('termsError').style.display = 'none';
        }
    });
</script>

@include('components.g-footer')