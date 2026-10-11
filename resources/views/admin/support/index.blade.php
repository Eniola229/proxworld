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

            <div id="ticketsLive">

            @php
                $mine = request()->boolean('mine');
                $meId = auth('admin')->id();
                $repliers = \App\Models\Ticket::repliersFor($tickets->pluck('id')->all());
                $tabQuery = fn ($s) => array_filter(['status' => $s, 'mine' => $mine ? 1 : null]);
            @endphp
            
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

            <!-- Admin Ratings -->
            @php $leaderboard = \App\Models\Ticket::adminRatingLeaderboard(); @endphp
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Support Ratings by Support Team</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Team Member</th>
                                    <th>Average Rating</th>
                                    <th class="text-end">Rated Tickets</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaderboard as $i => $row)
                                    <tr>
                                        <td><span class="badge {{ $i === 0 ? 'bg-success text-white' : 'bg-soft-secondary text-secondary' }}">{{ $i + 1 }}</span></td>
                                        <td class="fw-bold text-dark">{{ $row->name }}</td>
                                        <td>
                                            <span class="text-warning" style="letter-spacing:2px;">{{ str_repeat('★', (int) round($row->avg_rating)) }}<span class="text-muted">{{ str_repeat('★', 5 - (int) round($row->avg_rating)) }}</span></span>
                                            <strong class="ms-2">{{ number_format($row->avg_rating, 2) }}</strong><span class="text-muted"> / 5</span>
                                        </td>
                                        <td class="text-end">{{ number_format($row->ratings_count) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="feather-star fs-3 d-block mb-2"></i>
                                            No ratings yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Tickets</h5>
                    <div class="card-header-action d-flex align-items-center gap-3">
                        <!-- My Replies toggle -->
                        <a href="{{ route('admin.support.index', array_filter(['status' => $status !== 'all' ? $status : null, 'mine' => $mine ? null : 1])) }}"
                           class="btn btn-sm {{ $mine ? 'btn-primary' : 'btn-light-brand' }}">
                            <i class="feather-user-check me-1"></i> My Replies
                        </a>

                        <!-- Filter Tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'all' ? 'active' : '' }}" 
                                   href="{{ route('admin.support.index', $tabQuery('all')) }}">
                                    All
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'open' ? 'active' : '' }}" 
                                   href="{{ route('admin.support.index', $tabQuery('open')) }}">
                                    Open
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'in_progress' ? 'active' : '' }}" 
                                   href="{{ route('admin.support.index', $tabQuery('in_progress')) }}">
                                    In Progress
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status == 'closed' ? 'active' : '' }}" 
                                   href="{{ route('admin.support.index', $tabQuery('closed')) }}">
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
                                    <th>Handled By</th>
                                    <th>Last Updated</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    @php
                                        $badgeClass = $ticket->status === 'open'
                                            ? 'bg-success text-white'
                                            : $ticket->getStatusBadgeClass();
                                        $who = $repliers->get($ticket->id, collect());
                                        $lead = $who->first();
                                        $leadIsMe = $lead && (string) $lead->admin_id === (string) $meId;
                                    @endphp
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
                                            <span class="badge {{ $badgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($lead)
                                                <span class="badge {{ $leadIsMe ? 'bg-primary text-white' : 'bg-soft-warning text-warning' }}"
                                                      title="{{ $who->pluck('name')->implode(', ') }}">
                                                    <i class="feather-message-circle me-1"></i>
                                                    {{ $leadIsMe ? 'You' : $lead->name }}
                                                </span>
                                                @if($who->count() > 1)
                                                    <span class="fs-11 text-muted" title="{{ $who->pluck('name')->implode(', ') }}">+{{ $who->count() - 1 }} more</span>
                                                @endif
                                            @else
                                                <span class="badge bg-success text-white">Awaiting reply</span>
                                            @endif
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
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="feather-inbox fs-3 d-block mb-2"></i>
                                            {{ $mine ? 'You have not replied to any tickets here yet' : 'No tickets found' }}
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

    </div>
</main>

<script>
(function () {
    var POLL_MS = 10000;
    var busy = false;

    function refreshTickets() {
        var live = document.getElementById('ticketsLive');
        if (!live || busy || document.hidden) return;

        busy = true;
        fetch(window.location.href, {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.text();
            })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var fresh = doc.getElementById('ticketsLive');
                if (fresh && fresh.innerHTML.trim() !== live.innerHTML.trim()) {
                    live.innerHTML = fresh.innerHTML;
                }
            })
            .catch(function () { /* ignore, try again on next tick */ })
            .finally(function () { busy = false; });
    }

    setInterval(refreshTickets, POLL_MS);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) refreshTickets();
    });
})();
</script>

@include('admin.components.footer')