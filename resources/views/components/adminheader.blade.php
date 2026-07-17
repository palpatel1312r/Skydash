<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Skydash Admin</title>

    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Material Design Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('Dashboard/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('Dashboard/js/select.dataTables.min.css') }}">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('Dashboard/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->

    <link rel="shortcut icon" href="{{ asset('Dashboard/images/favicon.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                {{-- ✅ 1. Determine the correct dashboard URL based on the logged-in user --}}
                @php
                    $dashboardUrl = '#';
                    if (auth()->guard('admin')->check()) {
                        $user = auth()->guard('admin')->user();
                        if ($user->role === 'Superadmin') {
                            $dashboardUrl = route('superadmin.dashboard');
                        } else {
                            $dashboardUrl = route('admin.dashboard');
                        }
                    } elseif (auth()->guard('customer')->check()) {
                        $dashboardUrl = route('customer.dashboard');
                    }
                @endphp

                {{-- ✅ 2. Main Logo (Large) --}}
                <a class="navbar-brand brand-logo mr-5" href="{{ $dashboardUrl }}">
                    <img src="{{ asset('Dashboard/images/logo.svg') }}" class="mr-2" alt="logo" />
                </a>

                {{-- ✅ 3. Mini Logo (Small) --}}
                <a class="navbar-brand brand-logo-mini" href="{{ $dashboardUrl }}">
                    <img src="{{ asset('Dashboard/images/logo-mini.svg') }}" alt="logo" />
                </a>
            </div>

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
                                // Determine which user is logged in
                                if (auth()->guard('admin')->check()) {
                                    $user = auth()->guard('admin')->user();
                                    $bgColor = '#4e73df'; // Admin Blue
                                } elseif (auth()->guard('customer')->check()) {
                                    $user = auth()->guard('customer')->user();
                                    $bgColor = '#1cc88a'; // Customer Green
                                } else {
                                    $user = null;
                                    $bgColor = '#6c757d'; // Default Gray
                                }
                                $initial = $user ? strtoupper(substr($user->name ?? 'U', 0, 1)) : 'U';
                            @endphp

                            <div
                                style="
                                width: 35px; 
                                height: 35px; 
                                border-radius: 50%; 
                                background-color: {{ $bgColor }}; 
                                color: white; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center; 
                                font-weight: bold; 
                                font-size: 18px;
                                text-transform: uppercase;
                            ">
                                {{ $initial }}
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            @if (auth()->guard('admin')->check())
                                <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                    <i class="ti-user text-primary"></i>
                                    Profile
                                </a>
                            @else
                                <a class="dropdown-item" href="{{ route('customer.profile') }}">
                                    <i class="ti-user text-primary"></i>
                                    Profile
                                </a>
                            @endif
                            <a class="dropdown-item" href="#" onclick="openChangePasswordModal()">
                                <i class="ti-key text-primary"></i>
                                Change Password
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
            <!-- partial:partials/_settings-panel.html -->
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

            <div id="right-sidebar" class="settings-panel">
                <!-- ... sidebar content ... -->
            </div>

            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    {{-- ✅ 1. ADMIN & SUPER ADMIN SHARED MENU --}}
                    @if (auth()->guard('admin')->check())
                        @php
                            $user = auth()->guard('admin')->user();
                        @endphp

                        {{-- ✅ DYNAMIC DASHBOARD LINK --}}
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ $user->role_id === 1 ? route('superadmin.dashboard') : route('admin.dashboard') }}">
                                <i class="icon-grid menu-icon"></i>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.customers.index') }}">
                                <i class="icon-columns menu-icon"></i>
                                <span class="menu-title">Customers</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.products.index') }}">
                                <i class="icon-layout menu-icon"></i>
                                <span class="menu-title">Products</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('invoices.index') }}">
                                <i class="icon-file menu-icon"></i>
                                <span class="menu-title">Invoices</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.profile') }}">
                                <i class="mdi mdi-account-circle menu-icon"></i>
                                <span class="menu-title">Profile</span>
                            </a>
                        </li>


                        @if ($user)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('superadmin.roles.index') }}">
                                    <i class="mdi mdi-account-multiple menu-icon"></i>
                                    <span class="menu-title">Manage Roles</span>
                                </a>
                            </li>
                        @endif

                        {{-- ✅ 2. CUSTOMER SIDEBAR --}}
                    @elseif (auth()->guard('customer')->check())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.dashboard') }}">
                                <i class="icon-grid menu-icon"></i>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.products') }}">
                                <i class="icon-layout menu-icon"></i>
                                <span class="menu-title">Products</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.invoices') }}">
                                <i class="icon-file menu-icon"></i>
                                <span class="menu-title">Invoices</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.profile') }}">
                                <i class="mdi mdi-account-circle menu-icon"></i>
                                <span class="menu-title">Profile</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <!-- Main Content -->
            @yield('content')

        </div> <!-- ✅ Close page-body-wrapper -->

    </div> <!-- ✅ Close container-scroller -->

    <!-- plugins:js -->
    <script src="{{ asset('Dashboard/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('Dashboard/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('Dashboard/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('Dashboard/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('Dashboard/js/dataTables.select.min.js') }}"></script>

    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('Dashboard/js/off-canvas.js') }}"></script>
    <script src="{{ asset('Dashboard/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('Dashboard/js/template.js') }}"></script>
    <script src="{{ asset('Dashboard/js/settings.js') }}"></script>
    <script src="{{ asset('Dashboard/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ asset('Dashboard/js/dashboard.js') }}"></script>
    <script src="{{ asset('Dashboard/js/Chart.roundedBarCharts.js') }}"></script>

    <script>
        function openChangePasswordModal() {
            const user = @json(auth()->guard('admin')->check() ? 'admin' : (auth()->guard('customer')->check() ? 'customer' : null));

            if (!user) return;

            // Set the correct form action URL based on user type
            const form = document.getElementById('changePasswordForm');
            if (user === 'admin') {
                form.action = "{{ route('admin.password.update') }}";
            } else if (user === 'customer') {
                form.action = "{{ route('customer.password.update') }}";
            }

            // Clear previous inputs
            form.reset();

            // Show the modal
            $('#changePasswordModal').modal('show');
        }
    </script>
    <!-- End custom js for this page-->

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti-lock text-primary"></i> Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="changePasswordForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" name="current_password" id="current_password"
                                class="form-control" required placeholder="Enter current password">
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" name="new_password" id="new_password" class="form-control"
                                required placeholder="Enter new password (min 4 characters)">
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                class="form-control" required placeholder="Confirm new password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
