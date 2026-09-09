{{-- resources/views/admin/auth/accept-invitation.blade.php --}}
@include('components.g-header')
<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                    <img src="{{ asset('assets/images/LOGO.png') }}" alt="" class="img-fluid">
                </div>
                <div class="card-body p-sm-5">
                    <h2 class="fs-20 fw-bolder mb-4">Set Your Password</h2>
                    <h4 class="fs-13 fw-bold mb-2">
                        Welcome, {{ $invitation->admin->name }} — finish setting up your admin account.
                    </h4>

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="feather-alert-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ url()->full() }}" class="w-100 mt-4 pt-2">
                        @csrf

                        <!-- Email (read-only, just for confirmation) -->
                        <div class="mb-4">
                            <input type="email"
                                   class="form-control"
                                   value="{{ $invitation->admin->email }}"
                                   disabled
                                   readonly>
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <input id="password"
                                   type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password"
                                   placeholder="New Password"
                                   required
                                   autofocus
                                   autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <input id="password_confirmation"
                                   type="password"
                                   class="form-control"
                                   name="password_confirmation"
                                   placeholder="Confirm Password"
                                   required
                                   autocomplete="new-password">
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn btn-lg btn-primary w-100">
                                <i class="feather-check-circle me-2"></i>
                                Set Password &amp; Continue
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@include('components.g-footer')