@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Newsletter Management</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item">Newsletters</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.newsletters.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>Create Newsletter
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="feather-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="feather-alert-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">All Newsletters</h5>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="border-b">
                                    <th>Subject</th>
                                    <th>Audience</th>
                                    <th>Status</th>
                                    <th>Scheduled At</th>
                                    <th>Created At</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($newsletters as $newsletter)
                                    <tr>
                                        <td>
                                            <strong>{{ $newsletter->subject }}</strong>
                                            @if($newsletter->excerpt)
                                                <p class="text-muted small mb-0">{{ Str::limit($newsletter->excerpt, 60) }}</p>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-soft-info text-info">{{ $newsletter->audience }}</span></td>
                                        <td>
                                            <span class="badge bg-soft-{{ $newsletter->status === 'sent' ? 'success text-success' : ($newsletter->status === 'sending' ? 'warning text-warning' : 'secondary text-secondary') }}">
                                                {{ ucfirst($newsletter->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $newsletter->scheduled_at ? $newsletter->scheduled_at->format('M d, Y H:i') : '-' }}</td>
                                        <td>{{ $newsletter->created_at->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <!-- View Button -->
                                                <a href="{{ route('admin.newsletters.show', $newsletter) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="View Details">
                                                    <i class="feather-eye"></i>
                                                </a>

                                                <!-- Edit Button -->
                                                <a href="{{ route('admin.newsletters.edit', $newsletter) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit Newsletter">
                                                    <i class="feather-edit-3"></i>
                                                </a>

                                                <!-- Send Button (Drafts Only) -->
                                                @if($newsletter->status === 'draft')
                                                    <form action="{{ route('admin.newsletters.send', $newsletter) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Send this newsletter now?')" title="Send Newsletter">
                                                            <i class="feather-send me-1"></i>Send
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Delete Button -->
                                                <form action="{{ route('admin.newsletters.destroy', $newsletter) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" onclick="return confirm('Are you sure you want to delete this newsletter?')" title="Delete Newsletter">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="feather-mail fs-3 d-block mb-2"></i> No newsletters found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($newsletters->hasPages())
                    <div class="card-footer">{{ $newsletters->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</main>

@include('admin.components.footer')