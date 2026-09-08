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
                    <h1 class="display-4 fw-bolder text-warning mb-3">404</h1>
                    <h2 class="fs-20 fw-bolder mb-3">Page Not Found</h2>
                    <p class="fs-13 text-muted mb-4">
                        Sorry, the page you’re looking for doesn’t exist  
                        or may have been moved.
                    </p>

                    <div class="d-flex gap-3 justify-content-center mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            Go Back
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            Go Home
                        </a>
                    </div>

                    <div class="mt-5 text-muted fs-11">
                        Check the URL or return to the homepage.
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('components.g-footer')
