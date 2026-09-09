@include('components.g-header')

@include('admin.components.nav')
@include('admin.components.header')

<main class="nxl-container">
    <div class="nxl-content">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Create Newsletter</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.newsletters.index') }}">Newsletters</a></li>
                    <li class="breadcrumb-item">Create</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <a href="{{ route('admin.newsletters.index') }}" class="btn btn-secondary">
                    <i class="feather-arrow-left me-2"></i>Back to Newsletters
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="feather-alert-circle me-2"></i> Please check the form for errors.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.newsletters.store') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Left Column: Content -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Newsletter Content</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label required">Subject</label>
                                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" placeholder="Enter subject line..." required>
                                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Excerpt</label>
                                    <textarea name="excerpt" class="form-control @error('excerpt') is-invalid @enderror" rows="2" placeholder="Brief overview or preview snippet...">{{ old('excerpt') }}</textarea>
                                    @error('excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Body Content</label>
                                    <textarea id="tinymce-editor" name="body" class="form-control @error('body') is-invalid @enderror" style="min-height: 400px;">{{ old('body') }}</textarea>
                                    @error('body')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Settings & Actions -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Settings & Publishing</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label required">Target Audience</label>
                                    <select name="audience" class="form-select @error('audience') is-invalid @enderror" required>
                                        <option value="">Select Audience</option>
                                        @foreach($audiences as $key => $label)
                                            <option value="{{ $key }}" {{ old('audience') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('audience')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Featured Image URL</label>
                                    <input type="url" name="featured_image_url" class="form-control @error('featured_image_url') is-invalid @enderror" value="{{ old('featured_image_url') }}" placeholder="https://">
                                    @error('featured_image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Featured Video URL</label>
                                    <input type="url" name="featured_video_url" class="form-control @error('featured_video_url') is-invalid @enderror" value="{{ old('featured_video_url') }}" placeholder="https://">
                                    @error('featured_video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" name="show_on_blog" id="show_on_blog" value="1" {{ old('show_on_blog', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_on_blog">Show on Blog</label>
                                </div>

                                <hr class="my-4">

                                <div class="mb-3">
                                    <label class="form-label required">Action</label>
                                    <select name="action" id="action-select" class="form-select @error('action') is-invalid @enderror" required>
                                        <option value="draft" {{ old('action') == 'draft' ? 'selected' : '' }}>Save as Draft</option>
                                        <option value="send_now" {{ old('action') == 'send_now' ? 'selected' : '' }}>Send Now</option>
                                        <option value="schedule" {{ old('action') == 'schedule' ? 'selected' : '' }}>Schedule</option>
                                    </select>
                                </div>

                                <div class="mb-3 d-none" id="schedule-container">
                                    <label class="form-label">Scheduled Date/Time</label>
                                    <input type="datetime-local" name="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror" value="{{ old('scheduled_at') }}">
                                    @error('scheduled_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mt-2">
                                    <i class="feather-save me-1"></i> Save Newsletter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</main>

@include('admin.components.footer')

<!-- TinyMCE Script -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle Schedule Datepicker
        const actionSelect = document.getElementById('action-select');
        const scheduleContainer = document.getElementById('schedule-container');

        function toggleSchedule() {
            if (actionSelect.value === 'schedule') {
                scheduleContainer.classList.remove('d-none');
            } else {
                scheduleContainer.classList.add('d-none');
            }
        }
        actionSelect.addEventListener('change', toggleSchedule);
        toggleSchedule();

        // Initialize TinyMCE Editor
        tinymce.init({
            selector: '#tinymce-editor',
            height: 450,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
            toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image media link table | code preview',
            image_title: true,
            automatic_uploads: true,
            file_picker_types: 'image media',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px } img { max-width: 100%; height: auto; }',
            
            // Handle automatic Cloudinary file uploads for images & videos
            images_upload_handler: function (blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());

                    fetch("{{ route('admin.newsletters.media') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('HTTP Error: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && (data.location || data.url)) {
                            resolve(data.location || data.url);
                        } else {
                            reject('Invalid JSON response from server');
                        }
                    })
                    .catch(error => {
                        reject('Upload failed: ' + error.message);
                    });
                });
            }
        });
    });
</script>