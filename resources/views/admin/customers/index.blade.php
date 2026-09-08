@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Customers</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Customers</li>
                </ul>
            </div>
        </div>
        <!-- Main Content -->
        <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="feather-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="feather-alert-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-text avatar-lg bg-primary-subtle me-3">
                                <i class="feather-users text-primary"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Total Customers</h6>
                                <h3 class="mb-0">{{ number_format($totalCustomers) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-text avatar-lg bg-success-subtle me-3">
                                <i class="feather-user-plus text-success"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">New Today</h6>
                                <h3 class="mb-0">{{ number_format($todayCustomers) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-text avatar-lg bg-info-subtle me-3">
                                <i class="feather-activity text-info"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1">Active (30 days)</h6>
                                <h3 class="mb-0">{{ number_format($activeCustomers) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

 
            <div class="row">
                <div class="col-lg-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">All Customers</h5>
                        </div>
                        
                        <!-- Search and Filter Form -->
                        <div class="card-body border-bottom">
                            <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Search by name, email, or ID..." 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" 
                                           name="date_from" 
                                           class="form-control" 
                                           placeholder="From Date"
                                           value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" 
                                           name="date_to" 
                                           class="form-control" 
                                           placeholder="To Date"
                                           value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <i class="feather-search me-1"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.customers.index') }}" class="btn btn-light">
                                            <i class="feather-x"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Customers Table -->
                        <div class="card-body custom-card-action p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr class="border-b">
                                            <th>Customer</th>
                                            <th>Email</th>
                                            <th>Orders</th>
                                            <th>Tickets</th>
                                            <th>Joined</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($customers as $customer)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-text avatar-md bg-soft-primary text-primary me-3">
                                                            {{ substr($customer->name, 0, 2) }}
                                                        </div>
                                                        <div>
                                                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="fw-bold d-block">
                                                                {{ $customer->name }}
                                                            </a>
                                                            <span class="fs-11 text-muted">ID: {{ substr($customer->id, 0, 8) }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $customer->email }}</td>
                                                <td>
                                                    <span class="badge bg-soft-info text-info">
                                                        {{ $customer->orders_count }} orders
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-warning text-warning">
                                                        {{ $customer->tickets_count }} tickets
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>{{ $customer->created_at->format('M d, Y') }}</div>
                                                    <span class="fs-11 text-muted">{{ $customer->created_at->diffForHumans() }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                                       class="btn btn-sm btn-light-brand">
                                                        <i class="feather-eye me-1"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="mb-3">
                                                        <i class="feather-users fs-1 text-muted"></i>
                                                    </div>
                                                    <h6 class="text-muted">No customers found</h6>
                                                    @if(request()->hasAny(['search', 'date_from', 'date_to']))
                                                        <p class="text-muted mb-0">Try adjusting your search or filters</p>
                                                        <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-primary mt-3">
                                                            Clear Filters
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($customers->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-0">
                                        Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers
                                    </p>
                                </div>
                                <div>
                                    {{ $customers->links() }}
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

@include('admin.components.footer')