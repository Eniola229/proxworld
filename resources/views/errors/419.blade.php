@include('components.g-header')

<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative text-center">

                <!-- Logo -->
                <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                    <img src="{{ asset('assets/images/B.png') }}" alt="Logo" class="img-fluid">
                </div>

                <div class="card-body p-sm-5 pt-5">
                    <h2 class="fs-20 fw-bolder mb-3">Session Expired</h2>
                    <p class="fs-13 text-muted mb-4">
                        Your session has expired for security reasons.  
                        Please log in again to continue.
                    </p>

                    <div class="d-flex justify-content-center mt-4">
                        <a href="{{ route('login') }}" class="btn btn-lg btn-primary">
                            Log in again
                        </a>
                    </div>

                    <div class="mt-5 text-muted fs-11">
                        This usually happens after being inactive for a while.
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('components.g-footer')
