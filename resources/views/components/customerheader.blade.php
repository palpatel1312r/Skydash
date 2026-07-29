<!DOCTYPE html>
<html lang="en">

<head>
    <!-- SAME HEAD SECTION -->
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
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
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

            <!-- SAME NAVBAR RIGHTS -->
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
                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-toggle="dropdown"></a>
                    </li>
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            @php
                                $user = auth()->guard('customer')->user();
                                $bgColor = '#1cc88a'; // Customer Green
                                $initial = strtoupper(substr($user->name ?? 'U', 0, 1));
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
                            <a class="dropdown-item" href="{{ route('admin.password.form') }}">
                                <i class="ti-key text-primary"></i> Change Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="dropdown-item"
                                    style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                                    <i class="ti-power-off text-primary"></i> Logout
                                </button>
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

            {{-- ======================= CUSTOMER SIDEBAR ======================= --}}
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    {{-- 1. CUSTOMER DASHBOARD --}}
                    @php
                        $isCustomerDashboardActive = request()->routeIs('customer.dashboard');
                    @endphp
                    <li class="nav-item {{ $isCustomerDashboardActive ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('customer.dashboard') }}">
                            <i class="icon-grid menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>

                    {{-- 2. CUSTOMER INVOICES --}}
                    @php
                        $isCustomerInvoiceActive =
                            request()->routeIs('customer.invoices') ||
                            request()->routeIs('customer.invoices.create') ||
                            request()->routeIs('customer.invoices.edit');
                    @endphp
                    <li class="nav-item {{ $isCustomerInvoiceActive ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('customer.invoices') }}">
                            <i class="icon-file menu-icon"></i>
                            <span class="menu-title">Invoices</span>
                        </a>
                    </li>

                    {{-- 3. CUSTOMER PROFILE --}}
                    @php
                        $isCustomerProfileActive =
                            request()->routeIs('customer.profile') || request()->routeIs('customer.password.update');
                    @endphp
                    <li class="nav-item {{ $isCustomerProfileActive ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('customer.profile') }}">
                            <i class="mdi mdi-account-circle menu-icon"></i>
                            <span class="menu-title">Profile</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            @yield('content')
        </div>
    </div>

    <!-- plugins:js -->
    <script src="{{ asset('Dashboard/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('Dashboard/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('Dashboard/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('Dashboard/js/off-canvas.js') }}"></script>
    <script src="{{ asset('Dashboard/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('Dashboard/js/template.js') }}"></script>
    <script src="{{ asset('Dashboard/js/settings.js') }}"></script>
    <script src="{{ asset('Dashboard/js/todolist.js') }}"></script>
    <script src="{{ asset('Dashboard/js/Admin.Dashboard.js') }}"></script>
    <style>
        .navbar-toggler:focus,
        .navbar-toggler:active,
        .navbar-toggler:hover {
            outline: none !important;
            box-shadow: none !important;
        }
    </style>
</body>

</html>
