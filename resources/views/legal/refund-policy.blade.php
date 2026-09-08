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
                                                <i class="feather-rotate-ccw refund-hero-icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <h1 class="display-4 fw-bold refund-hero-title mb-3">Refund Policy</h1>
                                            <p class="lead refund-hero-subtitle mb-1">
                                                Understanding when and how refunds are processed
                                            </p>
                                            <p class="refund-hero-meta small mb-0">Last Updated: 20 Jan 2026</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="note-body" style="margin-top: -3rem;">
                            <div class="container-fluid">
                                <div class="row justify-content-center">
                                    <div class="col-xxl-8 col-xl-10">

                                        <!-- Quick Summary Card -->
                                        <div class="card border-0 shadow-lg mb-4">
                                            <div class="card-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <div class="text-center">
                                                            <div class="refund-icon-circle refund-icon-success d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;">
                                                                <i class="feather-zap text-success fs-3"></i>
                                                            </div>
                                                            <h6 class="fw-bold mb-1">Instant Refunds</h6>
                                                            <p class="text-muted small mb-0">Automatic refunds processed immediately</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="text-center">
                                                            <div class="refund-icon-circle refund-icon-primary d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;">
                                                                <i class="feather-clock text-primary fs-3"></i>
                                                            </div>
                                                            <h6 class="fw-bold mb-1">24-48 Hours</h6>
                                                            <p class="text-muted small mb-0">Manual refund processing time</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="text-center">
                                                            <div class="refund-icon-circle refund-icon-info d-inline-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;">
                                                                <i class="feather-headphones text-info fs-3"></i>
                                                            </div>
                                                            <h6 class="fw-bold mb-1">24/7 Support</h6>
                                                            <p class="text-muted small mb-0">Always here to help</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Main Content Card -->
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-body p-md-5 p-4">

                                                <!-- Introduction -->
                                                <div class="mb-5">
                                                    <div class="d-flex align-items-start gap-3 mb-4">
                                                        <div class="flex-shrink-0">
                                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                                                <i class="feather-shield text-primary fs-4"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h4 class="fw-bold mb-2">Our Commitment to You</h4>
                                                            <p class="text-muted mb-0">
                                                                At ProxWorld, we strive to provide fast, reliable proxy services. 
                                                                This refund policy outlines the circumstances under which refunds may be issued. 
                                                                Please read this policy carefully before placing an order.
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="refund-alert refund-alert-info">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="feather-clock text-info mt-1"></i>
                                                            <div>
                                                                <strong>24/7 Support Available:</strong>
                                                                <p class="mb-0 mt-1">Have questions about refunds? Our support team is available around the clock to assist you.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Automatic Refunds -->
                                                <div class="mb-5">
                                                    <div class="border-start border-success border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">
                                                            <i class="feather-check-circle text-success me-2"></i>
                                                            Automatic Refunds
                                                        </h5>
                                                    </div>
                                                    <p class="text-muted mb-4">We automatically process refunds in the following situations:</p>
                                                    <div class="row g-3">
                                                        @php
                                                            $autoRefunds = [
                                                                ['icon'=>'feather-x-circle',      'title'=>'Order Cancellation', 'text'=>'If our service provider cancels your order for any reason, the full amount will be automatically refunded to your wallet within minutes.'],
                                                                ['icon'=>'feather-alert-triangle', 'title'=>'Service Failure',   'text'=>'If our system fails to process your order and deducts funds from your wallet, an automatic refund will be initiated immediately.'],
                                                                ['icon'=>'feather-cpu',            'title'=>'API Errors',        'text'=>'In cases where the order cannot be placed due to technical errors, funds are refunded automatically to your wallet.'],
                                                            ];
                                                        @endphp
                                                        @foreach($autoRefunds as $r)
                                                        <div class="col-md-4">
                                                            <div class="refund-colored-card refund-colored-success h-100 p-3 rounded">
                                                                <div class="d-flex align-items-start gap-2 mb-3">
                                                                    <i class="{{ $r['icon'] }} text-success"></i>
                                                                    <h6 class="fw-semibold mb-0">{{ $r['title'] }}</h6>
                                                                </div>
                                                                <p class="text-muted small mb-0">{{ $r['text'] }}</p>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Manual Refund Requests -->
                                                <div class="mb-5">
                                                    <div class="border-start border-primary border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">
                                                            <i class="feather-message-circle text-primary me-2"></i>
                                                            Manual Refund Requests
                                                        </h5>
                                                    </div>
                                                    <p class="text-muted mb-4">You may request a refund by contacting our 24/7 support team in the following cases:</p>
                                                    <div class="accordion" id="manualRefundAccordion">

                                                        <div class="refund-accordion-item border rounded mb-3">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#incomplete">
                                                                    <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                    Incomplete Order Delivery
                                                                </button>
                                                            </h2>
                                                            <div id="incomplete" class="accordion-collapse collapse show" data-bs-parent="#manualRefundAccordion">
                                                                <div class="accordion-body pt-0">
                                                                    <p class="text-muted mb-0">
                                                                        If your order shows as "Partial" or does not deliver the promised quantity, 
                                                                        we will investigate and issue a partial or full refund based on what was delivered.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="refund-accordion-item border rounded mb-3">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#quality">
                                                                    <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                    Quality Issues
                                                                </button>
                                                            </h2>
                                                            <div id="quality" class="accordion-collapse collapse" data-bs-parent="#manualRefundAccordion">
                                                                <div class="accordion-body pt-0">
                                                                    <p class="text-muted mb-0">
                                                                        If the delivered proxies or other services purchased are of significantly lower quality 
                                                                        than promised (e.g., blacklisted, dead, or non-functional IPs), please contact support with evidence.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="refund-accordion-item border rounded mb-3">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#stuck">
                                                                    <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                    Order Stuck in Processing
                                                                </button>
                                                            </h2>
                                                            <div id="stuck" class="accordion-collapse collapse" data-bs-parent="#manualRefundAccordion">
                                                                <div class="accordion-body pt-0">
                                                                    <p class="text-muted mb-0">
                                                                        If your order has been "Processing" for more than 48 hours without progress, 
                                                                        contact our support team for investigation and possible refund.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <!-- Non-Refundable Situations -->
                                                <div class="mb-5">
                                                    <div class="border-start border-danger border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">
                                                            <i class="feather-x-circle text-danger me-2"></i>
                                                            Non-Refundable Situations
                                                        </h5>
                                                    </div>
                                                    <p class="text-muted mb-4">Refunds will NOT be issued in the following cases:</p>
                                                    <div class="refund-colored-card refund-colored-danger p-4 rounded">
                                                        <div class="row g-3">
                                                            @php
                                                                $noRefunds = [
                                                                    ['title'=>'Completed Orders',          'text'=>'Orders marked as "Completed" are not eligible for refunds.'],
                                                                    ['title'=>'Change of Mind',             'text'=>'Once an order is placed and being processed, refunds cannot be issued due to change of mind.'],
                                                                    ['title'=>'Incorrect Order Configuration',   'text'=>'If you select the wrong plan, protocol, or configuration and the order is fulfilled as requested, refunds will not be issued.'],
                                                                    ['title'=>'Account Issues',            'text'=>'If a target website you use the proxy against blocks or restricts access for reasons outside our control (e.g. that site's own anti-bot rules).'],
                                                                    ['title'=>'Natural Drop-offs',         'text'=>'Some target websites blacklist individual IPs over time. Occasional IP rotation/blacklisting is normal for proxy usage and is not refundable on its own — see our Replacement Policy below.'],
                                                                    ['title'=>'Excessive Refund Requests', 'text'=>'Accounts with a pattern of excessive refund requests may be subject to review and potential suspension.'],
                                                                ];
                                                            @endphp
                                                            @foreach($noRefunds as $n)
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <i class="feather-x text-danger small mt-1 flex-shrink-0"></i>
                                                                    <div>
                                                                        <strong class="d-block mb-1">{{ $n['title'] }}</strong>
                                                                        <p class="text-muted small mb-0">{{ $n['text'] }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Refund Process & Timeline -->
                                                <div class="mb-5">
                                                    <div class="border-start border-info border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">
                                                            <i class="feather-refresh-cw text-info me-2"></i>
                                                            Refund Process & Timeline
                                                        </h5>
                                                    </div>
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-md-6">
                                                            <div class="refund-colored-card refund-colored-success h-100 p-4 rounded text-center">
                                                                <i class="feather-zap text-success fs-1 mb-3 d-block"></i>
                                                                <h6 class="fw-bold mb-2">Automatic Refunds</h6>
                                                                <p class="text-muted mb-3">Processed instantly to your wallet</p>
                                                                <span class="badge bg-success px-3 py-2">Immediate</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="refund-colored-card refund-colored-warning h-100 p-4 rounded text-center">
                                                                <i class="feather-clock text-warning fs-1 mb-3 d-block"></i>
                                                                <h6 class="fw-bold mb-2">Manual Refunds</h6>
                                                                <p class="text-muted mb-3">After support team investigation</p>
                                                                <span class="badge bg-warning px-3 py-2">24-48 Hours</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="refund-alert refund-alert-info mb-0">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="feather-info text-info mt-1"></i>
                                                            <div>
                                                                <strong>Important:</strong>
                                                                <p class="mb-0 mt-1">All refunds are credited to your ProxWorld wallet and can be used for future orders. Wallet funds cannot be withdrawn to bank accounts.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- How to Request a Refund -->
                                                <div class="mb-5">
                                                    <div class="border-start border-primary border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">
                                                            <i class="feather-life-buoy text-primary me-2"></i>
                                                            How to Request a Refund
                                                        </h5>
                                                    </div>
                                                    <div class="refund-soft-card p-4 rounded">
                                                        @php
                                                            $steps = [
                                                                'Log in to your ProxWorld account',
                                                                'Navigate to Support section',
                                                                'Create a new support ticket',
                                                                'Select category: "Refund Request"',
                                                                'Provide your order ID and reason for refund',
                                                                'Include any relevant screenshots or evidence',
                                                            ];
                                                        @endphp
                                                        @foreach($steps as $step)
                                                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                                            <div class="flex-shrink-0">
                                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:0.85rem;">
                                                                    {{ $loop->iteration }}
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 d-flex align-items-center">
                                                                <strong>{{ $step }}</strong>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        <div class="d-flex gap-3">
                                                            <div class="flex-shrink-0">
                                                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                                                    <i class="feather-check"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 d-flex align-items-center">
                                                                <strong>Our 24/7 support team will review and respond within 24 hours</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Replacement Policy -->
                                                <div class="mb-5">
                                                    <div class="border-start border-success border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">
                                                            <i class="feather-repeat text-success me-2"></i>
                                                            Replacement Policy
                                                        </h5>
                                                    </div>
                                                    <p class="text-muted mb-3">
                                                        Some plans come with a replacement guarantee. If an assigned IP goes dead or gets blacklisted within the guarantee period:
                                                    </p>
                                                    <div class="refund-colored-card refund-colored-success p-4 rounded">
                                                        <div class="row g-3">
                                                            @php
                                                                $refills = [
                                                                    'Use the "Request Replacement" button on your order',
                                                                    'We will automatically refill the dropped quantity',
                                                                    'Replacements are free during the guarantee period',
                                                                    'Check individual plan descriptions for replacement guarantee duration',
                                                                ];
                                                            @endphp
                                                            @foreach($refills as $r)
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <i class="feather-check text-success mt-1 flex-shrink-0"></i>
                                                                    <span>{{ $r }}</span>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Footer Note -->
                                                <div class="pt-4 border-top">
                                                    <div class="refund-alert refund-alert-info mb-0">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="feather-info text-primary mt-1"></i>
                                                            <p class="mb-0 small">
                                                                This refund policy may be updated from time to time. Continued use of our services 
                                                                constitutes acceptance of any changes. 
                                                                <strong>Last updated: 20 Jan 2026</strong>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Contact Support CTA -->
                                        <div class="refund-cta-card card border-0 shadow-lg overflow-hidden mb-4">
                                            <div class="refund-hero-circle" style="top: -40px; right: -60px; width: 200px; height: 200px; z-index: 1;"></div>
                                            <div class="refund-hero-circle" style="bottom: -60px; left: -40px; width: 160px; height: 160px; z-index: 1;"></div>
                                            <div class="card-body text-center p-5 position-relative" style="z-index: 2;">
                                                <i class="feather-headphones fs-1 mb-3 d-block refund-cta-icon"></i>
                                                <h4 class="fw-bold mb-3 refund-cta-title">24/7 Customer Support</h4>
                                                <p class="mb-4 refund-cta-text mx-auto" style="max-width: 500px;">
                                                    Our support team is always available to help you with refund requests, 
                                                    order issues, or any questions you may have.
                                                </p>
                                                <div class="d-flex justify-content-center gap-3 flex-wrap">
                                                    @auth
                                                        <a href="{{ route('support.index') }}" class="btn btn-light btn-lg shadow">
                                                            <i class="feather-message-circle me-2"></i>Contact Support
                                                        </a>
                                                    @else
                                                        <a href="{{ route('login') }}" class="btn btn-light btn-lg shadow">
                                                            <i class="feather-log-in me-2"></i>Login to Contact Support
                                                        </a>
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

<style>
    html { scroll-behavior: smooth; }

    /* ============================================
       REFUND PAGE — LIGHT & DARK MODE COMPATIBLE
    ============================================ */

    /* Hero */
    .refund-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .refund-hero-title,
    .refund-hero-icon {
        color: #ffffff !important;
        text-shadow: 0 1px 4px rgba(0,0,0,0.3);
    }
    .refund-hero-subtitle {
        color: rgba(255,255,255,0.92) !important;
        text-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .refund-hero-meta {
        color: rgba(255,255,255,0.7) !important;
    }

    /* Decorative circles */
    .refund-hero-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        pointer-events: none;
        z-index: 1;
    }

    /* Icon circles in summary card */
    .refund-icon-circle { border-radius: 50%; }
    .refund-icon-success { background-color: rgba( 25, 135,  84, 0.12); }
    .refund-icon-primary { background-color: rgba( 13, 110, 253, 0.12); }
    .refund-icon-info    { background-color: rgba( 13, 202, 240, 0.12); }

    /* Soft neutral card (replaces bg-light) */
    .refund-soft-card {
        background-color: var(--bs-tertiary-bg, rgba(0,0,0,0.04));
        color: var(--bs-body-color);
    }

    /* Coloured tint cards */
    .refund-colored-card { color: var(--bs-body-color); }
    .refund-colored-success { background-color: rgba( 25, 135,  84, 0.12); }
    .refund-colored-danger  { background-color: rgba(220,  53,  69, 0.12); }
    .refund-colored-warning { background-color: rgba(255, 193,   7, 0.12); }
    .refund-colored-info    { background-color: rgba( 13, 202, 240, 0.12); }
    .refund-colored-primary { background-color: rgba( 13, 110, 253, 0.12); }

    /* Alert boxes */
    .refund-alert {
        padding: 0.85rem 1rem;
        border-radius: 0.5rem;
        color: var(--bs-body-color);
    }
    .refund-alert-info    { background-color: rgba( 13, 202, 240, 0.15); }
    .refund-alert-warning { background-color: rgba(255, 193,   7, 0.15); }
    .refund-alert-danger  { background-color: rgba(220,  53,  69, 0.15); }
    .refund-alert-primary { background-color: rgba( 13, 110, 253, 0.15); }
    .refund-alert-success { background-color: rgba( 25, 135,  84, 0.15); }

    /* Accordion — fully theme-aware */
    .refund-accordion-item {
        background-color: var(--bs-card-bg, var(--bs-body-bg)) !important;
        border-color: var(--bs-border-color) !important;
    }
    .refund-accordion-item .accordion-button {
        background-color: var(--bs-card-bg, var(--bs-body-bg)) !important;
        color: var(--bs-body-color) !important;
        transition: all 0.3s ease;
    }
    .refund-accordion-item .accordion-button:not(.collapsed) {
        background-color: var(--bs-tertiary-bg, rgba(0,0,0,0.03)) !important;
        color: var(--bs-body-color) !important;
        box-shadow: none !important;
    }
    .refund-accordion-item .accordion-button:focus {
        box-shadow: none !important;
    }
    .refund-accordion-item .accordion-body {
        background-color: var(--bs-card-bg, var(--bs-body-bg)) !important;
        color: var(--bs-body-color) !important;
    }
    .accordion-icon {
        transition: transform 0.3s ease;
    }
    .accordion-button:not(.collapsed) .accordion-icon {
        transform: rotate(90deg);
    }

    /* CTA card */
    .refund-cta-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .refund-cta-icon,
    .refund-cta-title,
    .refund-cta-text {
        color: #ffffff !important;
    }
</style>

@include('components.g-footer')