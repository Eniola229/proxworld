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
                                                <i class="feather-shield-off refund-hero-icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <h1 class="display-4 fw-bold refund-hero-title mb-3">Acceptable Use Policy</h1>
                                            <p class="lead refund-hero-subtitle mb-1">
                                                What you can and can't do with proxies obtained through {{ config('app.name') }}
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

                                        <div class="refund-alert refund-alert-warning mb-4">
                                            <div class="d-flex align-items-start gap-2">
                                                <i class="feather-alert-triangle mt-1"></i>
                                                <div>
                                                    <strong>This policy applies to everyone</strong> — including your own end customers if you operate a
                                                    reseller storefront. You're responsible for their compliance too.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-body p-md-5 p-4">

                                                <div class="mb-5">
                                                    <div class="border-start border-danger border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-x-octagon text-danger me-2"></i>Prohibited Uses</h5>
                                                    </div>
                                                    <p class="text-muted mb-4">You may not use {{ config('app.name') }} proxies, API, or reseller panels to:</p>
                                                    <div class="refund-colored-card refund-colored-danger p-4 rounded">
                                                        <div class="row g-3">
                                                            @php
                                                                $prohibited = [
                                                                    'Access, distribute, or store child sexual abuse material, or any content exploiting minors.',
                                                                    'Conduct or facilitate credential stuffing, account takeover, or brute-force login attempts.',
                                                                    'Send unsolicited bulk email, SMS, or messaging ("spam") of any kind.',
                                                                    'Distribute malware, ransomware, or conduct denial-of-service attacks.',
                                                                    'Scrape data in a way that harms or degrades a target site for others.',
                                                                    'Commit payment fraud, carding, or ad/click fraud.',
                                                                    'Circumvent geographic sanctions or export controls.',
                                                                    'Impersonate any person or entity.',
                                                                    'Violate the IP or privacy rights of any third party.',
                                                                    'Violate any applicable law in Nigeria, the traffic\'s destination, or your own jurisdiction.',
                                                                ];
                                                            @endphp
                                                            @foreach($prohibited as $p)
                                                            <div class="col-md-6">
                                                                <div class="d-flex align-items-start gap-2">
                                                                    <i class="feather-x text-danger small mt-1 flex-shrink-0"></i>
                                                                    <span class="text-muted small">{{ $p }}</span>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-5">
                                                    <div class="border-start border-primary border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-users text-primary me-2"></i>Reseller Responsibility</h5>
                                                    </div>
                                                    <p class="text-muted mb-0">
                                                        If you operate a reseller storefront, you are responsible for ensuring your own customers comply
                                                        with this AUP. Confirmed, repeated abuse originating from a reseller's storefront may result in
                                                        suspension of that reseller panel in addition to the offending end customer's account.
                                                    </p>
                                                </div>

                                                <div class="mb-5">
                                                    <div class="border-start border-warning border-4 ps-3 mb-4">
                                                        <h5 class="fw-bold mb-1"><i class="feather-alert-circle text-warning me-2"></i>Enforcement</h5>
                                                    </div>
                                                    <p class="text-muted mb-3">Violations may result in, without prior notice in serious cases:</p>
                                                    <div class="refund-soft-card p-4 rounded">
                                                        @php
                                                            $enforcement = [
                                                                'Immediate suspension of the account, API key, or reseller panel involved',
                                                                'Forfeiture of wallet balance associated with the abusive activity, where permitted by law',
                                                                'Reporting to law enforcement, where the activity is or may be illegal',
                                                            ];
                                                        @endphp
                                                        @foreach($enforcement as $i => $e)
                                                        <div class="d-flex gap-3 {{ $loop->last ? '' : 'mb-3 pb-3 border-bottom' }}">
                                                            <div class="flex-shrink-0">
                                                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;font-size:0.85rem;">{{ $i + 1 }}</div>
                                                            </div>
                                                            <div class="flex-grow-1 d-flex align-items-center"><span>{{ $e }}</span></div>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="pt-4 border-top">
                                                    <div class="refund-alert refund-alert-info mb-0">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="feather-info text-primary mt-1"></i>
                                                            <p class="mb-0 small">
                                                                If you believe {{ config('app.name') }} infrastructure is being used to target you or your
                                                                systems, contact support with as much detail as possible (timestamps, IPs, logs).
                                                                <strong>Last updated: {{ now()->format('d M Y') }}</strong>
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
                                                <h4 class="fw-bold mb-3 refund-cta-title">Report Abuse</h4>
                                                <p class="mb-4 refund-cta-text mx-auto" style="max-width: 500px;">See infrastructure being misused? Let us know.</p>
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
