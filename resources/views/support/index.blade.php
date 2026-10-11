    @include('components.g-header')
    @include('components.nav')

    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Support</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Support</li>
                    </ul>
                </div>
            </div>

            <div class="main-content">
                <div class="row justify-content-center">
                    <div class="col-xl-8 col-lg-10">
                        <div class="card overflow-hidden">
                            <div id="supportPage" style="height: calc(100vh - 260px); min-height: 480px;"></div>
                        </div>
                        <p class="text-muted fs-12 mt-3 mb-0">
                            <i class="feather-info me-1"></i>
                            Replies from our team are also sent to your email.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new SupportChat(document.getElementById('supportPage'), {
            authed: true,
            csrf: @json(csrf_token()),
            loginUrl: @json(route('login')),
            openTicket: @json($openTicket),
            urls: {
                index:    @json(route('support.index')),
                store:    @json(route('support.store')),
                messages: @json(route('support.fetch-messages', '__ID__')),
                reply:    @json(route('support.reply', '__ID__')),
                rate:     @json(route('support.rate', '__ID__')),
            },
        }).init();
    });
</script>
@endpush

    @include('components.g-footer')
</body>
</html>