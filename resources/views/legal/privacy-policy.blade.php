@include('components.g-header')

<main class="nxl-container apps-container apps-notes">
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content">
            <div class="content-area">
                <div class="content-area-body">
                    <div class="note-wrapper">

                        <!-- Hero Header -->
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
                                                <i class="feather-lock refund-hero-icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <h1 class="display-4 fw-bold refund-hero-title mb-3">Privacy Policy</h1>
                                            <p class="lead refund-hero-subtitle mb-1">
                                                How we collect, use, and protect your information
                                            </p>
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
                                                    <div class="d-flex align-items-start gap-3 mb-4">
                                                        <div class="flex-shrink-0">
                                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                                                <i class="feather-shield text-primary fs-4"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h4 class="fw-bold mb-2">Our Commitment</h4>
                                                            <p class="text-muted mb-0">
                                                                This Privacy Policy explains how {{ config('app.name') }} collects, uses, and protects
                                                                your information across our website, reseller panel, and API.
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-5">
                                                    <div class="border-start border-primary border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-database text-primary me-2"></i>Information We Collect</h5>
                                                    </div>
                                                    <ul class="text-muted">
                                                        <li class="mb-2"><strong>Account data:</strong> name, email, phone, country, password (hashed) or Google account identifier.</li>
                                                        <li class="mb-2"><strong>Financial data:</strong> wallet transaction history, top-up references, order history. We never store your card details — payments are processed by Flutterwave.</li>
                                                        <li class="mb-2"><strong>Usage data:</strong> IP address, device/browser info, and a security activity log of actions on your account.</li>
                                                        <li class="mb-2"><strong>Support data:</strong> contents of support tickets and live chat messages.</li>
                                                        <li class="mb-0"><strong>Reseller data:</strong> your panel name, subdomain, branding, and any support contacts you choose to display.</li>
                                                    </ul>
                                                </div>

                                                <div class="mb-5">
                                                    <div class="border-start border-success border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-share-2 text-success me-2"></i>Third-Party Processors</h5>
                                                    </div>
                                                    <p class="text-muted mb-3">We share the minimum necessary data with:</p>
                                                    <div class="row g-3">
                                                        @foreach([
                                                            ['icon'=>'feather-credit-card','name'=>'Flutterwave','desc'=>'Payment processing'],
                                                            ['icon'=>'feather-mail','name'=>'Brevo','desc'=>'Transactional & marketing email'],
                                                            ['icon'=>'feather-image','name'=>'Cloudinary','desc'=>'Media hosting for blog/newsletter'],
                                                            ['icon'=>'feather-server','name'=>'Proxy Providers','desc'=>'Only technical info needed to provision your order'],
                                                        ] as $p)
                                                        <div class="col-md-6">
                                                            <div class="refund-colored-card refund-colored-success h-100 p-3 rounded">
                                                                <div class="d-flex align-items-start gap-2 mb-2">
                                                                    <i class="{{ $p['icon'] }} text-success"></i>
                                                                    <h6 class="fw-semibold mb-0">{{ $p['name'] }}</h6>
                                                                </div>
                                                                <p class="text-muted small mb-0">{{ $p['desc'] }}</p>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="mb-5">
                                                    <div class="border-start border-info border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-activity text-info me-2"></i>Activity Logging</h5>
                                                    </div>
                                                    <p class="text-muted mb-0">
                                                        For security, meaningful actions on your account are logged with a timestamp, IP address, and
                                                        description. This protects you against unauthorized changes and is accessible only to authorized
                                                        {{ config('app.name') }} staff for security investigations.
                                                    </p>
                                                </div>

                                                <div class="mb-5">
                                                    <div class="border-start border-warning border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-user-check text-warning me-2"></i>Your Rights</h5>
                                                    </div>
                                                    <p class="text-muted mb-0">
                                                        You may request access to, correction of, or deletion of your personal data by contacting support.
                                                        Deletion requests may be limited where we have a legal obligation to retain transaction records.
                                                    </p>
                                                </div>

                                                <div class="pt-4 border-top">
                                                    <div class="refund-alert refund-alert-info mb-0">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="feather-info text-primary mt-1"></i>
                                                            <p class="mb-0 small">
                                                                We may update this policy from time to time; material changes will be communicated via
                                                                email or in-app notice. <strong>Last updated: {{ now()->format('d M Y') }}</strong>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="refund-cta-card card border-0 shadow-lg overflow-hidden mb-4">
                                            <div class="refund-hero-circle" style="top: -40px; right: -60px; width: 200px; height: 200px; z-index: 1;"></div>
                                            <div class="refund-hero-circle" style="bottom: -60px; left: -40px; width: 160px; height: 160px; z-index: 1;"></div>
                                            <div class="card-body text-center p-5 position-relative" style="z-index: 2;">
                                                <i class="feather-headphones fs-1 mb-3 d-block refund-cta-icon"></i>
                                                <h4 class="fw-bold mb-3 refund-cta-title">Questions about your data?</h4>
                                                <p class="mb-4 refund-cta-text mx-auto" style="max-width: 500px;">Our support team can help.</p>
                                                <div class="d-flex justify-content-center gap-3 flex-wrap">
                                                    @auth
                                                        <a href="{{ route('support.index') }}" class="btn btn-light btn-lg shadow"><i class="feather-message-circle me-2"></i>Contact Support</a>
                                                    @else
                                                        <a href="{{ route('login') }}" class="btn btn-light btn-lg shadow"><i class="feather-log-in me-2"></i>Login to Contact Support</a>
                                                    @endauth
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
