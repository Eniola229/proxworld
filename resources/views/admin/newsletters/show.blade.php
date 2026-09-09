@include('components.g-header')
@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Newsletter Preview</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.newsletters.index') }}">Newsletters</a></li>
                    <li class="breadcrumb-item">View</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto d-flex gap-2">
                <a href="{{ route('admin.newsletters.edit', $newsletter) }}" class="btn btn-primary">
                    <i class="feather-edit-3 me-2"></i>Edit Newsletter
                </a>
                <a href="{{ route('admin.newsletters.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left me-2"></i>Back to Newsletters
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="row">
                <!-- Left Column: HTML Email Body Preview -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $newsletter->subject }}</h5>
                            <span class="badge bg-soft-{{ $newsletter->status === 'sent' ? 'success text-success' : 'secondary text-secondary' }}">
                                {{ ucfirst($newsletter->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            @if($newsletter->featured_image_url)
                                <div class="mb-4 text-center">
                                    <img src="{{ $newsletter->featured_image_url }}" class="img-fluid rounded" alt="Featured Image" style="max-height: 350px;">
                                </div>
                            @endif

                            @if($newsletter->excerpt)
                                <div class="p-3 mb-4 bg-light rounded text-muted italic">
                                    <strong>Excerpt:</strong> {{ $newsletter->excerpt }}
                                </div>
                            @endif

                            <div class="newsletter-body p-3 border rounded bg-white">
                                {!! $newsletter->body !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Meta Details -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Details & Metadata</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Target Audience</span>
                                    <span class="fw-bold">{{ ucfirst($newsletter->audience) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Show on Blog</span>
                                    <span class="badge bg-soft-{{ $newsletter->show_on_blog ? 'success text-success' : 'danger text-danger' }}">
                                        {{ $newsletter->show_on_blog ? 'Yes' : 'No' }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Created Date</span>
                                    <span>{{ $newsletter->created_at->format('M d, Y H:i') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">Scheduled For</span>
                                    <span>{{ $newsletter->scheduled_at ? $newsletter->scheduled_at->format('M d, Y H:i') : 'N/A' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('admin.components.footer')