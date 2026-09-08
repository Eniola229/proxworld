
    @include('components.g-header')
    @include('components.nav')

    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Support Tickets</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Support</li>
                    </ul>
                </div>
            </div>

            <div class="main-content">
                <div class="row">
                    <!-- Create Ticket Form -->
                    <div class="col-lg-4 mb-4">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">New Ticket</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('support.store') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="subject" class="form-control" required placeholder="e.g. Order not completed">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Message</label>
                                        <textarea name="message" class="form-control" rows="5" required placeholder="Describe your issue..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Submit Ticket</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Ticket History -->
                    <div class="col-lg-8">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">My Tickets</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                         <tbody>
                                            @forelse($tickets as $ticket)
                                                <!-- We add onclick and cursor pointer to make the whole row work -->
                                                <tr onclick="window.location.href='{{ route('support.show', $ticket->id) }}'" style="cursor: pointer;">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="me-2 fw-bold">{{ $ticket->subject }}</span>
                                                            <i class="feather-arrow-right small text-muted"></i>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($ticket->status == 'open')
                                                            <span class="badge bg-warning">Open</span>
                                                        @elseif($ticket->status == 'in_progress')
                                                            <span class="badge bg-info">In Progress</span>
                                                        @else
                                                            <span class="badge bg-success">Closed</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-muted">No tickets found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
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