@include('components.g-header')
@include('components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Profile Settings</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Profile</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="row">

                <!-- Identity Verification -->
                <div class="col-lg-6 mb-4">
                    <div class="card stretch stretch-full">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title">Identity Verification</h5>
                            <span class="badge bg-soft-{{ \App\Types\KycStatus::color(auth()->user()->kyc_status) }} text-{{ \App\Types\KycStatus::color(auth()->user()->kyc_status) }}">
                                {{ \App\Types\KycStatus::label(auth()->user()->kyc_status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            @if(auth()->user()->kyc_status === \App\Types\KycStatus::VERIFIED)
                                <p class="text-muted mb-0">
                                    Your identity was verified on {{ auth()->user()->kyc_verified_at?->format('M d, Y') }}.
                                </p>
                            @else
                                <p class="text-muted mb-3">You'll need to verify your identity before placing an order.</p>
                                <a href="{{ route('kyc.show') }}" class="btn btn-primary btn-sm">
                                    {{ auth()->user()->kyc_status === \App\Types\KycStatus::UNVERIFIED ? 'Verify now' : 'View status' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Update Info -->
                <div class="col-lg-6 mb-4">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Account Details</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" readonly>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="col-lg-6 mb-4">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.password') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-warning">Change Password</button>
                            </form>
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
