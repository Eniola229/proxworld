@include('components.g-header')

<main class="nxl-container apps-container apps-notes">
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content">
            <div class="content-area">
                <div class="content-area-body">
                    <div class="note-wrapper">
                        
                        <!-- Hero Header -->
                        <div class="terms-hero position-relative overflow-hidden" style="padding: 4rem 0;">
                            <div class="terms-hero-circle" style="top: -60px; left: -80px; width: 220px; height: 220px;"></div>
                            <div class="terms-hero-circle" style="bottom: -80px; right: -80px; width: 280px; height: 280px;"></div>

                            <div class="container-fluid position-relative" style="z-index: 2;">
                                <div class="row justify-content-center">
                                    <div class="col-xxl-8 col-xl-10">
                                        <a href="{{ route('welcome') }}" class="btn btn-light btn-sm mb-4 shadow-sm">
                                            <i class="feather-arrow-left me-2"></i>Back to Home
                                        </a>
                                        <div class="text-center">
                                            <div class="mb-3">
                                                <i class="feather-file-text terms-hero-icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <h1 class="display-4 fw-bold terms-hero-title mb-3">Terms of Use</h1>
                                            <p class="lead terms-hero-subtitle mb-1">
                                                Please read these terms carefully before using our services
                                            </p>
                                            <p class="terms-hero-meta small mb-0">Last Updated: 20 Jan 2026</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="note-body" style="margin-top: -3rem;">
                            <div class="container-fluid">
                                <div class="row justify-content-center">
                                    <div class="col-xxl-8 col-xl-10">

                                        <!-- Quick Navigation -->
                                        <div class="card border-0 shadow-lg mb-4">
                                            <div class="card-body p-4">
                                                <h6 class="fw-bold mb-3">Quick Navigation</h6>
                                                <div class="row g-2">
                                                    <div class="col-md-4 col-sm-6">
                                                        <a href="#section-1" class="btn btn-outline-primary btn-sm w-100 text-start">
                                                            <i class="feather-user me-2"></i>Account Registration
                                                        </a>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6">
                                                        <a href="#section-2" class="btn btn-outline-primary btn-sm w-100 text-start">
                                                            <i class="feather-package me-2"></i>Services Description
                                                        </a>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6">
                                                        <a href="#section-3" class="btn btn-outline-primary btn-sm w-100 text-start">
                                                            <i class="feather-credit-card me-2"></i>Payment & Billing
                                                        </a>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6">
                                                        <a href="#section-4" class="btn btn-outline-primary btn-sm w-100 text-start">
                                                            <i class="feather-check-circle me-2"></i>User Responsibilities
                                                        </a>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6">
                                                        <a href="#section-5" class="btn btn-outline-primary btn-sm w-100 text-start">
                                                            <i class="feather-alert-triangle me-2"></i>Prohibited Activities
                                                        </a>
                                                    </div>
                                                    <div class="col-md-4 col-sm-6">
                                                        <a href="#section-6" class="btn btn-outline-primary btn-sm w-100 text-start">
                                                            <i class="feather-rotate-ccw me-2"></i>Refunds & Cancellations
                                                        </a>
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
                                                                <i class="feather-file-text text-primary fs-4"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h4 class="fw-bold mb-2">Agreement to Terms</h4>
                                                            <p class="text-muted mb-0">
                                                                Welcome to ProxWorld! By accessing and using our proxy services, 
                                                                you agree to be bound by these Terms of Use. Please read them carefully.
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="terms-alert terms-alert-info">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="feather-clock text-info mt-1"></i>
                                                            <div>
                                                                <strong>24/7 Support Available:</strong>
                                                                <p class="mb-0 mt-1">Questions about our terms? Our support team is available around the clock to help clarify any points.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 1: Account Registration -->
                                                <div class="mb-5" id="section-1">
                                                    <div class="border-start border-primary border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">1. Account Registration</h5>
                                                    </div>
                                                    <div class="row g-3">
                                                        @php
                                                            $reg = [
                                                                ['icon'=>'feather-user',  'id'=>'1.1','title'=>'Eligibility',           'text'=>'You must be at least 18 years old to use our services.'],
                                                                ['icon'=>'feather-lock',  'id'=>'1.2','title'=>'Account Security',       'text'=>'You are responsible for maintaining the confidentiality of your account credentials.'],
                                                                ['icon'=>'feather-info',  'id'=>'1.3','title'=>'Accurate Information',   'text'=>'You must provide accurate and complete information during registration.'],
                                                                ['icon'=>'feather-users', 'id'=>'1.4','title'=>'One Account Per User',   'text'=>'Each user is permitted one account. Multiple accounts may be suspended.'],
                                                            ];
                                                        @endphp
                                                        @foreach($reg as $item)
                                                        <div class="col-md-6">
                                                            <div class="terms-soft-card h-100 p-3 rounded">
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <i class="{{ $item['icon'] }} text-primary mt-1"></i>
                                                                    <div>
                                                                        <strong class="d-block mb-2">{{ $item['id'] }} {{ $item['title'] }}</strong>
                                                                        <p class="text-muted small mb-0">{{ $item['text'] }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Section 2: Services Description -->
                                                <div class="mb-5" id="section-2">
                                                    <div class="border-start border-success border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">2. Services Description</h5>
                                                    </div>
                                                    <div class="terms-colored-card terms-colored-success p-3 rounded mb-3">
                                                        <div class="d-flex align-items-start gap-3">
                                                            <i class="feather-check-circle text-success fs-4"></i>
                                                            <div>
                                                                <strong class="d-block mb-2">2.1 What We Offer</strong>
                                                                <p class="text-muted mb-0">ProxWorld resells proxy access (residential, datacenter, ISP, and mobile) sourced from third-party infrastructure providers, available for direct purchase, via our reseller panel, or via our API.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row g-3">
                                                        @php
                                                            $services = [
                                                                ['id'=>'2.2','title'=>'No Guarantees',        'text'=>'While we strive for quality, we cannot guarantee specific results or permanent retention of delivered services.'],
                                                                ['id'=>'2.3','title'=>'Provider Changes',     'text'=>'Services may be affected by changes to our upstream proxy providers\' availability, pricing, or policies.'],
                                                                ['id'=>'2.4','title'=>'Service Availability', 'text'=>'We reserve the right to modify, suspend, or discontinue any service at any time.'],
                                                            ];
                                                        @endphp
                                                        @foreach($services as $s)
                                                        <div class="col-md-4">
                                                            <div class="terms-soft-card h-100 p-3 rounded">
                                                                <strong class="d-block mb-2">{{ $s['id'] }} {{ $s['title'] }}</strong>
                                                                <p class="text-muted small mb-0">{{ $s['text'] }}</p>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Section 3: Payment and Billing -->
                                                <div class="mb-5" id="section-3">
                                                    <div class="border-start border-warning border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">3. Payment and Billing</h5>
                                                    </div>
                                                    <div class="terms-alert terms-alert-warning mb-3">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="feather-alert-circle text-warning mt-1"></i>
                                                            <div>
                                                                <strong>Important:</strong>
                                                                <p class="mb-0 mt-1">Wallet deposits are non-refundable and can only be used to place orders on our platform. There is no withdrawal option to bank accounts.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row g-3">
                                                        @php
                                                            $payments = [
                                                                ['icon'=>'feather-wallet',      'id'=>'3.1','title'=>'Wallet System',   'text'=>'All purchases are made using credits in your ProxWorld wallet.'],
                                                                ['icon'=>'feather-dollar-sign', 'id'=>'3.2','title'=>'Deposits',        'text'=>'Wallet deposits are final and can only be used for placing orders on ProxWorld.'],
                                                                ['icon'=>'feather-tag',         'id'=>'3.3','title'=>'Pricing',         'text'=>'All prices are displayed in Nigerian Naira (₦) and are subject to change without notice.'],
                                                                ['icon'=>'feather-zap',         'id'=>'3.4','title'=>'Order Deduction', 'text'=>'Funds are deducted from your wallet immediately when an order is placed.'],
                                                            ];
                                                        @endphp
                                                        @foreach($payments as $p)
                                                        <div class="col-md-6">
                                                            <div class="terms-soft-card p-3 rounded">
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <i class="{{ $p['icon'] }} text-warning mt-1"></i>
                                                                    <div>
                                                                        <strong class="d-block mb-2">{{ $p['id'] }} {{ $p['title'] }}</strong>
                                                                        <p class="text-muted small mb-0">{{ $p['text'] }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        <div class="col-12">
                                                            <div class="terms-colored-card terms-colored-danger p-3 rounded">
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <i class="feather-x-circle text-danger mt-1"></i>
                                                                    <div>
                                                                        <strong class="text-danger d-block mb-2">3.5 No Chargebacks</strong>
                                                                        <p class="text-muted small mb-0">Initiating chargebacks may result in account suspension and legal action.</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 4: User Responsibilities -->
                                                <div class="mb-5" id="section-4">
                                                    <div class="border-start border-info border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">4. User Responsibilities</h5>
                                                    </div>
                                                    <div class="list-group list-group-flush">
                                                        @php
                                                            $responsibilities = [
                                                                ['id'=>'4.1','title'=>'Accurate Information','text'=>'You must provide correct order details (plan, quantity, configuration) when placing orders.'],
                                                                ['id'=>'4.2','title'=>'Lawful Use',      'text'=>'You are responsible for using proxies obtained through ProxWorld lawfully — see our Acceptable Use Policy.'],
                                                                ['id'=>'4.3','title'=>'Compliance',          'text'=>'You are responsible for ensuring your use of our services complies with the terms of any third-party website or service you access through a proxy.'],
                                                                ['id'=>'4.4','title'=>'No Illegal Use',      'text'=>'Our services must not be used for illegal purposes or to violate third-party rights.'],
                                                                ['id'=>'4.5','title'=>'Third-Party Risk',        'text'=>'You acknowledge that some target websites restrict or block proxy/VPN traffic, and ProxWorld is not responsible for such third-party restrictions.'],
                                                            ];
                                                        @endphp
                                                        @foreach($responsibilities as $r)
                                                        <div class="list-group-item terms-list-item px-0 {{ $loop->last ? 'border-bottom' : '' }}">
                                                            <div class="d-flex gap-3">
                                                                <div class="badge bg-info text-white flex-shrink-0" style="height:fit-content;">{{ $r['id'] }}</div>
                                                                <div>
                                                                    <strong class="d-block mb-1">{{ $r['title'] }}</strong>
                                                                    <p class="text-muted mb-0 small">{{ $r['text'] }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Section 5: Prohibited Activities -->
                                                <div class="mb-5" id="section-5">
                                                    <div class="border-start border-danger border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">5. Prohibited Activities</h5>
                                                    </div>
                                                    <div class="terms-colored-card terms-colored-danger p-4 rounded">
                                                        <div class="d-flex align-items-start gap-3 mb-3">
                                                            <i class="feather-alert-octagon text-danger fs-3"></i>
                                                            <h6 class="fw-bold mb-0 mt-1">You may NOT:</h6>
                                                        </div>
                                                        <div class="row g-2">
                                                            @php
                                                                $prohibited = [
                                                                    'Resell our services without authorization',
                                                                    'Use automated systems or bots to access our platform',
                                                                    'Attempt to hack, reverse engineer, or exploit our systems',
                                                                    'Create multiple accounts to abuse promotions or refunds',
                                                                    'Share your account credentials with others',
                                                                    'Place orders for competitors\' accounts without permission',
                                                                    'Engage in fraudulent payment activities',
                                                                ];
                                                            @endphp
                                                            @foreach($prohibited as $item)
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <i class="feather-x text-danger small mt-1 flex-shrink-0"></i>
                                                                    <span class="small">{{ $item }}</span>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 6: Refunds and Cancellations -->
                                                <div class="mb-5" id="section-6">
                                                    <div class="border-start border-primary border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">6. Refunds and Cancellations</h5>
                                                    </div>
                                                    <div class="row g-3">
                                                        @php
                                                            $refunds = [
                                                                ['icon'=>'feather-rotate-ccw','color'=>'text-primary',  'id'=>'6.1','title'=>'Refund Policy',       'text'=>'Refunds are subject to our Refund Policy. Please review it carefully before placing orders.'],
                                                                ['icon'=>'feather-x-circle',  'color'=>'text-warning',  'id'=>'6.2','title'=>'Order Cancellation',  'text'=>'Orders cannot be cancelled once they enter "Processing" status.'],
                                                                ['icon'=>'feather-refresh-cw', 'color'=>'text-success', 'id'=>'6.3','title'=>'Automatic Refunds',   'text'=>'System failures or provider cancellations trigger automatic refunds to your wallet.'],
                                                            ];
                                                        @endphp
                                                        @foreach($refunds as $r)
                                                        <div class="col-md-4">
                                                            <div class="terms-soft-card h-100 p-3 rounded text-center">
                                                                <i class="{{ $r['icon'] }} {{ $r['color'] }} fs-2 mb-3 d-block"></i>
                                                                <strong class="d-block mb-2">{{ $r['id'] }} {{ $r['title'] }}</strong>
                                                                <p class="text-muted small mb-0">{{ $r['text'] }}</p>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Section 7: Intellectual Property -->
                                                <div class="mb-5">
                                                    <div class="border-start border-secondary border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">7. Intellectual Property</h5>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <div class="terms-soft-card p-3 rounded">
                                                                <strong class="d-block mb-2">7.1 Our Content</strong>
                                                                <p class="text-muted small mb-0">All content on ProxWorld, including logos, text, graphics, and software, is our property.</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="terms-soft-card p-3 rounded">
                                                                <strong class="d-block mb-2">7.2 Usage Restrictions</strong>
                                                                <p class="text-muted small mb-0">You may not copy, reproduce, or distribute our content without written permission.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 8: Disclaimer of Warranties -->
                                                <div class="mb-5">
                                                    <div class="border-start border-warning border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">8. Disclaimer of Warranties</h5>
                                                    </div>
                                                    <div class="terms-colored-card terms-colored-warning p-4 rounded">
                                                        <div class="row g-3">
                                                            @php
                                                                $warranties = [
                                                                    ['icon'=>'feather-info',           'id'=>'8.1','title'=>'"As Is" Services',    'text'=>'Our services are provided "as is" without warranties of any kind.'],
                                                                    ['icon'=>'feather-alert-triangle',  'id'=>'8.2','title'=>'No Guarantees',       'text'=>'We do not guarantee uninterrupted, error-free, or secure service.'],
                                                                    ['icon'=>'feather-link',            'id'=>'8.3','title'=>'Third-Party Services', 'text'=>'We are not responsible for failures caused by third-party platforms or providers.'],
                                                                ];
                                                            @endphp
                                                            @foreach($warranties as $w)
                                                            <div class="col-md-4 text-center">
                                                                <i class="{{ $w['icon'] }} text-warning fs-3 mb-2 d-block"></i>
                                                                <strong class="d-block mb-1 small">{{ $w['id'] }} {{ $w['title'] }}</strong>
                                                                <p class="text-muted small mb-0">{{ $w['text'] }}</p>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 9: Limitation of Liability -->
                                                <div class="mb-5">
                                                    <div class="border-start border-danger border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">9. Limitation of Liability</h5>
                                                    </div>
                                                    <div class="list-group list-group-flush">
                                                        @php
                                                            $liabilities = [
                                                                ['id'=>'9.1','title'=>'Maximum Liability',  'text'=>'Our liability is limited to the amount you paid for the specific service in question.'],
                                                                ['id'=>'9.2','title'=>'No Indirect Damages','text'=>'We are not liable for indirect, incidental, or consequential damages.'],
                                                                ['id'=>'9.3','title'=>'Third-Party Restrictions', 'text'=>'We are not liable for consequences resulting from a third-party website blocking, blacklisting, or restricting proxy traffic.'],
                                                            ];
                                                        @endphp
                                                        @foreach($liabilities as $l)
                                                        <div class="list-group-item terms-list-item px-0 {{ $loop->last ? 'border-bottom' : '' }}">
                                                            <strong>{{ $l['id'] }} {{ $l['title'] }}:</strong>
                                                            <p class="text-muted mb-0 small">{{ $l['text'] }}</p>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Section 10: Account Termination -->
                                                <div class="mb-5">
                                                    <div class="border-start border-4 ps-3 mb-4" style="border-color: var(--bs-body-color) !important;">
                                                        <h5 class="fw-bold mb-1">10. Account Termination</h5>
                                                    </div>
                                                    <div class="row g-3">
                                                        @php
                                                            $terminations = [
                                                                ['icon'=>'feather-slash',    'id'=>'10.1','title'=>'Our Right',              'text'=>'We reserve the right to suspend or terminate accounts that violate these terms.'],
                                                                ['icon'=>'feather-x-circle', 'id'=>'10.2','title'=>'No Refund on Termination','text'=>'Terminated accounts forfeit remaining wallet balance except in cases of our error.'],
                                                                ['icon'=>'feather-log-out',  'id'=>'10.3','title'=>'User Termination',        'text'=>'You may close your account at any time by contacting support.'],
                                                            ];
                                                        @endphp
                                                        @foreach($terminations as $t)
                                                        <div class="col-md-4">
                                                            <div class="terms-soft-card h-100 p-3 rounded text-center">
                                                                <i class="{{ $t['icon'] }} fs-2 mb-3 d-block text-muted"></i>
                                                                <strong class="d-block mb-2">{{ $t['id'] }} {{ $t['title'] }}</strong>
                                                                <p class="text-muted small mb-0">{{ $t['text'] }}</p>
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Section 11: Privacy -->
                                                <div class="mb-5">
                                                    <div class="border-start border-success border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">11. Privacy and Data Protection</h5>
                                                    </div>
                                                    <div class="terms-colored-card terms-colored-success p-4 rounded">
                                                        <div class="row g-3">
                                                            @php
                                                                $privacy = [
                                                                    ['icon'=>'feather-database','id'=>'11.1','title'=>'Data Collection','text'=>'We collect and process personal data as described in our Privacy Policy.'],
                                                                    ['icon'=>'feather-shield',  'id'=>'11.2','title'=>'Security',       'text'=>'We implement reasonable security measures to protect your data.'],
                                                                    ['icon'=>'feather-lock',    'id'=>'11.3','title'=>'No Sale',        'text'=>'We do not sell your personal information to third parties.'],
                                                                ];
                                                            @endphp
                                                            @foreach($privacy as $p)
                                                            <div class="col-md-4">
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <i class="{{ $p['icon'] }} text-success mt-1"></i>
                                                                    <div>
                                                                        <strong class="d-block mb-1 small">{{ $p['id'] }} {{ $p['title'] }}</strong>
                                                                        <p class="text-muted small mb-0">{{ $p['text'] }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 12: Changes to Terms -->
                                                <div class="mb-5">
                                                    <div class="border-start border-info border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">12. Changes to Terms</h5>
                                                    </div>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <div class="terms-colored-card terms-colored-info p-3 rounded">
                                                                <strong class="d-block mb-2">12.1 Modification Rights</strong>
                                                                <p class="text-muted small mb-0">We may update these terms at any time without prior notice.</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="terms-colored-card terms-colored-info p-3 rounded">
                                                                <strong class="d-block mb-2">12.2 Continued Use</strong>
                                                                <p class="text-muted small mb-0">Continued use of our services after changes constitutes acceptance of new terms.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Section 13: Governing Law -->
                              <!--                   <div class="mb-5">
                                                    <div class="border-start border-primary border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1">13. Governing Law</h5>
                                                    </div>
                                                    <div class="terms-alert terms-alert-primary">
                                                        <div class="d-flex align-items-start gap-3">
                                                            <i class="feather-map-pin text-primary fs-4"></i>
                                                            <p class="mb-0">These Terms of Use are governed by the laws of the <strong>Federal Republic of Nigeria</strong>.</p>
                                                        </div>
                                                    </div>
                                                </div>
 -->
                                                <!-- Footer Note -->
                                                <div class="pt-4 border-top">
                                                    <div class="terms-alert terms-alert-info mb-0">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="feather-info text-primary mt-1"></i>
                                                            <p class="mb-0 small">
                                                                By using ProxWorld services, you acknowledge that you have read, understood, 
                                                                and agree to be bound by these Terms of Use. 
                                                                <strong>Last updated: 20 Jan 2026</strong>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Contact Support CTA -->
                                        <div class="terms-cta-card card border-0 shadow-lg overflow-hidden mb-4">
                                            <div class="terms-hero-circle" style="top: -40px; right: -60px; width: 200px; height: 200px; z-index: 1;"></div>
                                            <div class="terms-hero-circle" style="bottom: -60px; left: -40px; width: 160px; height: 160px; z-index: 1;"></div>
                                            <div class="card-body text-center p-5 position-relative" style="z-index: 2;">
                                                <i class="feather-headphones fs-1 mb-3 d-block terms-cta-icon"></i>
                                                <h4 class="fw-bold mb-3 terms-cta-title">Questions About Our Terms?</h4>
                                                <p class="mb-4 terms-cta-text mx-auto" style="max-width: 500px;">
                                                    Our 24/7 support team is always available to answer your questions 
                                                    and provide clarification on any aspect of our Terms of Use.
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
       TERMS PAGE — LIGHT & DARK MODE COMPATIBLE
    ============================================ */

    /* Hero */
    .terms-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .terms-hero-title,
    .terms-hero-icon {
        color: #ffffff !important;
        text-shadow: 0 1px 4px rgba(0,0,0,0.3);
    }
    .terms-hero-subtitle {
        color: rgba(255,255,255,0.92) !important;
        text-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .terms-hero-meta {
        color: rgba(255,255,255,0.7) !important;
    }

    /* Decorative circles — semi-transparent, never cover text */
    .terms-hero-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.12);
        pointer-events: none;
        z-index: 1;
    }

    /* Soft neutral cards (replaces bg-light) */
    .terms-soft-card {
        background-color: var(--bs-tertiary-bg, rgba(0,0,0,0.04));
        color: var(--bs-body-color);
    }

    /* Coloured tint cards (replaces bg-X bg-opacity-10) */
    .terms-colored-card { color: var(--bs-body-color); }
    .terms-colored-success { background-color: rgba( 25, 135,  84, 0.12); }
    .terms-colored-danger  { background-color: rgba(220,  53,  69, 0.12); }
    .terms-colored-warning { background-color: rgba(255, 193,   7, 0.12); }
    .terms-colored-info    { background-color: rgba( 13, 202, 240, 0.12); }
    .terms-colored-primary { background-color: rgba( 13, 110, 253, 0.12); }

    /* Alert boxes (replaces Bootstrap alerts with hardcoded text-dark) */
    .terms-alert {
        padding: 0.85rem 1rem;
        border-radius: 0.5rem;
        color: var(--bs-body-color);
    }
    .terms-alert-info    { background-color: rgba( 13, 202, 240, 0.15); }
    .terms-alert-warning { background-color: rgba(255, 193,   7, 0.15); }
    .terms-alert-danger  { background-color: rgba(220,  53,  69, 0.15); }
    .terms-alert-primary { background-color: rgba( 13, 110, 253, 0.15); }
    .terms-alert-success { background-color: rgba( 25, 135,  84, 0.15); }

    /* List items — transparent bg so card bg shows through */
    .terms-list-item {
        background-color: transparent !important;
        color: var(--bs-body-color);
    }
    .terms-list-item:hover {
        background-color: var(--bs-tertiary-bg, rgba(0,0,0,0.02)) !important;
    }

    /* CTA card */
    .terms-cta-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .terms-cta-icon,
    .terms-cta-title,
    .terms-cta-text {
        color: #ffffff !important;
    }

    /* Quick nav button hover */
    .btn-outline-primary {
        transition: transform 0.2s ease;
    }
    .btn-outline-primary:hover {
        transform: translateY(-2px);
    }
</style>

@include('components.g-footer')