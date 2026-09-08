@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Support Tickets</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Support</li>
                </ul>
            </div>
        </div>

        @if(session('alert'))
            <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show">
                <i class="feather-{{ session('alert')['type'] == 'success' ? 'check-circle' : 'alert-circle' }} me-2"></i>
                {{ session('alert')['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Total Tickets</p>
                                    <h3 class="mb-0">{{ number_format($stats['total']) }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-soft-primary text-primary">
                                    <i class="feather-inbox"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Open</p>
                                    <h3 class="mb-0 text-success">{{ number_format($stats['open']) }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-soft-success text-success">
                                    <i class="feather-alert-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">In Progress</p>
                                    <h3 class="mb-0 text-warning">{{ number_format($stats['in_progress']) }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-soft-warning text-warning">
                                    <i class="feather-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Closed</p>
                                    <h3 class="mb-0 text-secondary">{{ number_format($stats['closed']) }}</h3>
                                </div>
                                <div class="avatar-text avatar-lg bg-soft-secondary text-secondary">
                                    <i class="feather-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Tickets</h5>
                    <div class="card-header-action">
                        <!-- Filter Tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'all' ? 'active' : '' }}" 
                                   href="{{ route('admin.support.index', ['status' => 'all']) }}">
                                    All
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'open' ? 'active' : '' }}" 
                                   href="{{ route('admin.support.index', ['status' => 'open']) }}">
                                    Open
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'in_progress' ? 'active' : '' }}" 
                                   href="{{ route('admin.support.index', ['status' => 'in_progress']) }}">
                                    In Progress
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'closed' ? 'active' : '' }}" 
                                   href="{{ route('admin.support.index', ['status' => 'closed']) }}">
                                    Closed
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Customer</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <code class="fs-11">#{{ substr($ticket->id, 0, 8) }}</code>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-text avatar-sm bg-soft-primary text-primary me-2">
                                                    {{ substr($ticket->user->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <a href="{{ route('admin.customers.show', $ticket->user_id) }}" 
                                                       class="text-dark fw-bold">
                                                        {{ $ticket->user->name }}
                                                    </a>
                                                    <p class="fs-11 text-muted mb-0">{{ $ticket->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.support.show', $ticket->id) }}" 
                                               class="text-dark fw-bold">
                                                {{ Str::limit($ticket->subject, 40) }}
                                            </a>
                                            @if($ticket->latestMessage)
                                                <p class="fs-11 text-muted mb-0">
                                                    {{ Str::limit($ticket->latestMessage->message, 50) }}
                                                </p>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $ticket->getStatusBadgeClass() }}">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                                        <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.support.show', $ticket->id) }}" 
                                                   class="btn btn-light-brand">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                
                                                @if(auth('admin')->user()->canManageSupport())
                                                    @if(!$ticket->isClosed())
                                                        <form method="POST" 
                                                              action="{{ route('admin.support.close', $ticket->id) }}" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Close this ticket?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-light-brand">
                                                                <i class="feather-check"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" 
                                                              action="{{ route('admin.support.reopen', $ticket->id) }}" 
                                                              class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-light-brand">
                                                                <i class="feather-rotate-cw"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                                @if(auth('admin')->user()->isSuperAdmin())
                                                    <form method="POST" 
                                                          action="{{ route('admin.support.destroy', $ticket->id) }}" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Delete this ticket permanently?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-light-danger">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="feather-inbox fs-3 d-block mb-2"></i>
                                            No tickets found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($tickets->hasPages())
                <div class="card-footer">
                    {{ $tickets->links() }}
                </div>
                @endif
            </div>

        </div>

    </div>
</main>

@include('admin.components.footer')