<header class="nxl-header">
    <div class="header-wrapper">
        <!--! [Start] Header Left -->
        <div class="header-left d-flex align-items-center gap-4">
            <!-- Mobile Toggle -->
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <!-- Navigation Toggle -->
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
        </div>
        <!--! [End] Header Left -->

        <!--! [Start] Header Right -->
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                
                <!-- Fullscreen Toggle -->
                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>

                <!-- Dark/Light Theme Toggle -->
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>

                <!-- Admin Profile Dropdown -->
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="{{ asset('assets/images/profile.jpg') }}" alt="profile" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/profile.jpg') }}" alt="profile" class="img-fluid user-avtar" />
                                <div>
                                    <h6 class="text-dark mb-0">{{ auth('admin')->user()->name }}</h6>
                                    <span class="fs-12 fw-medium text-muted">{{ auth('admin')->user()->email }}</span>
                                    <div class="mt-1">
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ ucwords(str_replace('_', ' ', auth('admin')->user()->role)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                <i class="feather-home"></i>
                                <span>Dashboard</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            
                            <!-- Laravel Logout Form -->
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item border-0 w-100 text-start text-danger">
                                    <i class="feather-log-out"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--! [End] Header Right -->
    </div>
</header>