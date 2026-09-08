<footer class="footer">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                    <span>Copyright ©</span>
                    <script>document.write(new Date().getFullYear());</script>
                    <span class="ms-1">{{ $reseller->panel_name }}. All rights reserved.</span>
                </p>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-md-end gap-3 mt-3 mt-md-0">
                    @auth
                        <a href="/wallet" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                            <i class="feather-credit-card me-1" style="font-size: 12px;"></i>
                            Add Funds
                        </a>
                        <span class="text-muted">|</span>
                        <a href="/orders/new" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                            <i class="feather-plus-circle me-1" style="font-size: 12px;"></i>
                            New Order
                        </a>
                    @endauth
                    @if($reseller->support_email)
                        <span class="text-muted">|</span>
                        <a href="mailto:{{ $reseller->support_email }}"
                           class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                            <i class="feather-headphones me-1" style="font-size: 12px;"></i>
                            Support
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- Floating support button — uses reseller support email, not your platform --}}
<div class="support-float-btn" id="supportBtn">
    <i class="feather-headphones"></i>
    <span>Support</span>
</div>

{{-- Support Modal --}}
<div class="support-modal" id="supportModal">
    <div class="support-modal-content">
        <div class="support-modal-header">
            <h5>Contact {{ $reseller->panel_name }} Support</h5>
            <button class="support-close" id="closeModal">&times;</button>
        </div>
        <div class="support-modal-body">
            <p class="text-muted mb-4">How would you like to reach us?</p>
            <div class="support-options">
                @if($reseller->support_email)
                    <a href="mailto:{{ $reseller->support_email }}" class="support-option">
                        <div class="support-option-icon" style="background: var(--reseller-primary);">
                            <i class="feather-mail"></i>
                        </div>
                        <div class="support-option-content">
                            <h6>Email Support</h6>
                            <p>{{ $reseller->support_email }}</p>
                        </div>
                    </a>
                @endif
                {{-- Reseller can add more channels via settings in the future --}}
                <div class="support-option" style="opacity:.5; cursor:default;">
                    <div class="support-option-icon" style="background: #6c757d;">
                        <i class="feather-message-circle"></i>
                    </div>
                    <div class="support-option-content">
                        <h6>Live Chat</h6>
                        <p>Coming soon</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .footer {
        padding: 1.5rem 0;
        border-top: 1px solid rgba(0,0,0,0.1);
        background-color: #fff;
        margin-top: auto;
    }
    .footer-link {
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }
    .footer-link:hover { color: var(--reseller-primary) !important; transform: translateY(-1px); }
    .footer .copyright { line-height: 1.8; }

    .support-float-btn {
        position: fixed; bottom: 30px; right: 30px;
        background: var(--reseller-primary);
        color: white; padding: 12px 20px;
        border-radius: 50px; cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 600;
        transition: all 0.3s ease; z-index: 1000;
    }
    .support-float-btn:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
    .support-float-btn i { font-size: 20px; }

    .support-modal {
        display: none; position: fixed; top:0; left:0;
        width:100%; height:100%;
        background: rgba(0,0,0,0.5); z-index:1050;
        animation: fadeIn 0.3s ease;
    }
    .support-modal.active { display: flex; align-items: center; justify-content: center; }
    .support-modal-content {
        background: white; border-radius: 12px;
        width: 90%; max-width: 450px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        animation: slideUp 0.3s ease;
    }
    .support-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e9ecef;
        display: flex; justify-content: space-between; align-items: center;
    }
    .support-modal-header h5 { margin:0; font-size:18px; font-weight:600; color:#212529; }
    .support-close {
        background:none; border:none; font-size:28px; line-height:1;
        color:#6c757d; cursor:pointer; padding:0;
        width:30px; height:30px;
        display:flex; align-items:center; justify-content:center;
        border-radius:4px; transition:all .2s ease;
    }
    .support-close:hover { background:#f8f9fa; color:#212529; }
    .support-modal-body { padding: 24px; }
    .support-options { display:flex; flex-direction:column; gap:12px; }
    .support-option {
        display:flex; align-items:center; gap:16px;
        padding:16px; border:2px solid #e9ecef;
        border-radius:8px; text-decoration:none;
        color:#212529; transition:all .3s ease;
    }
    .support-option:hover {
        border-color: var(--reseller-primary);
        background:#f8f9ff; transform:translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .support-option-icon {
        width:48px; height:48px; border-radius:50%;
        color:white; display:flex; align-items:center;
        justify-content:center; font-size:22px; flex-shrink:0;
    }
    .support-option-content h6 { margin:0 0 4px; font-size:16px; font-weight:600; }
    .support-option-content p  { margin:0; font-size:13px; color:#6c757d; }
    @keyframes fadeIn  { from{opacity:0} to{opacity:1} }
    @keyframes slideUp { from{transform:translateY(30px);opacity:0} to{transform:translateY(0);opacity:1} }

    @media (max-width: 767.98px) {
        .footer .row > div:first-child { text-align:center; }
        .footer .d-flex { justify-content:center !important; flex-wrap:wrap; }
        .support-float-btn { bottom:20px; right:20px; padding:10px 16px; font-size:13px; }
        .support-float-btn span { display:none; }
        .support-modal-content { width:95%; margin:0 10px; }
    }
</style>

{{-- Same JS stack as your main footer --}}
<script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('assets/js/common-init.min.js') }}"></script>
<script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>
<script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn   = document.getElementById('supportBtn');
        const modal = document.getElementById('supportModal');
        const close = document.getElementById('closeModal');

        btn.addEventListener('click',  () => modal.classList.add('active'));
        close.addEventListener('click',() => modal.classList.remove('active'));
        modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('active'); });
    });
</script>

@stack('scripts')
</body>
</html>