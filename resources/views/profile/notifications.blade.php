    @include('components.g-header')
    @include('components.nav')

    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Notifications</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Notifications</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <!-- Mark All as Read Button -->
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form method="POST" action="{{ route('notifications.mark.read') }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-primary btn-sm">Mark All as Read</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="main-content">
                <div class="row">
                    <div class="col-12">
                        <div class="card stretch stretch-full">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Message</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(auth()->user()->notifications()->latest()->get() as $notification)
                                                <tr class="{{ $notification->read_at ? '' : 'bg-light' }}">
                                                    <td>
                                                        @if($notification->read_at)
                                                            <span class="badge bg-soft-secondary text-secondary">Read</span>
                                                        @else
                                                            <span class="badge bg-primary text-white">Unread</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <!-- Icon -->
                                                            <div class="avatar-text avatar-sm rounded {{ $notification->read_at ? 'bg-gray-200' : 'bg-primary text-white' }}">
                                                                @if(isset($notification->data['icon']))
                                                                    <i class="feather-{{ $notification->data['icon'] }}"></i>
                                                                @else
                                                                    <i class="feather-bell"></i>
                                                                @endif
                                                            </div>
                                                            
                                                            <div>
                                                                <!-- Link if available, else text -->
                                                                @if(isset($notification->data['link']))
                                                                    <a href="{{ $notification->data['link'] }}" class="fw-bold text-dark text-decoration-none">
                                                                        {{ $notification->data['message'] }}
                                                                    </a>
                                                                @else
                                                                    <span class="fw-bold text-dark">{{ $notification->data['message'] }}</span>
                                                                @endif
                                                                
                                                                <br>
                                                                <span class="fs-11 text-muted">
                                                                    ID: {{ substr($notification->id, 0, 8) }}...
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $notification->created_at->format('M d, Y - H:i') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-5">
                                                        <div class="text-muted">
                                                            <i class="feather-bell-off fs-3 mb-3 d-block"></i>
                                                            No notifications found.
                                                        </div>
                                                    </td>
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