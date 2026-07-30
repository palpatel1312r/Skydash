<!DOCTYPE html>
<html lang="en">

<head>
    <!-- SAME HEAD SECTION AS YOUR EXISTING CODE -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Skydash Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Dashboard/js/select.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/css/vertical-layout-light/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('Dashboard/images/favicon.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link href="https://cdn.datatables.net/v/dt/dt-3.0.0/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-3.0.0/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <!-- Date Range Picker CSS & JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                @php
                    $dashboardUrl = route('admin.dashboard');
                @endphp
                <a class="navbar-brand brand-logo mr-5" href="{{ $dashboardUrl }}">
                    <img src="{{ asset('Dashboard/images/logo.svg') }}" class="mr-2" alt="logo" />
                </a>
                <a class="navbar-brand brand-logo-mini" href="{{ $dashboardUrl }}">
                    <img src="{{ asset('Dashboard/images/logo-mini.svg') }}" alt="logo" />
                </a>
            </div>

            <!-- SAME NAVBAR RIGHTS AS EXISTING CODE -->
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="icon-menu"></span>
                </button>
                <ul class="navbar-nav mr-lg-2">
                    <li class="nav-item nav-search d-none d-lg-block">
                        <div class="input-group">
                            <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                                <span class="input-group-text" id="search">
                                    <i class="icon-search"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now"
                                aria-label="search" aria-describedby="search">
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-toggle="dropdown">
                        </a>
                    </li>
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            @php
                                $user = auth()->guard('admin')->user();
                                $bgColor = '#4e73df';
                                $initial = strtoupper(substr($user->name ?? 'U', 0, 1));
                            @endphp
                            <div
                                style="width: 35px; height: 35px; border-radius: 50%; background-color: {{ $bgColor }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 18px; text-transform: uppercase;">
                                {{ $initial }}
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <i class="ti-user text-primary"></i> Profile
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.password.form') }}">
                                <i class="ti-key text-primary"></i> Change Password
                            </a>
                            <div class="dropdown-divider"></div>

                            {{-- ✅ FIXED: Pop-up confirmation now works --}}
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

            {{-- ======================= ADMIN SIDEBAR ======================= --}}
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    @php
                        $user = auth()->guard('admin')->user();
                    @endphp

                    {{-- 1. DASHBOARD --}}
                    @php
                        $isDashboardActive = request()->routeIs('admin.dashboard');
                    @endphp
                    <li class="nav-item {{ $isDashboardActive ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="icon-grid menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>

                    {{-- 2. CUSTOMERS --}}
                    @php
                        $isCustomerActive =
                            request()->routeIs('admin.customers.index') ||
                            request()->routeIs('admin.customers.create') ||
                            request()->routeIs('admin.customers.edit');
                    @endphp
                    <li class="nav-item {{ $isCustomerActive ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.customers.index') }}">
                            <i class="mdi mdi-account menu-icon"></i>
                            <span class="menu-title">Customers</span>
                        </a>
                    </li>

                    {{-- 3. USERS --}}
                    @php
                        $isUserActive =
                            request()->routeIs('admin.user.index') ||
                            request()->routeIs('admin.user.create') ||
                            request()->routeIs('admin.user.edit');
                    @endphp
                    <li class="nav-item {{ $isUserActive ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.user.index') }}">
                            <i class="mdi mdi-account-multiple menu-icon"></i>
                            <span class="menu-title">Users</span>
                        </a>
                    </li>

                    {{-- 4. PRODUCTS --}}
                    @php
                        $isProductActive =
                            request()->routeIs('products') ||
                            request()->routeIs('products.create') ||
                            request()->routeIs('products.edit') ||
                            request()->routeIs('products.add') ||
                            request()->routeIs('products.update');
                    @endphp
                    @if ($user->role_id == 1 || $user->role_id == 2)
                        <li class="nav-item {{ $isProductActive ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('products') }}">
                                <i class="icon-layout menu-icon"></i>
                                <span class="menu-title">Products</span>
                            </a>
                        </li>
                    @endif

                    {{-- 5. INVOICES --}}
                    @php
                        $isInvoiceActive =
                            request()->routeIs('invoices.index') ||
                            request()->routeIs('invoices.create') ||
                            request()->routeIs('invoices.edit') ||
                            request()->routeIs('admin.invoices.update');
                    @endphp
                    <li class="nav-item {{ $isInvoiceActive ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('invoices.index') }}">
                            <i class="icon-file menu-icon"></i>
                            <span class="menu-title">Invoices</span>
                        </a>
                    </li>

                    {{-- 6. PROFILE --}}
                    @php
                        $isProfileActive =
                            request()->routeIs('admin.profile') ||
                            request()->routeIs('admin.password.form') ||
                            request()->routeIs('admin.password.update');
                    @endphp
                    <li class="nav-item {{ $isProfileActive ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.profile') }}">
                            <i class="mdi mdi-account-circle menu-icon"></i>
                            <span class="menu-title">Profile</span>
                        </a>
                    </li>
                    {{-- 7. MANAGE ROLES --}}
                    @if ($user)
                        @php
                            $isRoleActive =
                                request()->routeIs('roles.index') ||
                                request()->routeIs('roles.create') ||
                                request()->routeIs('roles.edit');
                        @endphp
                        <li class="nav-item {{ $isRoleActive ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('roles.index') }}">
                                <i class="mdi mdi-account-multiple menu-icon"></i>
                                <span class="menu-title">Manage Roles</span>
                            </a>
                        </li>
                    @endif
                    {{-- LOGOUT BUTTON --}}
                    <li class="nav-item mt-auto" style="margin-top: auto;">
                        <a class="nav-link" href="#"
                            onclick="event.preventDefault(); 
                                     if(confirm('Are you sure you want to logout?')) {
                                         document.getElementById('logout-form').submit();
                                     }"
                            style="cursor: pointer;">
                            <i class="mdi mdi-logout menu-icon"></i>
                            <span class="menu-title" style="color: inherit;">Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
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
    <script src="{{ asset('Dashboard/js/Chart.roundedBarCharts.js') }}"></script>
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
