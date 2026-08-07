@extends('Components.customerheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">

            {{-- Alerts --}}
            <div class="row">
                <div class="col-md-12 grid-margin">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 1. Welcome Hero Section --}}
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card hero-card rounded-lg shadow-lg overflow-hidden">
                        <div
                            class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center p-4">
                            <div class="mb-3 mb-md-0">
                                <h2 class="font-weight-bold mb-2 text-white">
                                    <i class="mdi mdi-account-circle-outline me-2"></i>
                                    Welcome back, {{ $customer->fullname ?? 'Valued Customer' }}!
                                </h2>
                                <p class="mb-2 opacity-85" style="max-width: 520px;">
                                    Your personalized dashboard gives you quick access to invoices, account status, and the
                                    latest updates.
                                </p>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge badge-light text-dark p-2 px-3 rounded-pill">
                                        <i class="mdi mdi-email-outline me-1"></i>
                                        {{ $customer->email ?? 'No Email' }}
                                    </span>
                                    <span class="badge badge-light text-dark p-2 px-3 rounded-pill">
                                        <i class="mdi mdi-calendar-clock me-1"></i>
                                        Member since
                                        {{ optional($customer)->created_at ? $customer->created_at->format('M Y') : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-md-right mt-3 mt-md-0">
                                <div class="hero-stat p-3 rounded-lg bg-white bg-opacity-10">
                                    <h3 class="mb-1 text-white">Estimated Spend</h3>
                                    <p class="mb-0 text-white-75">₹{{ number_format($totalSpent ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FILTERS + SEARCH + CREATE BUTTON ROW --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div
                        class="d-flex flex-column flex-sm-row flex-wrap align-items-start align-items-sm-center justify-content-between gap-2 gap-sm-3">

                        {{-- LEFT: Filters --}}
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="text-muted fw-bold small me-1 d-none d-sm-inline">Filter By:</span>

                            {{-- Category Filter --}}
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm shadow-sm rounded-pill px-3 dropdown-toggle"
                                    type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-tag-outline me-1"></i>
                                    <span id="categoryLabel">
                                        @if (isset($request) && $request->has('category'))
                                            {{ $request->category }}
                                        @else
                                            All Categories
                                        @endif
                                    </span>
                                </button>
                                <ul class="dropdown-menu shadow-sm" aria-labelledby="categoryDropdown"
                                    style="min-width: 200px; max-height: 300px; overflow-y: auto;">
                                    <li><a class="dropdown-item category-option" href="#" data-cat="">All
                                            Categories</a></li>
                                    @foreach ($categories as $category)
                                        <li><a class="dropdown-item category-option" href="#"
                                                data-cat="{{ $category }}">{{ $category }}</a></li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Clear Filters Button --}}
                            <a href="{{ route('customer.dashboard') }}"
                                class="btn btn-sm shadow-sm rounded-pill px-3 
   {{ request()->has('category') || request()->has('search') ? 'btn-outline-danger' : 'btn-outline-dark' }}">
                                <i class="mdi mdi-close me-1"></i> <span class="d-none d-sm-inline">Clear</span>
                            </a>
                        </div>

                        {{-- RIGHT: Search  --}}
                        <div class="d-flex align-items-center gap-2">
                            {{-- SEARCH INPUT --}}
                            <div class="input-group input-group-sm" style="max-width: 220px;">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="mdi mdi-magnify text-muted"></i>
                                </span>
                                <input id="productSearch" class="form-control bg-light border-start-0"
                                    placeholder="Search products..." value="{{ request('search') ?? '' }}">
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Products Section --}}
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge badge-primary p-2 rounded-circle">
                                        <i class="mdi mdi-star text-white"></i>
                                    </span>
                                    <h4 class="card-title mb-0 ms-2 fw-bold">Our Products</h4>
                                    <span class="badge bg-light text-muted ms-2">{{ count($products ?? []) }} items</span>
                                </div>
                            </div>

                            {{-- PRODUCT GRID - 4 columns on desktop --}}
                            <div class="row g-3" id="productGrid">
                                @forelse($products ?? [] as $product)
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 product-item"
                                        data-title="{{ strtolower($product->title) }}"
                                        data-category="{{ strtolower($product->category ?? '') }}"
                                        data-price="{{ $product->price }}">
                                        <div class="amazon-card h-100">
                                            {{-- Image --}}
                                            <div class="img-wrapper position-relative">
                                                @if ($product->image)
                                                    <img src="{{ asset($product->image) }}" alt="{{ $product->title }}"
                                                        class="product-img"
                                                        onclick="openProductDetailModal(
        '{{ addslashes($product->title) }}',
        '{{ addslashes($product->description ?? 'No description available.') }}',
        '{{ $product->price }}',
        '{{ $product->category }}',
        '{{ $product->quantity }}',
        '{{ $product->image }}',
        '{{ $product->id }}'  
    )"
                                                        style="cursor: pointer;">
                                                @else
                                                    <div
                                                        class="bg-light d-flex align-items-center justify-content-center text-muted no-image">
                                                        <div class="text-center">
                                                            <i class="mdi mdi-image-off" style="font-size: 32px;"></i>
                                                            <div class="small mt-1">No Image</div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (($product->quantity ?? 0) <= 5 && ($product->quantity ?? 0) > 0)
                                                    <span
                                                        class="badge bg-warning text-dark position-absolute top-0 start-0 m-2 rounded-pill px-2">
                                                        Low Stock
                                                    </span>
                                                @elseif(($product->quantity ?? 0) <= 0)
                                                    <span
                                                        class="badge bg-danger position-absolute top-0 start-0 m-2 rounded-pill px-2">
                                                        Out of Stock
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Details --}}
                                            <div class="card-details">
                                                @if ($product->category ?? false)
                                                    <span class="badge bg-light text-muted border rounded-pill px-2 mb-1"
                                                        style="font-size: 11px;">
                                                        {{ $product->category }}
                                                    </span>
                                                @endif

                                                <h6 class="product-title mb-1" title="{{ $product->title }}">
                                                    {{ $product->title }}
                                                </h6>

                                                <div class="price-box mb-2">
                                                    <span class="current-price">
                                                        <span
                                                            class="currency">₹</span>{{ number_format($product->price, 0) }}
                                                    </span>
                                                    <span class="mrp text-muted">
                                                        <del>₹{{ number_format($product->price * 1.4, 0) }}</del>
                                                    </span>
                                                </div>

                                                <div class="mb-2">
                                                    @if (($product->quantity ?? 0) > 0)
                                                        <small class="text-success">
                                                            <i class="mdi mdi-check-circle me-1"></i>
                                                            {{ $product->quantity }} in stock
                                                        </small>
                                                    @else
                                                        <small class="text-danger">
                                                            <i class="mdi mdi-close-circle me-1"></i>
                                                            Currently unavailable
                                                        </small>
                                                    @endif
                                                </div>

                                                {{-- Card Actions Container --}}
                                                <div class="mt-auto card-actions-wrapper"
                                                    id="card-actions-{{ $product->id }}">
                                                    {{-- Actions (visible by default) --}}
                                                    <div class="card-actions d-flex flex-column gap-2 w-100">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <input type="number" id="qty_{{ $product->id }}"
                                                                value="1" min="1"
                                                                max="{{ $product->quantity ?? 99 }}"
                                                                class="form-control form-control-sm qty-input"
                                                                {{ ($product->quantity ?? 0) <= 0 ? 'disabled' : '' }}>

                                                            <button
                                                                class="btn btn-primary btn-sm flex-grow-1 add-to-cart-btn"
                                                                data-product-id="{{ $product->id }}"
                                                                {{ ($product->quantity ?? 0) <= 0 ? 'disabled' : '' }}>
                                                                <i class="mdi mdi-cart-outline me-1"></i> Add
                                                            </button>
                                                        </div>

                                                        <button class="btn btn-success btn-sm w-100 buy-now-btn"
                                                            data-product-id="{{ $product->id }}"
                                                            {{ ($product->quantity ?? 0) <= 0 ? 'disabled' : '' }}>
                                                            <i class="mdi mdi-flash me-1"></i> Buy Now
                                                        </button>
                                                    </div>

                                                    {{-- Success Message (hidden by default) --}}
                                                    {{-- <div class="added-success-message text-center py-2"
                                                        style="display: none;">
                                                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                                                            <i class="mdi mdi-check-circle" style="font-size: 24px;"></i>
                                                            <p class="mb-0 fw-bold" style="font-size: 0.85rem;">Added to
                                                                Cart!</p>
                                                            <small class="text-muted">Item has been added
                                                                successfully</small>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5 text-muted" id="noProductsMsg">
                                        <i class="mdi mdi-package-variant-closed" style="font-size: 48px;"></i>
                                        <p class="mt-2 mb-0">No products available at the moment.</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- No results message (hidden by default) --}}
                            <div id="noSearchResults" class="col-12 text-center py-5 text-muted" style="display: none;">
                                <i class="mdi mdi-magnify" style="font-size: 48px;"></i>
                                <p class="mt-2 mb-0">No products found matching your search.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Stats Cards --}}
            <div class="row g-4 mb-4">
                {{-- Total Invoices --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 stats-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <p class="text-muted small mb-1 fw-medium">Total Invoices</p>
                                    <h6 class="text-muted mb-0" style="font-size: 0.8rem;">All time generated</h6>
                                </div>
                                <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="mdi mdi-file-document-box-outline"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1">{{ $invoicesCount ?? 0 }}</h2>
                            <p class="text-muted small mb-3">Invoices created for your account</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                                    <i class="mdi mdi-file-check me-1"></i> Complete history
                                </span>
                                <a href="{{ route('customer.invoices') }}"
                                    class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    View All <i class="mdi mdi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Total Spent --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 stats-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <p class="text-muted small mb-1 fw-medium">Total Spent</p>
                                    <h6 class="text-muted mb-0" style="font-size: 0.8rem;">Across all invoices</h6>
                                </div>
                                <div class="stats-icon bg-success bg-opacity-10 text-success">
                                    <i class="mdi mdi-cash-multiple"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1 text-success">₹{{ number_format($totalSpent ?? 0, 2) }}</h2>
                            <p class="text-muted small mb-3">Lifetime payments & purchases</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                    <i class="mdi mdi-trending-up me-1"></i> All payments
                                </span>
                                <a href="{{ route('customer.invoices') }}"
                                    class="btn btn-sm btn-outline-success rounded-pill px-3">
                                    View Spending <i class="mdi mdi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Account Status --}}
                <div class="col-md-4">
                    @php
                        $status = strtolower($accountStatus ?? 'active');
                        $statusConfig = [
                            'active' => [
                                'class' => 'success',
                                'icon' => 'mdi-check-circle',
                                'text' => 'Your account is in good standing',
                            ],
                            'pending' => [
                                'class' => 'warning',
                                'icon' => 'mdi-clock-outline',
                                'text' => 'Account verification is pending',
                            ],
                            'inactive' => [
                                'class' => 'danger',
                                'icon' => 'mdi-alert-circle',
                                'text' => 'Account is currently inactive',
                            ],
                            'suspended' => [
                                'class' => 'danger',
                                'icon' => 'mdi-block-helper',
                                'text' => 'Account has been suspended',
                            ],
                        ];
                        $config = $statusConfig[$status] ?? $statusConfig['active'];
                    @endphp
                    <div class="card border-0 shadow-sm h-100 stats-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <p class="text-muted small mb-1 fw-medium">Account Status</p>
                                    <h6 class="text-muted mb-0" style="font-size: 0.8rem;">Current account state</h6>
                                </div>
                                <div
                                    class="stats-icon bg-{{ $config['class'] }} bg-opacity-10 text-{{ $config['class'] }}">
                                    <i class="mdi {{ $config['icon'] }}"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1 text-{{ $config['class'] }}">{{ ucfirst($status) }}</h2>
                            <p class="text-muted small mb-3">{{ $config['text'] }}</p>
                            <div class="d-flex align-items-center justify-content-between">
                                <span
                                    class="badge bg-{{ $config['class'] }} bg-opacity-10 text-{{ $config['class'] }} rounded-pill px-3 py-1">
                                    <i class="mdi {{ $config['icon'] }} me-1"></i> {{ ucfirst($status) }}
                                </span>
                                <a href="{{ route('customer.profile') }}"
                                    class="btn btn-sm btn-outline-{{ $config['class'] }} rounded-pill px-3">
                                    Manage Profile <i class="mdi mdi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Quick Actions & Account Summary --}}
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm rounded-lg h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title mb-3">Quick Actions</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('customer.invoices') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="mdi mdi-file-document-outline me-1"></i> Invoices
                                    </a>
                                    <a href="{{ route('customer.profile') }}" class="btn btn-outline-warning btn-sm">
                                        <i class="mdi mdi-account-edit me-1"></i> Profile
                                    </a>
                                </div>
                            </div>
                            <div class="mt-4">
                                <p class="text-muted mb-1">Need help?</p>
                                <a href="{{ route('customer.profile') }}" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-lifebuoy me-1"></i> Account Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm rounded-lg h-100">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Account Summary</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Invoices due</span>
                                <strong>{{ $dueInvoices ?? 0 }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Overdue amount</span>
                                <strong>₹{{ number_format($overdueAmount ?? 0, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Open requests</span>
                                <strong>{{ $openRequests ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- 1. CART NOTIFICATION MODAL --}}
    <div class="modal fade" id="cartNotificationModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 18px;">
                {{-- Colored top bar --}}
                <div id="notifTopBar" style="height: 6px; background: #0d6efd;"></div>
                <div class="modal-body text-center p-4 pt-4">
                    <div id="notifIconWrapper" class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width: 78px; height: 78px; border-radius: 50%; background: #e7f1ff;">
                        <div id="notifIcon" style="font-size: 36px; line-height: 1;"></div>
                    </div>
                    <h4 id="notifTitle" class="fw-bold mb-2" style="font-size: 1.35rem;">Notification</h4>
                    <p id="notifMessage" class="text-muted mb-1 px-2" style="font-size: 0.95rem; line-height: 1.5;">
                        Message goes here
                    </p>
                    <p id="notifDetail" class="small text-muted mb-4" style="display: none;"></p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary rounded-pill py-2 fw-medium" id="notifOkBtn">
                            <i class="mdi mdi-check me-1"></i> Got it
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. PRODUCT DETAIL MODAL (No Edit Button) --}}
    <div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 750px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-bottom bg-white px-4 py-3">
                    <h5 class="modal-title fw-bold text-truncate" id="pdTitle" style="max-width: 85%;">Product Title
                    </h5>
                    {{-- X button with Bootstrap 5 data-bs-dismiss --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        {{-- Left: Main Image --}}
                        <div class="col-md-5">
                            <div class="main-image-wrapper bg-light rounded-3 d-flex align-items-center justify-content-center p-3 border"
                                style="height: 280px;">
                                <img id="pdMainImage" src="" alt="Product Image" class="img-fluid"
                                    style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            </div>
                        </div>

                        {{-- Right: Details --}}
                        <div class="col-md-7">
                            <h3 class="fw-bold mb-2 text-dark" id="pdName">Product Name</h3>

                            <div class="d-flex align-items-baseline gap-3 mb-3">
                                <h4 class="text-primary fw-bold mb-0" id="pdPrice">₹0.00</h4>
                                <span class="text-danger text-decoration-line-through small" id="pdMrp">₹0.00</span>
                            </div>

                            <div class="mb-3 d-flex flex-wrap gap-2">
                                <span
                                    class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2"
                                    id="pdStock">
                                    <i class="mdi mdi-check-circle me-1"></i> In Stock
                                </span>
                                <span
                                    class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3 py-2"
                                    id="pdCategory">
                                    Category
                                </span>
                            </div>

                            <p class="text-muted small mb-3" id="pdDesc" style="line-height: 1.6;">
                                Product description will appear here...
                            </p>

                            {{-- Add to Cart & Buy Now - Using data attributes from modal --}}
                            <div class="mt-3 d-flex flex-column gap-2 w-100">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" id="modal_qty" value="1" min="1" max="99"
                                        class="form-control form-control-sm qty-input" style="width: 60px;">

                                    <button class="btn btn-primary btn-sm flex-grow-1" id="modalAddToCart">
                                        <i class="mdi mdi-cart-outline me-1"></i> Add to Cart
                                    </button>
                                </div>

                                <button class="btn btn-success btn-sm w-100" id="modalBuyNow">
                                    <i class="mdi mdi-flash me-1"></i> Buy Now
                                </button>
                            </div>

                            {{-- Close Button --}}
                            <div class="d-grid gap-2 mt-3">
                                <button type="button" class="btn btn-light py-2" data-bs-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #categoryDropdown:hover {
            background: #2a2b2d;
            border-color: #f8f3f3;
        }

        /* ===== Hero ===== */
        .hero-card {
            background: linear-gradient(135deg, #3f51b5 0%, #2196f3 100%);
            color: #fff;
        }

        .hero-stat {
            max-width: 260px;
        }

        .hero-stat h3 {
            font-size: 1rem;
            opacity: .85;
        }

        .hero-stat p {
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* ===== Product Card ===== */
        .amazon-card {
            background: #ffffff;
            border: 1px solid #e8ecef;
            border-radius: 12px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: all 0.25s ease;
        }

        .amazon-card:hover {
            border-color: #cfd4da;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            transform: translateY(-3px);
        }

        .amazon-card .img-wrapper {
            width: 100%;
            height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 12px;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .amazon-card .product-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .amazon-card:hover .product-img {
            transform: scale(1.05);
        }

        .amazon-card .no-image {
            height: 170px;
            width: 100%;
            border-radius: 8px;
        }

        .amazon-card .card-details {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .amazon-card .product-title {
            font-size: 0.9rem;
            font-weight: 500;
            color: #212529;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.35;
            min-height: 2.7em;
        }

        .amazon-card .product-title:hover {
            color: #0d6efd;
        }

        .amazon-card .price-box {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .amazon-card .current-price {
            font-size: 1.15rem;
            font-weight: 700;
            color: #212529;
        }

        .amazon-card .current-price .currency {
            font-size: 0.8rem;
            font-weight: 500;
        }

        .amazon-card .mrp {
            font-size: 0.8rem;
            color: #6c757d !important;
        }

        .amazon-card .discount-badge {
            font-size: 0.75rem;
            font-weight: 600;
            color: #198754;
            background: #d1e7dd;
            padding: 1px 6px;
            border-radius: 4px;
        }

        .amazon-card .qty-input {
            width: 58px;
            text-align: center;
            border-radius: 6px;
            font-weight: 500;
        }

        .amazon-card .btn {
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.82rem;
            padding: 0.35rem 0.6rem;
        }

        .amazon-card .btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        /* Stats icon (if needed) */
        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        /* Search input styling */
        .input-group-sm .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
        }

        .input-group-sm .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }

        .input-group-sm .form-control {
            border-left: none;
        }
    </style>
@endsection

@push('scripts')
    <script>
        // ========================================
        // 1. SEARCH FUNCTIONALITY (Client-side)
        // ========================================
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('productSearch');
            const productItems = document.querySelectorAll('.product-item');
            const noResultsMsg = document.getElementById('noSearchResults');
            const noProductsMsg = document.getElementById('noProductsMsg');

            function checkNoProducts() {
                const visibleItems = document.querySelectorAll('.product-item:not([style*="display: none"])');
                if (visibleItems.length === 0) {
                    if (noResultsMsg) noResultsMsg.style.display = 'block';
                    if (noProductsMsg) noProductsMsg.style.display = 'none';
                } else {
                    if (noResultsMsg) noResultsMsg.style.display = 'none';
                    if (noProductsMsg) noProductsMsg.style.display = 'block';
                }
            }

            if (productItems.length === 0) {
                if (noResultsMsg) noResultsMsg.style.display = 'none';
                return;
            }

            function performSearch(searchTerm) {
                const term = searchTerm.toLowerCase().trim();
                productItems.forEach(function(item) {
                    const title = (item.dataset.title || '').toLowerCase();
                    const category = (item.dataset.category || '').toLowerCase();
                    const matches = term === '' || title.includes(term) || category.includes(term);
                    item.style.display = matches ? '' : 'none';
                });
                checkNoProducts();
            }

            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func(...args), wait);
                };
            }

            const debouncedSearch = debounce(function(e) {
                performSearch(e.target.value);
            }, 300);

            searchInput.addEventListener('input', debouncedSearch);
            checkNoProducts();

            // ========================================
            // 2. CATEGORY FILTER DROPDOWN
            // ========================================
            document.querySelectorAll('.category-option').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const cat = this.dataset.cat || '';
                    const url = new URL(window.location.href);
                    if (url.searchParams.get('category') === cat) return;
                    url.searchParams.delete('page');
                    if (cat === '') {
                        url.searchParams.delete('category');
                        document.getElementById('categoryLabel').textContent = 'All Categories';
                    } else {
                        url.searchParams.set('category', cat);
                        document.getElementById('categoryLabel').textContent = cat;
                    }
                    window.location.href = url.toString();
                });
            });

            // ========================================
            // 3. CART NOTIFICATION LOGIC
            // ========================================
            window.showNotification = function(type, title, message, detail = null) {
                const icon = document.getElementById('notifIcon');
                const iconWrapper = document.getElementById('notifIconWrapper');
                const titleEl = document.getElementById('notifTitle');
                const messageEl = document.getElementById('notifMessage');
                const detailEl = document.getElementById('notifDetail');
                const topBar = document.getElementById('notifTopBar');
                const okBtn = document.getElementById('notifOkBtn');
                const modalEl = document.getElementById('cartNotificationModal');

                if (!icon || !titleEl || !messageEl || !modalEl) {
                    alert(title + ': ' + message);
                    return;
                }

                detailEl.style.display = 'none';
                detailEl.textContent = '';

                if (type === 'success') {
                    topBar.style.background = '#198754';
                    iconWrapper.style.background = '#d1e7dd';
                    icon.innerHTML = '<i class="mdi mdi-check-circle text-success"></i>';
                    titleEl.className = 'fw-bold mb-2 text-success';
                    okBtn.className = 'btn btn-success rounded-pill py-2 fw-medium';
                    okBtn.innerHTML = '<i class="mdi mdi-check me-1"></i> Great!';
                } else if (type === 'warning') {
                    topBar.style.background = '#ffc107';
                    iconWrapper.style.background = '#fff3cd';
                    icon.innerHTML = '<i class="mdi mdi-alert text-warning"></i>';
                    titleEl.className = 'fw-bold mb-2 text-warning';
                    okBtn.className = 'btn btn-warning rounded-pill py-2 fw-medium text-dark';
                    okBtn.innerHTML = '<i class="mdi mdi-check me-1"></i> Understood';
                } else {
                    topBar.style.background = '#dc3545';
                    iconWrapper.style.background = '#f8d7da';
                    icon.innerHTML = '<i class="mdi mdi-close-circle text-danger"></i>';
                    titleEl.className = 'fw-bold mb-2 text-danger';
                    okBtn.className = 'btn btn-danger rounded-pill py-2 fw-medium';
                    okBtn.innerHTML = '<i class="mdi mdi-close me-1"></i> Close';
                }

                titleEl.textContent = title;
                messageEl.textContent = message;

                if (detail) {
                    detailEl.textContent = detail;
                    detailEl.style.display = 'block';
                }

                if (typeof $ !== 'undefined') {
                    $('#cartNotificationModal').modal('show');
                    if (type === 'success') {
                        setTimeout(() => $('#cartNotificationModal').modal('hide'), 2000);
                    }
                }
            };

            // ========================================
            // 4. PRODUCT DETAIL MODAL
            // ========================================
            window.openProductDetailModal = function(title, description, price, category, qty, image, productId) {
                // Set title
                document.getElementById('pdTitle').textContent = title;
                document.getElementById('pdName').textContent = title;

                // Set description
                document.getElementById('pdDesc').textContent = description && description.trim() !== '' ?
                    description : 'No description available.';

                // Set price
                const priceNum = parseFloat(price) || 0;
                document.getElementById('pdPrice').textContent = '₹' + priceNum.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                // Set MRP (40% higher)
                const mrp = priceNum * 1.4;
                document.getElementById('pdMrp').textContent = '₹' + mrp.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                // Set category
                document.getElementById('pdCategory').textContent = category || 'General';

                // Handle stock status
                const stockQty = parseInt(qty) || 0;
                const stockBadge = document.getElementById('pdStock');
                const modalQty = document.getElementById('modal_qty');
                const modalAddBtn = document.getElementById('modalAddToCart');
                const modalBuyBtn = document.getElementById('modalBuyNow');

                // Store product ID on buttons for later use
                if (modalAddBtn) modalAddBtn.dataset.productId = productId;
                if (modalBuyBtn) modalBuyBtn.dataset.productId = productId;

                if (stockQty > 0) {
                    stockBadge.innerHTML = '<i class="mdi mdi-check-circle me-1"></i> In Stock (' + stockQty +
                        ' available)';
                    stockBadge.className =
                        'badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2';
                    if (modalQty) {
                        modalQty.disabled = false;
                        modalQty.max = stockQty;
                        modalQty.value = 1;
                    }
                    if (modalAddBtn) modalAddBtn.disabled = false;
                    if (modalBuyBtn) modalBuyBtn.disabled = false;
                } else {
                    stockBadge.innerHTML = '<i class="mdi mdi-close-circle me-1"></i> Out of Stock';
                    stockBadge.className =
                        'badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-2';
                    if (modalQty) {
                        modalQty.disabled = true;
                        modalQty.value = 1;
                    }
                    if (modalAddBtn) modalAddBtn.disabled = true;
                    if (modalBuyBtn) modalBuyBtn.disabled = true;
                }

                // Set image
                let imgSrc = '';
                if (image) {
                    imgSrc = image.startsWith('http') ? image : '{{ asset('') }}' + image;
                }
                const mainImage = document.getElementById('pdMainImage');
                mainImage.src = imgSrc || 'https://via.placeholder.com/400x300?text=No+Image';
                mainImage.onerror = function() {
                    this.src = 'https://via.placeholder.com/400x300?text=No+Image';
                };

                // Show modal using jQuery
                if (typeof $ !== 'undefined') {
                    $('#productDetailModal').modal('show');
                }
            };
        });

        // ========================================
        // 5. JQUERY READY - AJAX & EVENT HANDLERS
        // ========================================
        jQuery(document).ready(function($) {
            // Set CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Modal OK button handler
            $('#notifOkBtn').on('click', function() {
                $('#cartNotificationModal').modal('hide');
            });

            // ========================================
            // MODAL ADD TO CART
            // ========================================
            $(document).on('click', '#modalAddToCart', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var productId = $(this).data('product-id');
                var quantity = $('#modal_qty').val();
                var $btn = $(this);

                // Prevent multiple clicks
                if ($btn.prop('disabled')) {
                    return;
                }

                if (!productId) {
                    showNotification('error', 'Error', 'Product not selected.');
                    return;
                }
                if (!quantity || quantity < 1) {
                    showNotification('error', 'Invalid Quantity', 'Please enter a valid quantity.');
                    return;
                }

                // Disable button and show loading
                $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Adding...');

                $.ajax({
                    url: "{{ route('cart.add') }}",
                    type: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show notification
                            showNotification('success', 'Success!', response.message);

                            // Update cart count
                            if (typeof updateCartCount === 'function') {
                                updateCartCount(response.cartCount);
                            }

                            // CLOSE MODAL IMMEDIATELY
                            $('#productDetailModal').modal('hide');

                            // Reset button state
                            $btn.prop('disabled', false).html(
                                '<i class="mdi mdi-cart-outline me-1"></i> Add to Cart');

                        } else {
                            showNotification('error', 'Error', response.message ||
                                'Could not add to cart');
                            $btn.prop('disabled', false).html(
                                '<i class="mdi mdi-cart-outline me-1"></i> Add to Cart');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.status, xhr.responseText);
                        if (xhr.status === 419) {
                            showNotification('error', 'Session Expired',
                                'Please refresh the page.');
                        } else if (xhr.status === 401) {
                            showNotification('error', 'Login Required',
                                'You must be logged in as a customer.');
                        } else if (xhr.status === 422) {
                            let msg = 'Validation failed';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            }
                            showNotification('error', 'Validation Error', msg);
                        } else {
                            showNotification('error', 'Error',
                                'Something went wrong. Please try again.');
                        }
                        $btn.prop('disabled', false).html(
                            '<i class="mdi mdi-cart-outline me-1"></i> Add to Cart');
                    }
                });
            });

            // ========================================
            // MODAL BUY NOW
            // ========================================
            $(document).on('click', '#modalBuyNow', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var productId = $(this).data('product-id');
                var quantity = $('#modal_qty').val();
                var $btn = $(this);

                // Prevent multiple clicks
                if ($btn.prop('disabled')) {
                    return;
                }

                if (!productId) {
                    showNotification('error', 'Error', 'Product not selected.');
                    return;
                }
                if (!quantity || quantity < 1) {
                    showNotification('error', 'Invalid Quantity', 'Please enter a valid quantity.');
                    return;
                }

                $btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Processing...');

                // Redirect to invoice creation
                window.location.href = "{{ route('customer.invoices.create') }}?product_id=" + productId +
                    "&quantity=" + quantity;
            });

            // ========================================
            // MODAL CLOSE - Reset state
            // ========================================
            $('#productDetailModal').on('hidden.bs.modal', function() {
                // Reset the quantity input
                $('#modal_qty').val(1);
                // Reset button states
                $('#modalAddToCart').prop('disabled', false).html(
                    '<i class="mdi mdi-cart-outline me-1"></i> Add to Cart');
                $('#modalBuyNow').prop('disabled', false).html(
                    '<i class="mdi mdi-flash me-1"></i> Buy Now');
            });

            // ========================================
            // CARD ADD TO CART
            // ========================================
            $(document).on('click', '.add-to-cart-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var productId = $(this).data('product-id');
                var quantity = $('#qty_' + productId).val();
                var $btn = $(this);
                var $cardWrapper = $('#card-actions-' + productId);
                var $actions = $cardWrapper.find('.card-actions');
                var $successMsg = $cardWrapper.find('.added-success-message');

                // Prevent multiple clicks
                if ($btn.prop('disabled')) {
                    return;
                }

                if (!quantity || quantity < 1) {
                    showNotification('error', 'Invalid Quantity', 'Please enter a valid quantity.');
                    return;
                }

                // Disable button and show loading
                $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Adding...');

                $.ajax({
                    url: "{{ route('cart.add') }}",
                    type: 'POST',
                    data: {
                        product_id: productId,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show notification
                            showNotification('success', 'Success!', response.message);

                            // Update cart count
                            if (typeof updateCartCount === 'function') {
                                updateCartCount(response.cartCount);
                            }

                            // Hide actions and show success message
                            $actions.addClass('fade-out');
                            $actions.css('display', 'none');
                            $successMsg.css('display', 'block');

                            // Reset button state
                            $btn.prop('disabled', false).html(
                                '<i class="mdi mdi-cart-outline me-1"></i> Add');

                            // After 3 seconds, reset back to show actions again
                            setTimeout(function() {
                                $successMsg.css('display', 'none');
                                $actions.css('display', 'flex');
                                $actions.removeClass('fade-out');
                            }, 3000);

                        } else {
                            showNotification('error', 'Error', response.message ||
                                'Could not add to cart');
                            $btn.prop('disabled', false).html(
                                '<i class="mdi mdi-cart-outline me-1"></i> Add');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.status, xhr.responseText);
                        if (xhr.status === 419) {
                            showNotification('error', 'Session Expired',
                                'Please refresh the page.');
                        } else if (xhr.status === 401) {
                            showNotification('error', 'Login Required',
                                'You must be logged in as a customer.');
                        } else if (xhr.status === 422) {
                            let msg = 'Validation failed';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            }
                            showNotification('error', 'Validation Error', msg);
                        } else {
                            showNotification('error', 'Error',
                                'Something went wrong. Please try again.');
                        }
                        $btn.prop('disabled', false).html(
                            '<i class="mdi mdi-cart-outline me-1"></i> Add');
                    }
                });
            });

            // ========================================
            // CARD BUY NOW
            // ========================================
            $(document).on('click', '.buy-now-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var productId = $(this).data('product-id');
                var quantity = $('#qty_' + productId).val();
                var $btn = $(this);

                // Prevent multiple clicks
                if ($btn.prop('disabled')) {
                    return;
                }

                if (!quantity || quantity < 1) {
                    showNotification('error', 'Invalid Quantity', 'Please enter a valid quantity.');
                    return;
                }

                $btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Processing...');

                window.location.href = "{{ route('customer.invoices.create') }}?product_id=" + productId +
                    "&quantity=" + quantity;
            });
        });
    </script>
@endpush
