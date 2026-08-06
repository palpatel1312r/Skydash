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
                                </div>
                            </div>

                            {{-- PRODUCT GRID - 4 columns on desktop --}}
                            <div class="row g-3">
                                @forelse($products ?? [] as $product)
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
                                        <div class="amazon-card h-100">
                                            {{-- Image --}}
                                            <div class="img-wrapper position-relative">
                                                @if ($product->image)
                                                    <img src="{{ asset($product->image) }}" alt="{{ $product->title }}"
                                                        class="product-img">
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
                                                    <span class="discount-badge">
                                                        {{ round((1 - 1 / 1.4) * 100) }}% off
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

                                                <div class="mt-auto d-flex flex-column gap-2 w-100">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="number" id="qty_{{ $product->id }}" value="1"
                                                            min="1" max="{{ $product->quantity ?? 99 }}"
                                                            class="form-control form-control-sm qty-input"
                                                            {{ ($product->quantity ?? 0) <= 0 ? 'disabled' : '' }}>

                                                        <button class="btn btn-primary btn-sm flex-grow-1 add-to-cart-btn"
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
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-5 text-muted">
                                        <i class="mdi mdi-package-variant-closed" style="font-size: 48px;"></i>
                                        <p class="mt-2 mb-0">No products available at the moment.</p>
                                    </div>
                                @endforelse
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

    {{-- ENHANCED CART NOTIFICATION MODAL --}}
    <div class="modal fade" id="cartNotificationModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 18px;">

                {{-- Colored top bar --}}
                <div id="notifTopBar" style="height: 6px; background: #0d6efd;"></div>

                <div class="modal-body text-center p-4 pt-4">
                    {{-- Icon circle --}}
                    <div id="notifIconWrapper" class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                        style="width: 78px; height: 78px; border-radius: 50%; background: #e7f1ff;">
                        <div id="notifIcon" style="font-size: 36px; line-height: 1;"></div>
                    </div>

                    {{-- Title --}}
                    <h4 id="notifTitle" class="fw-bold mb-2" style="font-size: 1.35rem;">Notification</h4>

                    {{-- Message --}}
                    <p id="notifMessage" class="text-muted mb-1 px-2" style="font-size: 0.95rem; line-height: 1.5;">
                        Message goes here
                    </p>

                    {{-- Extra detail line (optional) --}}
                    <p id="notifDetail" class="small text-muted mb-4" style="display: none;"></p>

                    {{-- Buttons --}}
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary rounded-pill py-2 fw-medium" id="notifOkBtn">
                            <i class="mdi mdi-check me-1"></i> Got it
                        </button>
                        <button type="button" class="btn btn-outline-secondary rounded-pill py-2" id="notifCloseBtn"
                            style="display: none;">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
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
    </style>
@endsection

@push('scripts')
    <script>
        let cartModalInstance = null;

        function showNotification(type, title, message, detail = null) {
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

            // Reset
            detailEl.style.display = 'none';
            detailEl.textContent = '';

            if (type === 'success') {
                // Success style
                topBar.style.background = '#198754';
                iconWrapper.style.background = '#d1e7dd';
                icon.innerHTML = '<i class="mdi mdi-check-circle text-success"></i>';
                titleEl.className = 'fw-bold mb-2 text-success';
                okBtn.className = 'btn btn-success rounded-pill py-2 fw-medium';
                okBtn.innerHTML = '<i class="mdi mdi-check me-1"></i> Great!';
            } else if (type === 'warning') {
                // Warning style
                topBar.style.background = '#ffc107';
                iconWrapper.style.background = '#fff3cd';
                icon.innerHTML = '<i class="mdi mdi-alert text-warning"></i>';
                titleEl.className = 'fw-bold mb-2 text-warning';
                okBtn.className = 'btn btn-warning rounded-pill py-2 fw-medium text-dark';
                okBtn.innerHTML = '<i class="mdi mdi-check me-1"></i> Understood';
            } else {
                // Error style
                topBar.style.background = '#dc3545';
                iconWrapper.style.background = '#f8d7da';
                icon.innerHTML = '<i class="mdi mdi-close-circle text-danger"></i>';
                titleEl.className = 'fw-bold mb-2 text-danger';
                okBtn.className = 'btn btn-danger rounded-pill py-2 fw-medium';
                okBtn.innerHTML = '<i class="mdi mdi-close me-1"></i> Close';
            }

            titleEl.textContent = title;
            messageEl.textContent = message;

            // Optional extra detail
            if (detail) {
                detailEl.textContent = detail;
                detailEl.style.display = 'block';
            }

            // Create / reuse modal instance
            if (!cartModalInstance) {
                cartModalInstance = new bootstrap.Modal(modalEl, {
                    backdrop: true,
                    keyboard: true
                });
            }

            cartModalInstance.show();

            // Auto-close success after 2 seconds
            if (type === 'success') {
                setTimeout(() => {
                    if (cartModalInstance) cartModalInstance.hide();
                }, 2000);
            }
        }

        // Close handlers
        document.addEventListener('DOMContentLoaded', function() {
            const closeBtn = document.getElementById('notifCloseBtn');
            const okBtn = document.getElementById('notifOkBtn');

            function closeModal() {
                if (cartModalInstance) {
                    cartModalInstance.hide();
                } else {
                    $('#cartNotificationModal').modal('hide'); // Bootstrap 4 fallback
                }
            }

            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (okBtn) okBtn.addEventListener('click', closeModal);
        });
        // Manually close modal when OK or X is clicked
        document.addEventListener('DOMContentLoaded', function() {
            const closeBtn = document.getElementById('notifCloseBtn');
            const okBtn = document.getElementById('notifOkBtn');

            function closeModal() {
                if (cartModalInstance) {
                    cartModalInstance.hide();
                } else {
                    // Fallback for Bootstrap 4
                    $('#cartNotificationModal').modal('hide');
                }
            }

            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (okBtn) okBtn.addEventListener('click', closeModal);
        });

        jQuery(document).ready(function($) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ===== ADD TO CART =====
            $(document).on('click', '.add-to-cart-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var productId = $(this).data('product-id');
                var quantity = $('#qty_' + productId).val();
                var $btn = $(this);

                if (!quantity || quantity < 1) {
                    showNotification('error', 'Invalid Quantity', 'Please enter a valid quantity.');
                    return;
                }

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
                            showNotification('success', 'Success!', response.message);
                            $('#cartCount').text(response.cartCount);
                        } else {
                            showNotification('error', 'Error', response.message ||
                                'Could not add to cart');
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
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            '<i class="mdi mdi-cart-outline me-1"></i> Add');
                    }
                });
            });

            // ===== BUY NOW =====
            $(document).on('click', '.buy-now-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var productId = $(this).data('product-id');
                var quantity = $('#qty_' + productId).val();
                var $btn = $(this);

                if (!quantity || quantity < 1) {
                    showNotification('error', 'Invalid Quantity', 'Please enter a valid quantity.');
                    return;
                }

                $btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Processing...');

                // Buy Now → go to invoice create with product & qty
                window.location.href = "{{ route('customer.invoices.create') }}?product_id=" + productId +
                    "&quantity=" + quantity;
            });
        });
    </script>
@endpush
