@include('components.g-header')

<main class="nxl-container apps-container apps-notes">
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content">
            <div class="content-area">
                <div class="content-area-body">
                    <div class="note-wrapper">

                        <div class="refund-hero position-relative overflow-hidden" style="padding: 4rem 0;">
                            <div class="refund-hero-circle" style="top: -60px; left: -80px; width: 220px; height: 220px;"></div>
                            <div class="refund-hero-circle" style="bottom: -80px; right: -80px; width: 280px; height: 280px;"></div>

                            <div class="container-fluid position-relative" style="z-index: 2;">
                                <div class="row justify-content-center">
                                    <div class="col-xxl-8 col-xl-10">
                                        <a href="{{ route('welcome') }}" class="btn btn-light btn-sm mb-4 shadow-sm">
                                            <i class="feather-arrow-left me-2"></i>Back to Home
                                        </a>
                                        <div class="text-center">
                                            <div class="mb-3">
                                                <i class="feather-info refund-hero-icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <h1 class="display-4 fw-bold refund-hero-title mb-3">Cookie Policy</h1>
                                            <p class="lead refund-hero-subtitle mb-1">How {{ config('app.name') }} uses cookies</p>
                                            <p class="refund-hero-meta small mb-0">Last Updated: {{ now()->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="note-body" style="margin-top: -3rem;">
                            <div class="container-fluid">
                                <div class="row justify-content-center">
                                    <div class="col-xxl-8 col-xl-10">

                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-body p-md-5 p-4">

                                                <div class="mb-5">
                                                    <div class="border-start border-success border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-check-circle text-success me-2"></i>Strictly Necessary</h5>
                                                    </div>
                                                    <div class="refund-colored-card refund-colored-success p-4 rounded">
                                                        <ul class="mb-0 text-muted">
                                                            <li class="mb-2"><strong>Session cookie</strong> (<code>proxworld_session</code>) — keeps you logged in as a customer/reseller.</li>
                                                            <li class="mb-2"><strong>Admin session cookie</strong> (<code>proxworld_admin_session</code>) — a separate cookie for staff, kept deliberately distinct from your customer session for security.</li>
                                                            <li class="mb-0"><strong>CSRF token cookie</strong> — protects forms against cross-site request forgery.</li>
                                                        </ul>
                                                        <p class="text-muted small mt-3 mb-0">Required for the site to function — can't be disabled while staying logged in.</p>
                                                    </div>
                                                </div>

                                                <div class="mb-5">
                                                    <div class="border-start border-primary border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-sliders text-primary me-2"></i>Functional</h5>
                                                    </div>
                                                    <p class="text-muted mb-0">Currency/locale preference — remembers your selected display currency (NGN, USD, GBP, etc.) between visits.</p>
                                                </div>

                                                <div class="mb-5">
                                                    <div class="border-start border-info border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-external-link text-info me-2"></i>Third-Party Cookies</h5>
                                                    </div>
                                                    <p class="text-muted mb-0">Our payment processor (Flutterwave) may set its own cookies during checkout, governed by its own privacy/cookie policy.</p>
                                                </div>

                                                <div class="mb-5">
                                                    <div class="border-start border-warning border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-settings text-warning me-2"></i>Managing Cookies</h5>
                                                    </div>
                                                    <p class="text-muted mb-0">Most browsers let you block or delete cookies through their settings. Blocking strictly necessary cookies will prevent you from logging in.</p>
                                                </div>

                                                <div class="pt-4 border-top">
                                                    <div class="refund-alert refund-alert-info mb-0">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="feather-info text-primary mt-1"></i>
                                                            <p class="mb-0 small">
                                                                If we enable analytics in the future, this policy will be updated to disclose it before it
                                                                goes live. <strong>Last updated: {{ now()->format('d M Y') }}</strong>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('legal.partials.hero-styles')
@include('components.g-footer')
