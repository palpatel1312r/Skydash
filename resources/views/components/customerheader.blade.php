<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Skydash Customer</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Dashboard/js/select.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('Dashboard/images/favicon.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container-scroller">
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">

            {{-- BRAND LOGO --}}
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                @php
                    $dashboardUrl = route('customer.dashboard');
                @endphp
                <a class="navbar-brand brand-logo mr-5" href="{{ $dashboardUrl }}">
                    <img src="{{ asset('Dashboard/images/logo.svg') }}" class="mr-2" alt="logo" />
                </a>
                <a class="navbar-brand brand-logo-mini" href="{{ $dashboardUrl }}">
                    <img src="{{ asset('Dashboard/images/logo-mini.svg') }}" alt="logo" />
                </a>
            </div>

            {{-- RIGHT MENU --}}
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="icon-menu"></span>
                </button>

                <ul class="navbar-nav mr-lg-2">
                    <li class="nav-item nav-search d-none d-lg-block">
                        <div class="input-group">
                            <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                                <span class="input-group-text" id="search"><i class="icon-search"></i></span>
                            </div>
                            <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now"
                                aria-label="search" aria-describedby="search">
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-nav-right">
                    {{-- NOTIFICATIONS --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-toggle="dropdown"></a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 border rounded bg-white text-dark"
                            href="{{ route('admin.cart.cart') }}" style="border-color: #dee2e6 !important;">
                            <div class="position-relative">
                                <i class="mdi mdi-cart-outline" style="font-size: 1.4rem;"></i>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark text-white"
                                    id="cartCount" style="font-size: 0.65rem; min-width: 18px; padding: 2px 5px;">
                                    {{ $cartCount ?? 0 }}
                                </span>
                            </div>
                            <span class="fw-medium">Cart</span>
                        </a>
                    </li>

                    {{-- PROFILE --}}
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            @php
                                $user = auth()->guard('customer')->user();
                                $bgColor = '#1cc88a';
                                $initial = strtoupper(substr($user->fullname ?? 'U', 0, 1));
                            @endphp
                            <div
                                style="width: 35px; height: 35px; border-radius: 50%; background-color: {{ $bgColor }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; text-transform: uppercase;">
                                {{ $initial }}
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="{{ route('customer.profile') }}">
                                <i class="ti-user text-primary"></i> Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('customer.password.form') }}">
                                <i class="ti-key text-primary"></i> Change Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#"
                                onclick="event.preventDefault(); 
                                         if(confirm('Are you sure you want to logout?')) {
                                             document.getElementById('logout-form-dropdown').submit();
                                         }">
                                <i class="ti-power-off text-primary"></i> Logout
                            </a>
                            <form id="logout-form-dropdown" action="{{ route('logout') }}" method="POST"
                                class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>

                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="icon-menu"></span>
                </button>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">
            <div class="theme-setting-wrapper">
                <div id="settings-trigger"><i class="ti-settings"></i></div>
                <div id="theme-settings" class="settings-panel">
                    <i class="settings-close ti-close"></i>
                    <p class="settings-heading">SIDEBAR SKINS</p>
                    <div class="sidebar-bg-options selected" id="sidebar-light-theme">
                        <div class="img-ss rounded-circle bg-light border mr-3"></div>Light
                    </div>
                    <div class="sidebar-bg-options" id="sidebar-dark-theme">
                        <div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark
                    </div>
                    <p class="settings-heading mt-2">HEADER SKINS</p>
                    <div class="color-tiles mx-0 px-4">
                        <div class="tiles success"></div>
                        <div class="tiles warning"></div>
                        <div class="tiles danger"></div>
                        <div class="tiles info"></div>
                        <div class="tiles dark"></div>
                        <div class="tiles default"></div>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR --}}
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('customer.dashboard') }}">
                            <i class="icon-grid menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('customer.invoices*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('customer.invoices') }}">
                            <i class="icon-file menu-icon"></i>
                            <span class="menu-title">Invoices</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('customer.profile') }}">
                            <i class="mdi mdi-account-circle menu-icon"></i>
                            <span class="menu-title">Profile</span>
                        </a>
                    </li>
                    <li class="nav-item mt-auto">
                        <a class="nav-link" href="#"
                            onclick="event.preventDefault(); 
                                     if(confirm('Are you sure you want to logout?')) {
                                         document.getElementById('logout-form').submit();
                                     }">
                            <i class="mdi mdi-logout menu-icon"></i>
                            <span class="menu-title">Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </nav>

            @yield('content')
        </div>
    </div>

    <script src="{{ asset('Dashboard/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('Dashboard/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('Dashboard/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('Dashboard/js/off-canvas.js') }}"></script>
    <script src="{{ asset('Dashboard/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('Dashboard/js/template.js') }}"></script>
    <script src="{{ asset('Dashboard/js/settings.js') }}"></script>
    <script src="{{ asset('Dashboard/js/todolist.js') }}"></script>
    <script src="{{ asset('Dashboard/js/Admin.Dashboard.js') }}"></script>
    @stack('scripts')
</body>

</html>
