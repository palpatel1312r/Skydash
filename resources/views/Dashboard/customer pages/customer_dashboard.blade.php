@extends('components.adminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">

            {{-- Success/Error Alerts --}}
            <div class="row">
                <div class="col-md-12 grid-margin">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 1. Welcome Hero Section --}}
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card bg-gradient-primary text-white rounded-lg shadow-lg" style="border: none;">
                        <div
                            class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center p-4">
                            <div class="mb-3 mb-md-0">
                                <h2 class="font-weight-bold mb-0 text-white">
                                    <i class="mdi mdi-account-circle me-2"></i>
                                    Welcome back, {{ $customer->fullname ?? 'Customer' }}!
                                </h2>
                                <p class="mt-2 mb-0 opacity-75" style="max-width: 500px;">
                                    Here is an overview of your account activity. Stay tuned for updates and new products!
                                </p>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-light text-dark p-2 px-3 rounded-pill font-weight-bold">
                                    <i class="mdi mdi-email-outline mr-1"></i> {{ $customer->email ?? 'No Email' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Stats Cards --}}
            <div class="row">
                {{-- Stat 1: Total Invoices --}}
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm rounded-lg">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title text-muted mb-0">Total Invoices</h6>
                                <div class="icon-box rounded-circle bg-primary text-white p-2">
                                    <i class="mdi mdi-file-document-box-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="font-weight-bold mb-0">0</h2>
                            <small class="text-muted">Invoices generated</small>
                            <div class="mt-3">
                                <a href="{{ route('customer.invoices') }}" class="btn btn-sm btn-outline-primary w-100">
                                    View All Invoices <i class="mdi mdi-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stat 2: Total Spent --}}
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm rounded-lg">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title text-muted mb-0">Total Spent</h6>
                                <div class="icon-box rounded-circle bg-success text-white p-2">
                                    <i class="mdi mdi-currency-inr mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="font-weight-bold mb-0 text-success">₹0.00</h2>
                            <small class="text-muted">Across all invoices</small>
                            <div class="mt-3">
                                <a href="{{ route('customer.invoices') }}" class="btn btn-sm btn-outline-success w-100">
                                    View Spending <i class="mdi mdi-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stat 3: Account Status --}}
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm rounded-lg">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title text-muted mb-0">Account Status</h6>
                                <div class="icon-box rounded-circle bg-warning text-white p-2">
                                    <i class="mdi mdi-account-check mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="font-weight-bold mb-0 text-warning">Active</h2>
                            <small class="text-muted">Your account is in good standing</small>
                            <div class="mt-3">
                                <a href="{{ route('customer.profile') }}" class="btn btn-sm btn-outline-warning w-100">
                                    Manage Profile <i class="mdi mdi-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Detailed Action Cards --}}
            <div class="row">
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm rounded-lg h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <i class="mdi mdi-shopping mdi-48px text-success mb-3"></i>
                                <h5 class="card-title">Explore Products</h5>
                                <p class="text-muted">Browse our latest collection of high-quality items.</p>
                            </div>
                            <a href="{{ route('customer.products') }}" class="btn btn-success btn-block mt-3">
                                <i class="mdi mdi-cart-outline mr-1"></i> Shop Now
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm rounded-lg h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <i class="mdi mdi-file-document-box mdi-48px text-primary mb-3"></i>
                                <h5 class="card-title">Billing History</h5>
                                <p class="text-muted">Track all your past invoices and payment records.</p>
                            </div>
                            <a href="{{ route('customer.invoices') }}" class="btn btn-primary btn-block mt-3">
                                <i class="mdi mdi-currency-inr mr-1"></i> View Invoices
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm rounded-lg h-100">
                        <div class="card-body text-center d-flex flex-column justify-content-between">
                            <div>
                                <i class="mdi mdi-account-settings mdi-48px text-warning mb-3"></i>
                                <h5 class="card-title">Manage Profile</h5>
                                <p class="text-muted">Update your personal details and contact information.</p>
                            </div>
                            <a href="{{ route('customer.profile') }}" class="btn btn-warning btn-block mt-3">
                                <i class="mdi mdi-account-edit mr-1"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        /* Custom styling for the icon boxes */
        .icon-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
        }

        /* Gradient background for hero */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #36b9cc 100%);
        }
    </style>
@endsection
