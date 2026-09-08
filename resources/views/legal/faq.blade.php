@include('components.g-header')

<main class="nxl-container apps-container apps-notes">
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content">
            <div class="content-area">
                <div class="content-area-body">
                    <div class="note-wrapper">
                        
                        <!-- Hero Header -->
                        <div class="faq-hero position-relative overflow-hidden" style="padding: 4rem 0;">
                            {{-- Decorative circles — pushed to far edges so they never overlap center text --}}
                            <div class="faq-hero-circle" style="top: -60px; left: -80px; width: 220px; height: 220px;"></div>
                            <div class="faq-hero-circle" style="bottom: -80px; right: -80px; width: 280px; height: 280px;"></div>

                            <div class="container-fluid position-relative" style="z-index: 2;">
                                <div class="row justify-content-center">
                                    <div class="col-xxl-8 col-xl-10 text-center">
                                        <a href="{{ route('welcome') }}" class="btn btn-light btn-sm mb-4 shadow-sm">
                                            <i class="feather-arrow-left me-2"></i>Back to Home
                                        </a>
                                        <h1 class="display-4 fw-bold mb-3 faq-hero-title">
                                            Frequently Asked Questions
                                        </h1>
                                        <p class="lead mb-0 mx-auto faq-hero-subtitle" style="max-width: 600px;">
                                            Find quick answers to common questions about ProxWorld. 
                                            Can't find what you're looking for? Our 24/7 support team is here to help!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="note-body" style="margin-top: -3rem;">
                            <div class="container-fluid">
                                <div class="row justify-content-center">
                                    <div class="col-xxl-8 col-xl-10">
                                        
                                        <!-- Quick Support Card -->
                                        <div class="card border-0 shadow-lg mb-5 overflow-hidden">
                                            <div class="row g-0">
                                                <div class="col-md-8">
                                                    <div class="card-body p-4">
                                                        <div class="d-flex align-items-start gap-3">
                                                            <div class="flex-shrink-0">
                                                                <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                                                    <i class="feather-headphones text-primary fs-3"></i>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h4 class="fw-bold mb-2">Need Help? We're Available 24/7</h4>
                                                                <p class="text-muted mb-3">
                                                                    Our dedicated support team is ready to assist you with any questions, 
                                                                    issues, or concerns you may have.
                                                                </p>
                                                                @auth
                                                                    <a href="{{ route('support.index') }}" class="btn btn-primary">
                                                                        <i class="feather-message-circle me-2"></i>
                                                                        Contact Support Now
                                                                    </a>
                                                                @else
                                                                    <a href="{{ route('login') }}" class="btn btn-primary">
                                                                        <i class="feather-log-in me-2"></i>
                                                                        Login to Get Support
                                                                    </a>
                                                                @endauth
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 faq-response-panel d-none d-md-flex align-items-center justify-content-center">
                                                    <div class="text-center p-3">
                                                        <i class="feather-clock fs-1 text-primary mb-2"></i>
                                                        <div class="fw-bold faq-response-label">Average Response</div>
                                                        <div class="h4 mb-0 faq-response-time">1-2 Hours</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Category Tabs -->
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-body p-0">
                                                <ul class="nav nav-tabs border-0" id="faqTabs" role="tablist" style="padding: 1.5rem 1.5rem 0;">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active px-4 py-3" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                                            <i class="feather-help-circle me-2"></i>General
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link px-4 py-3" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                                                            <i class="feather-shopping-cart me-2"></i>Orders
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link px-4 py-3" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">
                                                            <i class="feather-credit-card me-2"></i>Payment
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link px-4 py-3" id="quality-tab" data-bs-toggle="tab" data-bs-target="#quality" type="button" role="tab">
                                                            <i class="feather-shield me-2"></i>Quality
                                                        </button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link px-4 py-3" id="support-tab" data-bs-toggle="tab" data-bs-target="#support" type="button" role="tab">
                                                            <i class="feather-life-buoy me-2"></i>Support
                                                        </button>
                                                    </li>
                                                </ul>

                                                <div class="tab-content p-4" id="faqTabContent">
                                                    
                                                    <!-- General Questions Tab -->
                                                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                                                        <div class="accordion accordion-flush" id="generalAccordion">
                                                            
                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#general1">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        What is ProxWorld?
                                                                    </button>
                                                                </h2>
                                                                <div id="general1" class="accordion-collapse collapse show" data-bs-parent="#generalAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3">
                                                                            ProxWorld is a proxy reseller and API platform that helps individuals, businesses,
                                                                            and developers access residential, datacenter, ISP, and mobile proxies for any
                                                                            legitimate use case.
                                                                        </p>
                                                                        <p class="mb-0 text-muted">
                                                                            You can buy proxies directly, through our reseller panel, or through our API —
                                                                            all backed by multiple upstream providers for reliability.
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#general2">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        Is ProxWorld safe to use?
                                                                    </button>
                                                                </h2>
                                                                <div id="general2" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <div class="faq-alert faq-alert-success mb-3">
                                                                            <i class="feather-check-circle me-2"></i>
                                                                            <strong>Yes!</strong> We use industry-standard security measures to protect your account and payment information.
                                                                        </div>
                                                                        <p class="mb-2">
                                                                            We never ask for your other accounts' passwords, and all transactions are encrypted.
                                                                        </p>
                                                                        <div class="faq-alert faq-alert-warning mb-0">
                                                                            <i class="feather-alert-triangle me-2"></i>
                                                                            Please note that using any proxy service may violate platform terms of service. 
                                                                            We recommend reviewing your platform's policies before using our services.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#general3">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        What proxy types do you support?
                                                                    </button>
                                                                </h2>
                                                                <div id="general3" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3 fw-semibold">We currently support the following proxy types:</p>
                                                                        <div class="row g-3">
                                                                            @foreach(['Residential Proxies','Datacenter Proxies','ISP Proxies','Mobile Proxies','Rotating Proxies','Static/Dedicated Proxies','HTTP(S) Proxies','SOCKS5 Proxies'] as $platform)
                                                                            <div class="col-sm-6">
                                                                                <div class="faq-platform-item d-flex align-items-center gap-2 p-3 rounded">
                                                                                    <i class="feather-check text-success"></i>
                                                                                    <span>{{ $platform }}</span>
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
                                                                            <div class="col-sm-6">
                                                                                <div class="faq-platform-item d-flex align-items-center gap-2 p-3 rounded">
                                                                                    <i class="feather-plus text-primary"></i>
                                                                                    <span class="fw-semibold">Many more!</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#general4">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        Do I need to provide any account passwords?
                                                                    </button>
                                                                </h2>
                                                                <div id="general4" class="accordion-collapse collapse" data-bs-parent="#generalAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <div class="faq-alert faq-alert-danger">
                                                                            <div class="d-flex align-items-start gap-3">
                                                                                <i class="feather-shield fs-3"></i>
                                                                                <div>
                                                                                    <h6 class="mb-2"><strong>No! Absolutely not!</strong></h6>
                                                                                    <p class="mb-0">
                                                                                        We NEVER ask for passwords to any of your other accounts. All we need is your
                                                                                        order details (plan, quantity, configuration). Be wary of any service that asks
                                                                                        for unrelated account passwords — it's a red flag for scams.
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <!-- Orders & Delivery Tab -->
                                                    <div class="tab-pane fade" id="orders" role="tabpanel">
                                                        <div class="accordion accordion-flush" id="ordersAccordion">

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#orders1">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        How long does delivery take?
                                                                    </button>
                                                                </h2>
                                                                <div id="orders1" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3">Delivery times vary by service type:</p>
                                                                        <div class="row g-3 mb-3">
                                                                            <div class="col-sm-6">
                                                                                <div class="faq-delivery-card faq-delivery-success p-3 rounded">
                                                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                                                        <i class="feather-zap text-success"></i>
                                                                                        <strong>Instant Services</strong>
                                                                                    </div>
                                                                                    <small class="text-muted">Start within minutes</small>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="faq-delivery-card faq-delivery-primary p-3 rounded">
                                                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                                                        <i class="feather-fast-forward text-primary"></i>
                                                                                        <strong>Fast Services</strong>
                                                                                    </div>
                                                                                    <small class="text-muted">Complete within 1-6 hours</small>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="faq-delivery-card faq-delivery-info p-3 rounded">
                                                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                                                        <i class="feather-clock text-info"></i>
                                                                                        <strong>Standard Services</strong>
                                                                                    </div>
                                                                                    <small class="text-muted">Complete within 24-48 hours</small>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <div class="faq-delivery-card faq-delivery-warning p-3 rounded">
                                                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                                                        <i class="feather-trending-up text-warning"></i>
                                                                                        <strong>Gradual Services</strong>
                                                                                    </div>
                                                                                    <small class="text-muted">Over several days (natural)</small>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <p class="mb-0 text-muted small">
                                                                            <i class="feather-info me-1"></i>
                                                                            Check individual service descriptions for specific delivery timeframes.
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#orders2">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        How do I place an order?
                                                                    </button>
                                                                </h2>
                                                                <div id="orders2" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3 fw-semibold">Placing an order is simple and quick:</p>
                                                                        <div class="steps">
                                                                            @php
                                                                                $steps = [
                                                                                    ['num' => '1', 'title' => 'Create an account and log in', 'sub' => 'Sign up in seconds with your email', 'color' => 'bg-primary'],
                                                                                    ['num' => '2', 'title' => 'Add funds to your wallet', 'sub' => 'Multiple payment options available', 'color' => 'bg-primary'],
                                                                                    ['num' => '3', 'title' => 'Go to "New Order" section', 'sub' => 'Browse available services', 'color' => 'bg-primary'],
                                                                                    ['num' => '4', 'title' => 'Select your proxy type and plan', 'sub' => 'Choose what you need', 'color' => 'bg-primary'],
                                                                                    ['num' => '5', 'title' => 'Choose quantity and duration', 'sub' => 'Pick how much you need', 'color' => 'bg-primary'],
                                                                                    ['num' => '6', 'title' => 'Get your proxy credentials instantly', 'sub' => 'IP:port:user:pass, ready to use', 'color' => 'bg-primary'],
                                                                                ];
                                                                            @endphp
                                                                            @foreach($steps as $step)
                                                                            <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                                                                <div class="flex-shrink-0">
                                                                                    <div class="rounded-circle {{ $step['color'] }} text-white d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:0.85rem;">{{ $step['num'] }}</div>
                                                                                </div>
                                                                                <div class="flex-grow-1">
                                                                                    <strong>{{ $step['title'] }}</strong>
                                                                                    <p class="text-muted small mb-0">{{ $step['sub'] }}</p>
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
                                                                            <div class="d-flex gap-3">
                                                                                <div class="flex-shrink-0">
                                                                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                                                                        <i class="feather-check"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-grow-1">
                                                                                    <strong>Click "Submit Order"</strong>
                                                                                    <p class="text-muted small mb-0">Done! Track your order in real-time</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#orders3">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        Can I cancel my order?
                                                                    </button>
                                                                </h2>
                                                                <div id="orders3" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3">
                                                                            Orders can only be cancelled if they are still in <span class="badge bg-warning text-dark">Pending</span> status. 
                                                                            Once an order moves to <span class="badge bg-info">Processing</span>, it cannot be cancelled.
                                                                        </p>
                                                                        <div class="faq-alert faq-alert-info">
                                                                            <i class="feather-info me-2"></i>
                                                                            If you need to cancel, please contact our 24/7 support immediately. 
                                                                            Automatic refunds are issued if the provider cancels your order.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#orders4">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        What if my order doesn't complete?
                                                                    </button>
                                                                </h2>
                                                                <div id="orders4" class="accordion-collapse collapse" data-bs-parent="#ordersAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3">
                                                                            If your order shows as <span class="badge bg-warning text-dark">Partial</span> or doesn't deliver the full quantity, you have two options:
                                                                        </p>
                                                                        <div class="row g-3 mb-3">
                                                                            <div class="col-md-6">
                                                                                <div class="faq-option-card faq-option-primary p-3 rounded h-100">
                                                                                    <div class="d-flex align-items-start gap-2 mb-2">
                                                                                        <i class="feather-refresh-cw text-primary"></i>
                                                                                        <strong>Request a Refill</strong>
                                                                                    </div>
                                                                                    <p class="small mb-0">Use the "Request Refill" button on your order (if available)</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="faq-option-card faq-option-success p-3 rounded h-100">
                                                                                    <div class="d-flex align-items-start gap-2 mb-2">
                                                                                        <i class="feather-dollar-sign text-success"></i>
                                                                                        <strong>Request a Refund</strong>
                                                                                    </div>
                                                                                    <p class="small mb-0">Contact our 24/7 support team with your order details</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <!-- Payment & Wallet Tab -->
                                                    <div class="tab-pane fade" id="payment" role="tabpanel">
                                                        <div class="accordion accordion-flush" id="paymentAccordion">

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#payment1">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        What payment methods do you accept?
                                                                    </button>
                                                                </h2>
                                                                <div id="payment1" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3 fw-semibold">We accept multiple payment methods:</p>
                                                                        <div class="row g-3">
                                                                            @foreach(['KoraPay' => 'feather-send', 'Paystack' => 'feather-zap', 'Flutterwave' => 'feather-zap'] as $method => $icon)
                                                                            <div class="col-md-6">
                                                                                <div class="faq-platform-item d-flex align-items-center gap-2 p-3 rounded">
                                                                                    <i class="{{ $icon }} text-primary"></i>
                                                                                    <span>{{ $method }}</span>
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#payment2">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        How does the wallet system work?
                                                                    </button>
                                                                </h2>
                                                                <div id="payment2" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3">Our wallet system makes ordering quick and easy:</p>
                                                                        @php
                                                                            $walletSteps = [
                                                                                ['icon' => 'feather-plus', 'color' => 'bg-primary', 'title' => 'Deposit funds', 'sub' => 'Add money to your ProxWorld wallet'],
                                                                                ['icon' => 'feather-shopping-cart', 'color' => 'bg-success', 'title' => 'Place orders instantly', 'sub' => 'Use wallet balance for fast checkout'],
                                                                                ['icon' => 'feather-list', 'color' => 'bg-info', 'title' => 'Track transactions', 'sub' => 'View complete wallet history'],
                                                                                ['icon' => 'feather-refresh-cw', 'color' => 'bg-warning', 'title' => 'Get refunds', 'sub' => 'Refunds credited back to wallet'],
                                                                            ];
                                                                        @endphp
                                                                        <div class="faq-wallet-box p-3 rounded mb-3">
                                                                            @foreach($walletSteps as $ws)
                                                                            <div class="d-flex gap-3 mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                                                                                <div class="flex-shrink-0">
                                                                                    <div class="rounded-circle {{ $ws['color'] }} text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                                                                        <i class="{{ $ws['icon'] }}"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-grow-1">
                                                                                    <strong>{{ $ws['title'] }}</strong>
                                                                                    <p class="text-muted small mb-0">{{ $ws['sub'] }}</p>
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#payment3">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        Is there a minimum deposit amount?
                                                                    </button>
                                                                </h2>
                                                                <div id="payment3" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <div class="faq-alert faq-alert-info">
                                                                            <i class="feather-info me-2"></i>
                                                                            Yes, the minimum deposit amount varies by payment method but typically starts 
                                                                            from <strong>₦100</strong>. The exact amount will be displayed when you select your payment method.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#payment4">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        Can I get a refund to my bank account?
                                                                    </button>
                                                                </h2>
                                                                <div id="payment4" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3">All refunds are processed to your ProxWorld wallet. You can then use that balance for future orders.</p>
                                                                        <div class="faq-option-card faq-option-primary p-3 rounded text-center">
                                                                            <i class="feather-shopping-cart fs-2 text-primary mb-2 d-block"></i>
                                                                            <strong>Use balance for future orders</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <!-- Quality & Security Tab -->
                                                    <div class="tab-pane fade" id="quality" role="tabpanel">
                                                        <div class="accordion accordion-flush" id="qualityAccordion">

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#quality1">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        Are the proxies real?
                                                                    </button>
                                                                </h2>
                                                                <div id="quality1" class="accordion-collapse collapse" data-bs-parent="#qualityAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3">Yes — every proxy is sourced live from our upstream infrastructure providers. We offer different tiers depending on your needs:</p>
                                                                        <div class="row g-3 mb-3">
                                                                            <div class="col-12">
                                                                                <div class="faq-quality-card faq-quality-success p-3 rounded">
                                                                                    <div class="d-flex align-items-start gap-3">
                                                                                        <div class="badge bg-success">Premium</div>
                                                                                        <div>
                                                                                            <strong>Residential / Mobile</strong>
                                                                                            <p class="text-muted small mb-0">Real ISP-assigned IPs, hardest to detect or block</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="faq-quality-card faq-quality-primary p-3 rounded">
                                                                                    <div class="d-flex align-items-start gap-3">
                                                                                        <div class="badge bg-primary">High</div>
                                                                                        <div>
                                                                                            <strong>ISP Proxies</strong>
                                                                                            <p class="text-muted small mb-0">Datacenter speed with residential-style ASN ownership</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="faq-quality-card faq-quality-secondary p-3 rounded">
                                                                                    <div class="d-flex align-items-start gap-3">
                                                                                        <div class="badge bg-secondary">Standard</div>
                                                                                        <div>
                                                                                            <strong>Datacenter Proxies</strong>
                                                                                            <p class="text-muted small mb-0">Fastest and most affordable, best for high-volume tasks</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="faq-alert faq-alert-info mb-0">
                                                                            <i class="feather-trending-up me-2"></i>
                                                                            Higher-tier proxies cost more but offer better trust scores with strict target sites.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#quality2">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        Will I get blocked using these proxies?
                                                                    </button>
                                                                </h2>
                                                                <div id="quality2" class="accordion-collapse collapse" data-bs-parent="#qualityAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <div class="faq-alert faq-alert-warning mb-3">
                                                                            <i class="feather-alert-triangle me-2"></i>
                                                                            Some target websites actively detect and block proxy/VPN traffic under their own
                                                                            terms of service. We cannot guarantee every target site will accept every proxy.
                                                                        </div>
                                                                        <p class="mb-2 fw-semibold">To minimize the risk of being blocked:</p>
                                                                        <div class="row g-2">
                                                                            @foreach(['Choose residential or mobile proxies for strict targets','Don\'t send excessive request volume from one IP','Rotate IPs at a reasonable rate','Match the proxy location to your use case'] as $tip)
                                                                            <div class="col-md-6">
                                                                                <div class="faq-platform-item d-flex align-items-start gap-2 p-3 rounded">
                                                                                    <i class="feather-check text-success mt-1"></i>
                                                                                    <small>{{ $tip }}</small>
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#quality3">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        What is a replacement guarantee?
                                                                    </button>
                                                                </h2>
                                                                <div id="quality3" class="accordion-collapse collapse" data-bs-parent="#qualityAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3">
                                                                            Some plans come with a replacement guarantee (e.g., 30 days, 60 days, or lifetime).
                                                                            If an assigned IP goes dead or gets blacklisted during the guarantee period, you can request a free replacement.
                                                                        </p>
                                                                        <div class="faq-option-card faq-option-success p-3 rounded">
                                                                            <div class="d-flex align-items-start gap-3">
                                                                                <i class="feather-shield text-success fs-3"></i>
                                                                                <div>
                                                                                    <strong>How it works:</strong>
                                                                                    <p class="mb-0 mt-1 small">
                                                                                        Simply click the "Request Refill" button on your order, and we'll automatically 
                                                                                        replace the dropped quantity at no extra cost during the guarantee period.
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <!-- Support Tab -->
                                                    <div class="tab-pane fade" id="support" role="tabpanel">
                                                        <div class="accordion accordion-flush" id="supportAccordion">

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#support1">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        How do I contact support?
                                                                    </button>
                                                                </h2>
                                                                <div id="support1" class="accordion-collapse collapse" data-bs-parent="#supportAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <div class="faq-alert faq-alert-primary mb-3">
                                                                            <i class="feather-headphones me-2"></i>
                                                                            <strong>Our support team is available 24/7!</strong>
                                                                        </div>
                                                                        <p class="mb-2 fw-semibold">To contact us:</p>
                                                                        <div class="steps">
                                                                            @foreach(['Log in to your account','Go to the "Support" section','Create a new support ticket'] as $i => $step)
                                                                            <div class="d-flex gap-3 mb-2 pb-2 border-bottom">
                                                                                <span class="badge bg-primary">{{ $i + 1 }}</span>
                                                                                <span>{{ $step }}</span>
                                                                            </div>
                                                                            @endforeach
                                                                            <div class="d-flex gap-3">
                                                                                <span class="badge bg-success">✓</span>
                                                                                <span>Our team typically responds within 1-2 hours</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#support2">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        How fast does support respond?
                                                                    </button>
                                                                </h2>
                                                                <div id="support2" class="accordion-collapse collapse" data-bs-parent="#supportAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <div class="row g-3">
                                                                            <div class="col-md-6">
                                                                                <div class="faq-delivery-card faq-delivery-success p-3 rounded text-center">
                                                                                    <i class="feather-zap fs-1 text-success mb-2 d-block"></i>
                                                                                    <h5 class="mb-1">Typical Response</h5>
                                                                                    <p class="h4 text-success mb-0">1-2 Hours</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="faq-delivery-card faq-delivery-warning p-3 rounded text-center">
                                                                                    <i class="feather-clock fs-1 text-warning mb-2 d-block"></i>
                                                                                    <h5 class="mb-1">Peak Times</h5>
                                                                                    <p class="h4 text-warning mb-0">Up to 6 Hours</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="faq-alert faq-alert-info mt-3 mb-0">
                                                                            <i class="feather-alert-circle me-2"></i>
                                                                            Urgent issues are prioritized and handled as quickly as possible.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="accordion-item faq-accordion-item border rounded mb-3">
                                                                <h2 class="accordion-header">
                                                                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#support3">
                                                                        <i class="feather-chevron-right me-2 accordion-icon"></i>
                                                                        Can I get a demo or trial?
                                                                    </button>
                                                                </h2>
                                                                <div id="support3" class="accordion-collapse collapse" data-bs-parent="#supportAccordion">
                                                                    <div class="accordion-body pt-0">
                                                                        <p class="mb-3">We don't offer free trials, but you can:</p>
                                                                        <div class="row g-2">
                                                                            @foreach(['Start with small test orders','Check our order history','Contact 24/7 support for help','Read detailed service descriptions'] as $tip)
                                                                            <div class="col-md-6">
                                                                                <div class="faq-platform-item d-flex align-items-start gap-2 p-3 rounded">
                                                                                    <i class="feather-check text-primary mt-1"></i>
                                                                                    <small>{{ $tip }}</small>
                                                                                </div>
                                                                            </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- Bottom CTA -->
                                        <div class="faq-cta-card card border-0 shadow-lg overflow-hidden mb-4">
                                            {{-- Decorative circles — pushed to edges, behind content --}}
                                            <div class="faq-hero-circle" style="top: -40px; right: -60px; width: 200px; height: 200px; z-index: 1;"></div>
                                            <div class="faq-hero-circle" style="bottom: -60px; left: -40px; width: 160px; height: 160px; z-index: 1;"></div>

                                            <div class="card-body text-center p-5 position-relative" style="z-index: 2;">
                                                <i class="feather-message-circle fs-1 mb-3 d-block faq-cta-icon"></i>
                                                <h3 class="fw-bold mb-3 faq-cta-title">Still Have Questions?</h3>
                                                <p class="mb-4 faq-cta-text mx-auto" style="max-width: 500px;">
                                                    Don't hesitate to reach out! Our friendly support team is standing by 24/7 
                                                    to answer all your questions and help you get started.
                                                </p>
                                                <div class="d-flex justify-content-center gap-3 flex-wrap">
                                                    @auth
                                                        <a href="{{ route('support.index') }}" class="btn btn-light btn-lg shadow">
                                                            <i class="feather-headphones me-2"></i>Contact 24/7 Support
                                                        </a>
                                                        <a href="{{ route('order.create') }}" class="btn btn-outline-light btn-lg">
                                                            <i class="feather-zap me-2"></i>Place Your First Order
                                                        </a>
                                                    @else
                                                        <a href="{{ route('register') }}" class="btn btn-light btn-lg shadow">
                                                            <i class="feather-user-plus me-2"></i>Create Free Account
                                                        </a>
                                                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                                            <i class="feather-log-in me-2"></i>Login
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
    /* ============================================
       FAQ PAGE — LIGHT & DARK MODE COMPATIBLE
    ============================================ */

    /* Hero */
    .faq-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .faq-hero-title {
        color: #ffffff !important;
        text-shadow: 0 1px 4px rgba(0,0,0,0.3);
    }
    .faq-hero-subtitle {
        color: rgba(255,255,255,0.9) !important;
        text-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    /* Decorative circles — purely visual, never over text */
    .faq-hero-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        pointer-events: none;
        z-index: 1;
    }

    /* Support panel (right side of support card) */
    .faq-response-panel {
        background-color: rgba(13, 110, 253, 0.06);
    }
    .faq-response-label,
    .faq-response-time {
        color: var(--bs-body-color);
    }

    /* Accordion — fully theme-aware */
    .faq-accordion-item {
        background-color: var(--bs-card-bg, var(--bs-body-bg)) !important;
        border-color: var(--bs-border-color) !important;
    }
    .faq-accordion-item .accordion-button {
        background-color: var(--bs-card-bg, var(--bs-body-bg)) !important;
        color: var(--bs-body-color) !important;
        transition: all 0.3s ease;
    }
    .faq-accordion-item .accordion-button:not(.collapsed) {
        background-color: var(--bs-tertiary-bg, rgba(0,0,0,0.03)) !important;
        color: var(--bs-body-color) !important;
        box-shadow: none !important;
    }
    .faq-accordion-item .accordion-button::after {
        filter: var(--bs-accordion-btn-icon-filter, none);
    }
    .faq-accordion-item .accordion-button:focus {
        box-shadow: none !important;
    }
    .faq-accordion-item .accordion-body {
        background-color: var(--bs-card-bg, var(--bs-body-bg)) !important;
        color: var(--bs-body-color) !important;
    }
    .accordion-icon {
        transition: transform 0.3s ease;
    }
    .accordion-button:not(.collapsed) .accordion-icon {
        transform: rotate(90deg);
    }

    /* Custom alert boxes */
    .faq-alert {
        padding: 0.85rem 1rem;
        border-radius: 0.5rem;
        border: none;
        color: var(--bs-body-color);
    }
    .faq-alert-success  { background-color: rgba( 25, 135,  84, 0.15); }
    .faq-alert-warning  { background-color: rgba(255, 193,   7, 0.15); }
    .faq-alert-danger   { background-color: rgba(220,  53,  69, 0.15); }
    .faq-alert-info     { background-color: rgba( 13, 202, 240, 0.15); }
    .faq-alert-primary  { background-color: rgba( 13, 110, 253, 0.15); }

    /* Platform / tip list items */
    .faq-platform-item {
        background-color: var(--bs-tertiary-bg, rgba(0,0,0,0.04));
        color: var(--bs-body-color);
    }

    /* Delivery cards */
    .faq-delivery-card { color: var(--bs-body-color); }
    .faq-delivery-success  { background-color: rgba( 25, 135,  84, 0.12); }
    .faq-delivery-primary  { background-color: rgba( 13, 110, 253, 0.12); }
    .faq-delivery-info     { background-color: rgba( 13, 202, 240, 0.12); }
    .faq-delivery-warning  { background-color: rgba(255, 193,   7, 0.12); }

    /* Option cards */
    .faq-option-card { color: var(--bs-body-color); }
    .faq-option-primary { background-color: rgba( 13, 110, 253, 0.12); }
    .faq-option-success { background-color: rgba( 25, 135,  84, 0.12); }

    /* Wallet steps box */
    .faq-wallet-box {
        background-color: var(--bs-tertiary-bg, rgba(0,0,0,0.04));
    }

    /* Quality tier cards */
    .faq-quality-card { color: var(--bs-body-color); }
    .faq-quality-success  { background-color: rgba( 25, 135,  84, 0.12); border-left: 3px solid #198754; }
    .faq-quality-primary  { background-color: rgba( 13, 110, 253, 0.12); border-left: 3px solid #0d6efd; }
    .faq-quality-secondary{ background-color: rgba(108, 117, 125, 0.12); border-left: 3px solid #6c757d; }

    /* CTA card at the bottom */
    .faq-cta-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .faq-cta-icon,
    .faq-cta-title,
    .faq-cta-text {
        color: #ffffff !important;
    }

    /* Nav tabs */
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--bs-secondary-color, #6c757d);
        transition: all 0.3s ease;
        background: transparent;
    }
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #0d6efd;
    }
    .nav-tabs .nav-link.active {
        border-bottom-color: #0d6efd;
        color: #0d6efd !important;
        background: transparent;
        font-weight: 600;
    }

    /* Hover lift on accordion items */
    .faq-accordion-item {
        transition: box-shadow 0.2s ease;
    }
    .faq-accordion-item:hover {
        box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.08);
    }
</style>

@include('components.g-footer')