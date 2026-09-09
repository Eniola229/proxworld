<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand">
                <img src="{{ asset('assets/images/LOGO.png') }}"
                     alt=""
                     class="logo logo-lg"
                     style="width: 140px; height: auto; display: block; margin: 0 auto;" />
                <img src="{{ asset('assets/images/B.png') }}" alt="" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>Admin Panel</label>
                </li>

                <!-- Dashboard -->
                <li class="nxl-item">
                    <a href="{{ route('admin.dashboard') }}" class="nxl-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-home"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>
 
                <!-- Customers -->
                <li class="nxl-item">
                    <a href="{{ route('admin.customers.index') }}" class="nxl-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-users"></i></span>
                        <span class="nxl-mtext">Customers</span>
                    </a>
                </li>

                <!-- Orders -->
                <li class="nxl-item">
                    <a href="{{ route('admin.orders.index') }}" class="nxl-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-shopping-cart"></i></span>
                        <span class="nxl-mtext">Orders</span>
                    </a>
                </li>

                <!-- Wallet Transactions -->
                <li class="nxl-item">
                    <a href="{{ route('admin.wallet.index') }}" class="nxl-link {{ request()->routeIs('admin.wallet.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                        <span class="nxl-mtext">Wallet</span>
                    </a>
                </li>

                <!-- Reseller Panels -->
                <li class="nxl-item">
                    <a href="{{ route('admin.resellers.index') }}"
                       class="nxl-link {{ request()->routeIs('admin.resellers.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-layers"></i></span>
                        <span class="nxl-mtext">Reseller Panels</span>
                    </a>
                </li>

<li class="nxl-item">
                    <a href="{{ route('admin.reseller-withdrawals.index') }}"
                       class="nxl-link {{ request()->routeIs('admin.reseller-withdrawals.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-credit-card"></i></span>
                        <span class="nxl-mtext">Reseller Withdrawals</span>
                    </a>
                </li>


                <!-- Providers dropdown (Super Admin only) -->
                @if(auth('admin')->user()->isSuperAdmin())
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.providers.*') || request()->routeIs('admin.exchange-rates.*') ? 'nxl-trigger' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-server"></i></span>
                        <span class="nxl-mtext">Providers</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">

                        <!-- API Providers -->
                        <li class="nxl-item">
                            <a class="nxl-link {{ request()->routeIs('admin.providers.*') ? 'active' : '' }}"
                               href="{{ route('admin.providers.index') }}">
                                <i class="feather-server me-2" style="font-size:12px;"></i>
                                API Providers
                                @php $activeProviderCount = \App\Models\Provider::active()->count(); @endphp
                                @if($activeProviderCount > 0)
                                    <span class="badge bg-success ms-1" style="font-size:9px;">{{ $activeProviderCount }}</span>
                                @endif
                            </a>
                        </li>

                        <!-- Exchange Rates -->
                        <li class="nxl-item">
                            <a class="nxl-link {{ request()->routeIs('admin.exchange-rates.*') ? 'active' : '' }}"
                               href="{{ route('admin.exchange-rates.index') }}">
                                <i class="feather-dollar-sign me-2" style="font-size:12px;"></i>
                                Exchange Rates
                                @php
                                    $staleRateCount = \App\Models\ExchangeRate::where('fetched_at', '<', now()->subMinutes(30))->count();
                                @endphp
                                @if($staleRateCount > 0)
                                    <span class="badge bg-warning text-dark ms-1" style="font-size:9px;" title="{{ $staleRateCount }} stale">!</span>
                                @endif
                            </a>
                        </li>

                    </ul>
                </li>
                @endif

                <!-- Pricing Config (Super Admin only) -->
                @if(auth('admin')->user()->isSuperAdmin())
                <li class="nxl-item">
                    <a href="{{ route('admin.settings.pricing.index') }}"
                       class="nxl-link {{ request()->routeIs('admin.settings.pricing.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-percent"></i></span>
                        <span class="nxl-mtext">Pricing Config</span>
                    </a>
                </li>
                @endif

                <!-- Referral -->
                @if(auth('admin')->user()->canManageReferral())
                <li class="nxl-item">
                    <a href="{{ route('admin.referral.withdrawals.index') }}"
                       class="nxl-link {{ request()->routeIs('admin.referral.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-gift"></i></span>
                        <span class="nxl-mtext">Referral</span>
                    </a>
                </li>
                @endif

                <li class="nxl-item">
                    <a href="{{ route('admin.newsletters.index') }}" class="nxl-link {{ request()->routeIs('admin.newsletters.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-mail"></i></span>
                        <span class="nxl-mtext">Newsletters</span>
                    </a>
                </li>

                <!-- Support Tickets -->
                <li class="nxl-item">
                    <a href="{{ route('admin.support.index') }}"
                       class="nxl-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-life-buoy"></i></span>
                        <span class="nxl-mtext">Support</span>
                    </a>
                </li>

                <!-- Admins Management -->
                @if(auth('admin')->user()->canManageAdmins())
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.admins.*') || request()->routeIs('admin.roles.*') ? 'nxl-trigger' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-shield"></i></span>
                        <span class="nxl-mtext">Admins</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a class="nxl-link {{ request()->routeIs('admin.admins.index') ? 'active' : '' }}"
                               href="{{ route('admin.admins.index') }}">All Admins</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link {{ request()->routeIs('admin.admins.create') ? 'active' : '' }}"
                               href="{{ route('admin.admins.create') }}">Add New Admin</a>
                        </li>
                        <li class="nxl-item">
                            <a class="nxl-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                               href="{{ route('admin.roles.index') }}">Roles</a>
                        </li>
                    </ul>
                </li>
                @endif

                <li class="nxl-item nxl-caption">
                    <label>Account</label>
                </li>

                <!-- Settings -->
                <li class="nxl-item nxl-hasmenu {{ request()->routeIs('admin.profile.*') ? 'nxl-trigger' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-settings"></i></span>
                        <span class="nxl-mtext">Settings</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item">
                            <a href="{{ route('admin.profile.show') }}"
                               class="nxl-link {{ request()->routeIs('admin.profile.show') ? 'active' : '' }}">
                                My Profile
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Logout -->
                <li class="nxl-item">
                    <form method="POST" action="{{ route('admin.logout') }}">
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