<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
            <img src="{{ asset('assets/images/LOGO.png') }}"
                 alt=""
                 class="logo logo-lg"
                 style="width: 140px; height: auto; display: block; margin: 0 auto;" />


                <img src="{{ asset('assets/images/LOGO.png') }}" alt="" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>Menu</label>
                </li>
                
                <!-- Dashboard -->
                <li class="nxl-item">
                    <a href="{{ route('dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                <!-- Orders Menu -->
                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                        <span class="nxl-mtext">Orders</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="{{ route('order.create') }}">New Order</a></li>
                        <li class="nxl-item"><a class="nxl-link" href="{{ route('orders.index') }}">Order History</a></li>
                    </ul>
                </li>

                <!-- Wallet Menu -->
                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                        <span class="nxl-mtext">Wallet</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="{{ route('wallet.index') }}">Add Funds</a></li>
                    </ul>
                </li>

                <!--Api-->
                <li class="nxl-item">
                    <a href="{{ route('api.index') }}" class="nxl-link {{ request()->routeIs('api.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-code"></i></span>
                        <span class="nxl-mtext">API Access</span>
                    </a>
                </li>

                <!-- Reseller Panel -->
                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-globe"></i></span>
                        <span class="nxl-mtext">Reseller Panel</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link" href="{{ route('reseller-panel.index') }}">My Panel</a>
                        </li>
                        @php $hasPanel =\App\Models\Reseller::where('owner_id', auth()->id())->exists(); @endphp
                        @if(!$hasPanel)
                            <li class="nxl-item">
                                <a class="nxl-link" href="{{ route('reseller-panel.create') }}">Create Panel</a>
                            </li>
                        @endif

                        {{-- Reseller Services Link --}}
                        @if(auth()->check() && \App\Models\Reseller::where('owner_id', auth()->id())->where('status', 'active')->exists())
                        <li class="nxl-item">
                            <a href="{{ route('reseller-panel.services') }}" class="nxl-link {{ request()->routeIs('reseller-panel.services*') ? 'active' : '' }}">
                                <span class="nxl-micon"><i class="feather-tag"></i></span>
                                <span class="nxl-mtext">Service Pricing</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                <!-- Referer Menu -->
                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                       <span class="nxl-micon"><i class="feather-gift"></i></span>
                        <span class="nxl-mtext">Referral</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="{{ route('referral.index') }}">Referral Dashboard</a></li>
                        <li class="nxl-item"><a class="nxl-link" href="{{ route('referral.withdraw') }}">Withdraw Earnings</a></li>
                    </ul>
                </li>

                <!-- Support Menu -->
                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-life-buoy"></i></span>
                        <span class="nxl-mtext">Support</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="{{ route('support.index') }}">New Ticket</a></li>
                        
                    </ul>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Account</label>
                </li>

                <!-- Settings Menu -->
                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-settings"></i></span>
                        <span class="nxl-mtext">Settings</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="{{ route('profile.index') }}">Profile</a></li>
                        <li class="nxl-item"><a class="nxl-link" href="{{ route('profile.index') }}">Change Password</a></li>
                    </ul>
                </li>

                <!-- Logout -->
                <li class="nxl-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nxl-link border-0 bg-transparent w-100 text-start">
                            <span class="nxl-micon"><i class="feather-power text-danger"></i></span>
                            <span class="nxl-mtext text-danger">Logout</span>
                        </button>
                    </form>
                </li>

            </ul>
            
            <!-- Removed the generic Download Card to keep it clean for your specific system -->

        </div>
    </div>
</nav>

<header class="nxl-header">
    <div class="header-wrapper">
        <!--! [Start] Header Left (Mobile & Menu Toggles) !-->
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
        <!--! [End] Header Left !-->

        <!--! [Start] Header Right !-->
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                
                <!-- Search Orders (Simplified) -->
                <div class="dropdown nxl-h-item nxl-header-search">
                    <a href="javascript:void(0);" class="nxl-head-link me-0" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <i class="feather-search"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-search-dropdown">
                        <div class="input-group search-form">
                            <span class="input-group-text">
                                <i class="feather-search fs-6 text-muted"></i>
                            </span>
                            <input type="text" class="form-control search-input-field" placeholder="Search orders..." />
                            <span class="input-group-text">
                                <button type="button" class="btn-close"></button>
                            </span>
                        </div>
                    </div>
                </div>

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

                <!-- DYNAMIC NOTIFICATIONS (Header) -->
                <div class="dropdown nxl-h-item">
                    <a class="nxl-head-link me-3" data-bs-toggle="dropdown" href="#" role="button" data-bs-auto-close="outside">
                        <i class="feather-bell"></i>
                        
                        <!-- Dynamic Badge Count -->
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="badge bg-danger nxl-h-badge">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
                        <div class="d-flex justify-content-between align-items-center notifications-head">
                            <h6 class="fw-bold text-dark mb-0">Notifications</h6>
                        </div>
                        
                        <!-- Loop through Unread Notifications -->
                        @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                            <div class="notifications-item">
                                
                                <!-- Dynamic Icon: Checks if 'icon' exists in data, else default -->
                                <div class="avatar-text avatar-md rounded bg-primary text-white me-3 border">
                                    @if(isset($notification->data['icon']))
                                        <i class="feather-{{ $notification->data['icon'] }}"></i>
                                    @else
                                        <i class="feather-bell"></i>
                                    @endif
                                </div>
                                <div class="notifications-desc">
                                    <!-- Dynamic Message & Link -->
                                    @if(isset($notification->data['link']))
                                        <a href="{{ $notification->data['link'] }}" class="font-body text-truncate-2-line">
                                            {{ $notification->data['message'] }}
                                        </a>
                                    @else
                                        <span class="font-body text-truncate-2-line">
                                            {{ $notification->data['message'] }}
                                        </span>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="notifications-date text-muted border-bottom border-bottom-dashed">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <!-- Empty State -->
                            <div class="text-center py-4 text-muted">
                                <i class="feather-check-circle fs-3 mb-2 d-block"></i>
                                No new notifications
                            </div>
                        @endforelse

                        <!-- Footer Link to Settings -->
                        <div class="text-center notifications-footer">
                            <a href="{{ route('notifications.settings') }}" class="fs-13 fw-semibold text-dark">Notification Settings</a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="{{ asset('assets/images/profile.jpg') }}" alt="profile" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/profile.jpg') }}" alt="profile" class="img-fluid user-avtar" />
                                <div>
                                    <h6 class="text-dark mb-0">{{ auth()->user()->name }}</h6>
                                    <span class="fs-12 fw-medium text-muted">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('profile.index') }}" class="dropdown-item">
                                <i class="feather-user"></i>
                                <span>Profile Details</span>
                            </a>
                            <a href="{{ route('wallet.index') }}" class="dropdown-item">
                                <i class="feather-credit-card"></i>
                                <span>Billing & Payments</span>
                            </a>
                            <a href="{{ route('profile.index') }}" class="dropdown-item">
                                <i class="feather-settings"></i>
                                <span>Account Settings</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            
                            <!-- Laravel Logout Form -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item border-0 w-100 text-start">
                                    <i class="feather-log-out"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--! [End] Header Right !-->
    </div>
</header>

@include('components.flash-messages')