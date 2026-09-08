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
                                                <i class="feather-briefcase refund-hero-icon" style="font-size: 3rem;"></i>
                                            </div>
                                            <h1 class="display-4 fw-bold refund-hero-title mb-3">Reseller Agreement</h1>
                                            <p class="lead refund-hero-subtitle mb-1">
                                                Terms for operating a white-label reseller storefront on {{ config('app.name') }}
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

                                                <p class="text-muted mb-5">
                                                    This Reseller Agreement applies in addition to our Terms of Use and Acceptable Use Policy to any
                                                    account approved to operate a white-label reseller storefront ("Reseller Panel") on {{ config('app.name') }}.
                                                </p>

                                                @php
                                                    $sections = [
                                                        ['icon'=>'feather-award','title'=>'1. Grant','text'=>'Upon approval, we grant you a non-exclusive, revocable right to operate a Reseller Panel under your chosen subdomain (and, if configured, your own custom domain), branded with your own name, logo, and primary color.'],
                                                        ['icon'=>'feather-percent','title'=>'2. Pricing & Markup','text'=>'ProxWorld sets the wholesale (reseller) price for each plan. You may set your own resale price at or above the reseller price — never below it. Your markup is credited to your withdrawable reseller profit balance when an order completes. You can override the default markup per plan, and hide any plan from your storefront.'],
                                                        ['icon'=>'feather-credit-card','title'=>'3. Wallet & Settlement','text'=>'Your Reseller Panel operates its own wallet, separate from your personal wallet, used to fund order fulfillment for your customers. Submitting a withdrawal request immediately reserves that amount pending review by our team.'],
                                                        ['icon'=>'feather-users','title'=>'4. Your Customers','text'=>'You are the first point of contact for your own customers\' support requests. You are solely responsible for ensuring your customers comply with our Acceptable Use Policy.'],
                                                        ['icon'=>'feather-slash','title'=>'5. Suspension & Termination','text'=>'We may suspend or terminate your Reseller Panel for violation of this Agreement, the Terms of Use, or the Acceptable Use Policy, or extended inactivity. Legitimately-earned profit not connected to a violation under investigation will still be paid out per our standard process.'],
                                                        ['icon'=>'feather-slash-circle','title'=>'6. No Employment or Partnership','text'=>'This Agreement does not create an employment, agency, joint venture, or partnership relationship between you and ProxWorld.'],
                                                        ['icon'=>'feather-refresh-cw','title'=>'7. Changes','text'=>'We may update pricing structures, default markups, or this Agreement from time to time, with notice provided through your Reseller Panel dashboard or by email.'],
                                                    ];
                                                @endphp

                                                @foreach($sections as $s)
                                                <div class="mb-5">
                                                    <div class="d-flex align-items-start gap-3 mb-2">
                                                        <div class="flex-shrink-0">
                                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                                                <i class="{{ $s['icon'] }} text-primary fs-4"></i>
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h5 class="fw-bold mb-2">{{ $s['title'] }}</h5>
                                                            <p class="text-muted mb-0">{{ $s['text'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach

                                                <div class="pt-4 border-top">
                                                    <div class="refund-alert refund-alert-info mb-0">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="feather-info text-primary mt-1"></i>
                                                            <p class="mb-0 small">
                                                                Have questions about the reseller program? Reach out any time.
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
                                                <i class="feather-trending-up fs-1 mb-3 d-block refund-cta-icon"></i>
                                                <h4 class="fw-bold mb-3 refund-cta-title">Ready to become a reseller?</h4>
                                                <p class="mb-4 refund-cta-text mx-auto" style="max-width: 500px;">Set your own prices, build your own brand, and earn on every sale.</p>
                                                <div class="d-flex justify-content-center gap-3 flex-wrap">
                                                    @auth
                                                        <a href="{{ route('reseller-panel.create') }}" class="btn btn-light btn-lg shadow"><i class="feather-arrow-right me-2"></i>Apply Now</a>
                                                    @else
                                                        <a href="{{ route('register') }}" class="btn btn-light btn-lg shadow"><i class="feather-user-plus me-2"></i>Create an Account First</a>
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
