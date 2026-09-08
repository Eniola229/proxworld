@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Customer</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.show', $customer->id) }}">{{ $customer->name }}</a></li>
                    <li class="breadcrumb-item">Edit</li>
                </ul>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="feather-alert-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Customer Information</h5>
                        </div>
                        <div class="card-body"> 
                            <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="alert alert-info mb-4">
                                    <i class="feather-info me-2"></i>
                                    <strong>Customer ID:</strong> {{ $customer->id }}
                                </div>

                                <div class="mb-4">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $customer->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if(auth('admin')->user()->canManageAdmins())
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $customer->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif

    
                                <div class="mb-4">
                                    <label for="status" class="form-label">Account Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="ACTIVE" {{ old('status', $customer->status) === 'ACTIVE' ? 'selected' : '' }}>
                                            ✅ Active
                                        </option>
                                        <option value="BLOCK" {{ old('status', $customer->status) === 'BLOCK' ? 'selected' : '' }}>
                                            🚫 Blocked
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Blocked customers will be prevented from logging in.
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="feather-alert-triangle me-2"></i>
                                    <strong>Warning:</strong> All changes will be logged and visible to Super Admins. Make sure you have authorization to edit this customer's information.
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>
                                        Update Customer
                                    </button>
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-light">
                                        <i class="feather-x me-2"></i>
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Customer Stats -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Customer Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Total Orders:</span>
                                        <strong>{{ $customer->orders()->count() }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Total Spent:</span>
                                        <strong>₦{{ number_format($customer->orders()->sum('charge'), 2) }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Member Since:</span>
                                        <strong>{{ $customer->created_at->format('M d, Y') }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Last Updated:</span>
                                        <strong>{{ $customer->updated_at->diffForHumans() }}</strong>
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

@include('admin.components.footer')