<nav class="nxl-navigation">
    <div class="navbar-wrapper">
    <div class="m-header" style="display:flex;align-items:center;height:64px;padding:0 1rem;">
        <a href="/dashboard" class="b-brand" style="display:flex;align-items:center;">
            @if($reseller->logo_path)
                <img src="{{ $reseller->logo_path }}"
                     alt="{{ $reseller->panel_name }}"
                     class="logo logo-lg"
                     style="max-width: 160px; height: 48px; width: auto; object-fit: contain; display: block;">
                <img src="{{ $reseller->logo_path }}"
                     alt="{{ $reseller->panel_name }}"
                     class="logo logo-sm"
                     style="width: 36px; height: 36px; object-fit: cover; border-radius: 6px; display: none;">
            @else
                <span class="logo logo-lg d-flex align-items-center justify-content-center fw-bold"
                      style="color: var(--reseller-primary); font-size: 1.2rem; letter-spacing: 1px;">
                    {{ $reseller->panel_name }}
                </span>
                <span class="logo logo-sm fw-bold fs-4 d-flex align-items-center justify-content-center"
                      style="width: 36px; height: 36px; color: var(--reseller-primary); display: none;">
                    {{ strtoupper(substr($reseller->panel_name, 0, 1)) }}
                </span>
            @endif 
        </a>
    </div>

        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption"><label>Menu</label></li>

                {{-- Dashboard --}}
                <li class="nxl-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="/dashboard" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                {{-- Orders --}}
                <li class="nxl-item nxl-hasmenu {{ request()->is('orders*') ? 'nxl-trigger' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                        <span class="nxl-mtext">Orders</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link {{ request()->is('orders/new') ? 'active' : '' }}"
                               href="/orders/new">New Order</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link {{ request()->is('orders') ? 'active' : '' }}"
                               href="/orders/r">Order History</a>
                        </li>
                    </ul>
                </li>

                {{-- Wallet --}}
                <li class="nxl-item nxl-hasmenu {{ request()->is('wallet*') ? 'nxl-trigger' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                        <span class="nxl-mtext">Wallet</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link {{ request()->is('wallet') ? 'active' : '' }}" 
                               href="/wallet">Add Funds</a>
                        </li>
                       <!--  <li class="nxl-item">
                            <a class="nxl-link {{ request()->is('wallet/transactions') ? 'active' : '' }}" 
                               href="/wallet/transactions">Transaction History</a>
                        </li> -->
                    </ul>
                </li>

                {{-- Panel Management — visible to the reseller owner only --}}
                @if(auth()->check() && auth()->id() === $reseller->owner_id)
                    <li class="nxl-item nxl-caption"><label>Management</label></li>
                    
                    <li class="nxl-item nxl-hasmenu {{ request()->is('manage*') ? 'nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-sliders"></i></span>
                            <span class="nxl-mtext">Panel Settings</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item">
                                <a class="nxl-link {{ request()->is('manage/settings') ? 'active' : '' }}"
                                   href="/manage/settings">
                                    <i class="feather-settings me-2"></i>Branding & Settings
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->is('manage/services') ? 'nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-tag"></i></span>
                            <span class="nxl-mtext">Services</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item">
                                <a class="nxl-link {{ request()->is('manage/services') ? 'active' : '' }}"
                                   href="/manage/services">
                                    <i class="feather-percent me-2"></i>Service Pricing
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->is('manage/customers') ? 'nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Customers</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item">
                                <a class="nxl-link {{ request()->is('manage/customers') ? 'active' : '' }}"
                                   href="/manage/customers">
                                    <i class="feather-list me-2"></i>Customer List
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->is('manage/revenue') ? 'nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                            <span class="nxl-mtext">Revenue</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item">
                                <a class="nxl-link {{ request()->is('manage/revenue') ? 'active' : '' }}"
                                   href="/manage/revenue">
                                    <i class="feather-dollar-sign me-2"></i>Revenue Summary
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nxl-item nxl-hasmenu {{ request()->is('manage/withdraw') ? 'nxl-trigger' : '' }}">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-arrow-down-circle"></i></span>
                            <span class="nxl-mtext">Withdraw Profit</span>
                            <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item">
                                <a class="nxl-link {{ request()->is('manage/withdraw') ? 'active' : '' }}"
                                   href="/manage/withdraw">
                                    <i class="feather-download me-2"></i>Withdraw
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <li class="nxl-item nxl-caption"><label>Account</label></li>

                {{-- Profile / Settings --}}
                <li class="nxl-item nxl-hasmenu {{ request()->is('profile*') ? 'nxl-trigger' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-user"></i></span>
                        <span class="nxl-mtext">Profile</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link {{ request()->is('profile') ? 'active' : '' }}" 
                               href="/profile">
                                <i class="feather-edit me-2"></i>Edit Profile
                            </a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link {{ request()->is('profile/password') ? 'active' : '' }}" 
                               href="/profile/password">
                                <i class="feather-lock me-2"></i>Change Password
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Logout --}}
                <li class="nxl-item">
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="nxl-link border-0 bg-transparent w-100 text-start">
                            <span class="nxl-micon"><i class="feather-power text-danger"></i></span>
                            <span class="nxl-mtext text-danger">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

{{-- Top header bar --}}
<header class="nxl-header">
    <div class="header-wrapper">
        <div class="header-left d-flex align-items-center gap-4">
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box"><div class="hamburger-inner"></div></div>
                </div>
            </a>
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">

                {{-- Search --}}
                <div class="dropdown nxl-h-item nxl-header-search">
                    <a href="javascript:void(0);" class="nxl-head-link me-0"
                       data-bs-toggle="dropdown" data-bs-auto-close="outside">
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

                {{-- Fullscreen --}}
                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0"
                           onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>

                {{-- Dark/Light --}}
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>

                {{-- Notifications --}}
                <div class="dropdown nxl-h-item">
                    <a class="nxl-head-link me-3" data-bs-toggle="dropdown"
                       href="#" role="button" data-bs-auto-close="outside">
                        <i class="feather-bell"></i>
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
                        @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                            <div class="notifications-item">
                                <div class="avatar-text avatar-md rounded bg-primary text-white me-3 border">
                                    <i class="feather-{{ $notification->data['icon'] ?? 'bell' }}"></i>
                                </div>
                                <div class="notifications-desc">
                                    @if(isset($notification->data['link']))
                                        <a href="{{ $notification->data['link'] }}" class="font-body text-truncate-2-line">
                                            {{ $notification->data['message'] }}
                                        </a>
                                    @else
                                        <span class="font-body text-truncate-2-line">
                                            {{ $notification->data['message'] }}
                                        </span>
                                    @endif
                                    <div class="notifications-date text-muted border-bottom border-bottom-dashed">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="feather-check-circle fs-3 mb-2 d-block"></i>
                                No new notifications
                            </div>
                        @endforelse
                        <div class="text-center notifications-footer">
                            <span class="fs-13 fw-semibold text-muted">{{ $reseller->panel_name }} Notifications</span>
                        </div>
                    </div>
                </div>

                {{-- User Profile Dropdown --}}
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown"
                       role="button" data-bs-auto-close="outside">
                        <img src="{{ asset('assets/images/profile.jpg') }}"
                             alt="profile" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/profile.jpg') }}"
                                     alt="profile" class="img-fluid user-avtar" />
                                <div>
                                    <h6 class="text-dark mb-0">{{ auth()->user()->name }}</h6>
                                    <span class="fs-12 fw-medium text-muted">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <a href="/profile" class="dropdown-item">
                                <i class="feather-user"></i>
                                <span>Profile Details</span>
                            </a>
                            <a href="/wallet" class="dropdown-item">
                                <i class="feather-credit-card"></i>
                                <span>Add Funds</span>
                            </a>
                            <a href="/wallet/transactions" class="dropdown-item">
                                <i class="feather-clock"></i>
                                <span>Transaction History</span>
                            </a>
                            @if(auth()->id() === $reseller->owner_id)
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('reseller.manage.settings') }}" class="dropdown-item">
                                    <i class="feather-settings"></i>
                                    <span>Panel Settings</span>
                                </a>
                                <a href="{{ route('reseller.manage.services') }}" class="dropdown-item">
                                    <i class="feather-tag"></i>
                                    <span>Service Pricing</span>
                                </a>
                                <a href="{{ route('reseller.manage.customers') }}" class="dropdown-item">
                                    <i class="feather-users"></i>
                                    <span>My Customers</span>
                                </a>
                                <a href="{{ route('reseller.manage.revenue') }}" class="dropdown-item">
                                    <i class="feather-bar-chart-2"></i>
                                    <span>Revenue Summary</span>
                                </a>
                            @endif
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="/logout">
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
    </div>
</header>
@include('components.flash-messages')