@include('components.g-header')

<main class="auth-minimal-wrapper">
    <div class="auth-minimal-inner">
        <div class="minimal-card-wrapper">
            <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative text-center">

                <!-- Logo -->
                <div class="wd-50 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                    <img src="{{ asset('assets/images/LOGO.png') }}" alt="Logo" class="img-fluid">
                </div>

                <div class="card-body p-sm-5 pt-5">
                    <h1 class="display-4 fw-bolder text-warning mb-3">
                        <i class="feather-clock"></i>
                    </h1>
                    <h2 class="fs-20 fw-bolder mb-3">Application Pending Approval</h2>
                    <p class="fs-13 text-muted mb-4">
                        @if($reseller)
                            Thanks for applying, <strong>{{ $reseller->panel_name }}</strong> is still under review.
                            We'll notify you by email as soon as your reseller panel is approved.
                        @else
                            Your reseller application is still under review.
                            We'll notify you by email as soon as it's approved.
                        @endif
                    </p>

                    <div class="d-flex gap-3 justify-content-center mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            Back to Dashboard
                        </a>
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('pending-logout-form').submit();"
                           class="btn btn-primary">
                            Log Out
                        </a>
                    </div>
                    <form id="pending-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

@include('components.g-footer')