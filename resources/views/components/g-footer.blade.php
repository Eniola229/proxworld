<footer class="footer">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                    <span>Copyright ©</span>
                    <script>document.write(new Date().getFullYear());</script>
                    <span class="ms-1">ProxWorld. All rights reserved.</span>
                </p>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-md-end gap-3 mt-3 mt-md-0">
                    <a href="{{ route('faq') }}" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-help-circle me-1" style="font-size: 12px;"></i>
                        FAQ
                    </a>
                    <span class="text-muted">|</span>
                    <a href="{{ route('terms-of-use') }}" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-file-text me-1" style="font-size: 12px;"></i>
                        Terms
                    </a>
                    <span class="text-muted">|</span>
                    <a href="{{ route('refund-policy') }}" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-rotate-ccw me-1" style="font-size: 12px;"></i>
                        Refund Policy
                    </a>
                    @auth
                    <span class="text-muted">|</span>
                    <a href="{{ route('support.index') }}" class="fs-11 fw-semibold text-uppercase text-muted footer-link">
                        <i class="feather-headphones me-1" style="font-size: 12px;"></i>
                        Support
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Floating Support Button -->
<div class="support-float-btn" id="supportBtn">
    <i class="feather-headphones"></i>
    <span>Support</span>
</div>

<!-- Support Modal -->
<div class="support-modal" id="supportModal">
    <div class="support-modal-content">
        <div class="support-modal-header">
            <h5>Contact Support</h5>
            <button class="support-close" id="closeModal">&times;</button>
        </div>
        <div class="support-modal-body">
            <p class="text-muted mb-4">How would you like to chat with us?</p>
            <div class="support-options">
                <a href="{{ config('services.telegram.support_url', env('SUPPORT_TELEGRAM_URL')) }}" target="_blank" class="support-option">
                    <div class="support-option-icon whatsapp">
                        <i class="feather-message-square"></i>
                    </div>
                    <div class="support-option-content">
                        <h6>Telegram (Recommended) </h6>
                        <p>Chat with us on Telegram </p>
                    </div>
                </a>
                <a href="#" class="support-option" id="websiteSupport">
                    <div class="support-option-icon">
                        <i class="feather-message-circle"></i>
                    </div>
                    <div class="support-option-content">
                        <h6>Website Chat</h6>
                        <p>Chat with us on our website</p>
                    </div>
                </a>
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
    
    .footer-link:hover {
        color: var(--bs-primary) !important;
        transform: translateY(-1px);
    }
    
    .footer .copyright {
        line-height: 1.8;
    }
    
    /* Support Float Button */
    .support-float-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: var(--bs-primary, #0d6efd);
        color: white;
        padding: 12px 20px;
        border-radius: 50px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        z-index: 1000;
    }
    
    .support-float-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        background: var(--bs-primary-dark, #0b5ed7);
    }
    
    .support-float-btn i {
        font-size: 20px;
    }
    
    /* Support Modal */
    .support-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1050;
        animation: fadeIn 0.3s ease;
    }
    
    .support-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .support-modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 450px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        animation: slideUp 0.3s ease;
    }
    
    .support-modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .support-modal-header h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #212529;
    }
    
    .support-close {
        background: none;
        border: none;
        font-size: 28px;
        line-height: 1;
        color: #6c757d;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .support-close:hover {
        background: #f8f9fa;
        color: #212529;
    }
    
    .support-modal-body {
        padding: 24px;
    }
    
    .support-options {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .support-option {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        text-decoration: none;
        color: #212529;
        transition: all 0.3s ease;
    }
    
    .support-option:hover {
        border-color: var(--bs-primary, #0d6efd);
        background: #f8f9ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.1);
    }
    
    .support-option-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--bs-primary, #0d6efd);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    
    .support-option-icon.whatsapp {
        background: #25D366;
    }
    
    .support-option-content h6 {
        margin: 0 0 4px 0;
        font-size: 16px;
        font-weight: 600;
    }
    
    .support-option-content p {
        margin: 0;
        font-size: 13px;
        color: #6c757d;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @media (max-width: 767.98px) {
        .footer .row > div:first-child {
            text-align: center;
        }
        
        .footer .d-flex {
            justify-content: center !important;
            flex-wrap: wrap;
        }
        
        .support-float-btn {
            bottom: 20px;
            right: 20px;
            padding: 10px 16px;
            font-size: 13px;
        }
        
        .support-float-btn span {
            display: none;
        }
        
        .support-modal-content {
            width: 95%;
            margin: 0 10px;
        }
    }
</style>

<!-- JS Files -->
    <!--! BEGIN: Vendors JS !-->
    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>
    <!--! END: Vendors JS !-->
    
    <!--! BEGIN: Apps Init !-->
    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>
    <!--! END: Apps Init !-->
    
    <!--! BEGIN: Theme Customizer !-->
    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>
    <!--! END: Theme Customizer !-->
    
    <!-- Support Modal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const supportBtn = document.getElementById('supportBtn');
            const supportModal = document.getElementById('supportModal');
            const closeModal = document.getElementById('closeModal');
            const websiteSupport = document.getElementById('websiteSupport');
            
            // Open modal
            supportBtn.addEventListener('click', function() {
                supportModal.classList.add('active');
            });
            
            // Close modal
            closeModal.addEventListener('click', function() {
                supportModal.classList.remove('active');
            });
            
            // Close on outside click
            supportModal.addEventListener('click', function(e) {
                if (e.target === supportModal) {
                    supportModal.classList.remove('active');
                }
            });
            
            // Website support click handler
            websiteSupport.addEventListener('click', function(e) {
                e.preventDefault();
                @auth
                    window.location.href = "{{ route('support.index') }}";
                @endauth
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>