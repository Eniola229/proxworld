@include('components.g-header')
@include('components.nav')

<main class="nxl-container">
<div class="nxl-content">

    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10">Identity Verification</h5>
            </div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Verification</li>
            </ul>
        </div>
    </div>

    <div class="main-content">

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-7 mx-auto">
                <div class="card stretch stretch-full">
                    <div class="card-body text-center py-5">

                        @if($user->kyc_status === \App\Types\KycStatus::VERIFIED)
                            <i class="feather-check-circle text-success" style="font-size:48px;"></i>
                            <h5 class="mt-3 mb-2">You're verified</h5>
                            <p class="text-muted mb-4">Your identity has been confirmed. You're all set to place orders.</p>
                            <a href="{{ route('order.create') }}" class="btn btn-primary">Place an order</a>

                        @elseif($user->kyc_status === \App\Types\KycStatus::PENDING || $user->kyc_status === \App\Types\KycStatus::IN_REVIEW)
                            <i class="feather-clock text-warning" style="font-size:48px;"></i>
                            <h5 class="mt-3 mb-2">Verification in progress</h5>
                            <p class="text-muted mb-4">
                                We're reviewing your submission. This usually only takes a moment —
                                refresh this page in a bit, or we'll notify you by email once it's done.
                            </p>
                            <form method="POST" action="{{ route('kyc.start') }}">
                                @csrf
                                <button type="submit" class="btn btn-light-brand">Restart verification</button>
                            </form>

                        @else
                            @if($user->kyc_status === \App\Types\KycStatus::DECLINED)
                                <i class="feather-alert-circle text-danger" style="font-size:48px;"></i>
                                <h5 class="mt-3 mb-2">Verification wasn't successful</h5>
                                <p class="text-muted mb-4">Your last attempt wasn't approved. Please try again with a clear photo of a valid ID.</p>
                            @else
                                <i class="feather-shield text-primary" style="font-size:48px;"></i>
                                <h5 class="mt-3 mb-2">Verify your identity to place orders</h5>
                                <p class="text-muted mb-4">
                                    We need to confirm your identity before you can place an order. It only takes
                                    a couple of minutes — you'll need a valid government-issued ID.
                                </p>
                            @endif
                            <form method="POST" action="{{ route('kyc.start') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Start verification</button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</main>

@include('components.g-footer')
</body>
</html>
