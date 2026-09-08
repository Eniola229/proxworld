@include('reseller.components.g-header')
@include('reseller.components.nav')

<main class="nxl-container">
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Panel Settings</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/manage/settings">Manage</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            @if(session('alert'))
                <div class="alert alert-{{ session('alert')['type'] }} alert-dismissible fade show" role="alert">
                    {{ session('alert')['message'] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title">Panel Configuration</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/manage/settings">
                                @csrf
                                
                                <div class="mb-3">
                                    <label class="form-label">Panel Name</label>
                                    <input type="text" name="panel_name" class="form-control @error('panel_name') is-invalid @enderror" 
                                           value="{{ old('panel_name', $reseller->panel_name) }}" required>
                                    @error('panel_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Primary Color</label>
                                    <input type="color" name="primary_color" class="form-control form-control-color w-100" 
                                           value="{{ old('primary_color', $reseller->primary_color) }}" required>
                                    @error('primary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Support Email</label>
                                    <input type="email" name="support_email" class="form-control @error('support_email') is-invalid @enderror" 
                                           value="{{ old('support_email', $reseller->support_email) }}">
                                    @error('support_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Default Markup Percentage (%)</label>
                                    <input type="number" name="default_markup_percent" step="0.01" class="form-control @error('default_markup_percent') is-invalid @enderror" 
                                           value="{{ old('default_markup_percent', $reseller->default_markup_percent) }}" required>
                                    @error('default_markup_percent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">This markup will be applied to all services by default.</div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary w-100">Save Settings</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include('reseller.components.g-footer')