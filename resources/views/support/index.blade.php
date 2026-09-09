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
                <div class="col-lg-6">
                    <div class="card stretch stretch-full text-center">
                        <div class="card-body py-5">
                            <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mx-auto mb-4" style="width:80px;height:80px;font-size:2.2rem;">
                                <i class="fab fa-telegram"></i>
                            </div>
                            <h4 class="fw-bold mb-2">Need Help?</h4>
                            <p class="text-muted mb-4 fs-14">
                                Our support team is available on Telegram — usually replies within minutes.
                            </p>
                            <a href="{{ config('services.telegram.support_url', env('SUPPORT_TELEGRAM_URL')) }}"
                               target="_blank" rel="noopener"
                               class="btn btn-primary btn-lg px-5">
                                <i class="fab fa-telegram me-2"></i> Chat with Support on Telegram
                            </a>
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