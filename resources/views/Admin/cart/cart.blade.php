@extends('Components.customerheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">

            {{-- Alerts --}}
            <div class="row">
                <div class="col-12">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                            <i class="mdi mdi-alert-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SINGLE COLUMN LAYOUT --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                        {{-- Card Header --}}
                        <div class="card-header bg-white border-bottom py-4 px-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <h4 class="card-title fw-bold mb-1">
                                        <i class="mdi mdi-cart text-primary me-2"></i> My Shopping Cart
                                    </h4>
                                    <p class="text-muted small mb-0">
                                        {{ count($cartItems) }} item{{ count($cartItems) !== 1 ? 's' : '' }} in your cart
                                    </p>
                                </div>
                                <a href="{{ route('customer.dashboard') }}"
                                    class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    <i class="mdi mdi-arrow-left me-1"></i> Continue Shopping
                                </a>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            @if (count($cartItems) > 0)
                                {{-- TABLE --}}
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr class="text-muted small text-uppercase">
                                                <th class="ps-4 py-3" style="width: 40%;">Product</th>
                                                <th class="py-3" style="width: 15%;">Unit Price</th>
                                                <th class="py-3 text-center" style="width: 20%;">Quantity</th>
                                                <th class="py-3 text-end" style="width: 15%;">Subtotal</th>
                                                <th class="pe-4 py-3 text-center" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $total = 0; @endphp
                                            @foreach ($cartItems as $item)
                                                @php
                                                    $subtotal = $item->product->price * $item->quantity;
                                                    $total += $subtotal;
                                                @endphp
                                                <tr class="border-bottom">
                                                    {{-- Product --}}
                                                    <td class="ps-4 py-4">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3 bg-light rounded-3 d-flex align-items-center justify-content-center"
                                                                style="width: 80px; height: 80px; flex-shrink: 0;">
                                                                @if ($item->product->image)
                                                                    <img src="{{ asset($item->product->image) }}"
                                                                        alt="{{ $item->product->title }}"
                                                                        class="img-fluid rounded-2"
                                                                        style="max-width: 90%; max-height: 90%; object-fit: contain;">
                                                                @else
                                                                    <i class="mdi mdi-image-off text-muted"
                                                                        style="font-size: 28px;"></i>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <h6 class="fw-bold mb-1 text-dark">
                                                                    {{ $item->product->title }}
                                                                </h6>
                                                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                                                    <small class="text-muted">SKU:
                                                                        #{{ $item->product->id }}</small>
                                                                    <span
                                                                        class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1"
                                                                        style="font-size: 11px;">
                                                                        <i class="mdi mdi-check-circle me-1"></i> In Stock
                                                                    </span>
                                                                </div>
                                                                @if ($item->product->category ?? false)
                                                                    <small class="text-muted d-block mt-1">
                                                                        Category: {{ $item->product->category }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>

                                                    {{-- Unit Price --}}
                                                    <td class="py-4">
                                                        <span
                                                            class="fw-semibold">₹{{ number_format($item->product->price, 2) }}</span>
                                                    </td>

                                                    {{-- Quantity --}}
                                                    <td class="py-4 text-center">
                                                        <div
                                                            class="d-inline-flex align-items-center gap-2 bg-light rounded-pill px-2 py-1">
                                                            <button type="button"
                                                                class="btn btn-sm btn-white border-0 rounded-circle qty-btn qty-decrease shadow-sm"
                                                                data-id="{{ $item->id }}"
                                                                style="width: 30px; height: 30px;">
                                                                <i class="mdi mdi-minus" style="font-size: 14px;"></i>
                                                            </button>

                                                            <span class="fw-bold px-2 quantity-display"
                                                                id="qty-display-{{ $item->id }}"
                                                                style="min-width: 28px;">
                                                                {{ $item->quantity }}
                                                            </span>

                                                            <button type="button"
                                                                class="btn btn-sm btn-white border-0 rounded-circle qty-btn qty-increase shadow-sm"
                                                                data-id="{{ $item->id }}"
                                                                style="width: 30px; height: 30px;">
                                                                <i class="mdi mdi-plus" style="font-size: 14px;"></i>
                                                            </button>
                                                        </div>
                                                    </td>

                                                    {{-- Subtotal --}}
                                                    <td class="py-4 text-end">
                                                        <span class="fw-bold text-primary fs-6 subtotal-cell"
                                                            id="subtotal-{{ $item->id }}">
                                                            ₹{{ number_format($subtotal, 2) }}
                                                        </span>
                                                    </td>

                                                    {{-- Action: Buy Now + Remove --}}
                                                    <td class="pe-4 py-4 text-center">
                                                        <div class="d-flex flex-column gap-2 align-items-center">
                                                            {{-- Buy Now Button --}}
                                                            <a href="{{ route('customer.invoices.create', [
                                                                'product_id' => $item->product_id,
                                                                'quantity' => $item->quantity,
                                                                'cart_id' => $item->id,
                                                                'from_buy_now' => 1,
                                                            ]) }}"
                                                                class="btn btn-success btn-sm w-100 rounded-pill"
                                                                style="font-size: 0.75rem;">
                                                                <i class="mdi mdi-flash me-1"></i> Buy Now
                                                            </a>

                                                            {{-- Remove Button --}}
                                                            <form action="{{ route('cart.remove', $item->id) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Are you sure you want to remove this item?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-outline-danger btn-sm rounded-pill w-100"
                                                                    style="font-size: 0.7rem;">
                                                                    <i class="mdi mdi-delete-outline me-1"></i> Remove
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- ORDER SUMMARY (below table) --}}
                                <div class="bg-light bg-opacity-50 border-top px-4 py-5">
                                    <div class="row justify-content-end">
                                        <div class="col-lg-5 col-md-7">
                                            <div class="card border-0 shadow-sm rounded-4">
                                                <div class="card-body p-4">
                                                    <h5 class="fw-bold mb-4 d-flex align-items-center">
                                                        <i class="mdi mdi-receipt text-primary me-2"></i>
                                                        Order Summary
                                                    </h5>

                                                    <div class="d-flex justify-content-between mb-3">
                                                        <span class="text-muted">Subtotal
                                                            ({{ count($cartItems) }} items)</span>
                                                        <span class="fw-medium"
                                                            id="summary-subtotal">₹{{ number_format($total, 2) }}</span>
                                                    </div>

                                                    <div class="d-flex justify-content-between mb-3">
                                                        <span class="text-muted">Shipping</span>
                                                        <span class="text-success fw-medium">
                                                            <i class="mdi mdi-truck-fast-outline me-1"></i> Free
                                                        </span>
                                                    </div>

                                                    <div class="d-flex justify-content-between mb-3">
                                                        <span class="text-muted">Estimated Tax</span>
                                                        <span>₹0.00</span>
                                                    </div>

                                                    <hr class="my-3">

                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <h5 class="fw-bold mb-0">Grand Total</h5>
                                                        <h4 class="fw-bold text-success mb-0" id="summary-total">
                                                            ₹{{ number_format($total, 2) }}
                                                        </h4>
                                                    </div>

                                                    <div class="d-grid gap-2">
                                                        <a href="{{ route('customer.invoices.create', ['from_cart' => 1]) }}"
                                                            class="btn btn-success btn-lg rounded-pill shadow-sm fw-bold py-3">
                                                            <i class="mdi mdi-cart-check me-2"></i> Buy All Items
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- EMPTY STATE --}}
                                <div class="text-center py-5 px-4">
                                    <div class="mb-4">
                                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width: 120px; height: 120px;">
                                            <i class="mdi mdi-cart-off text-muted" style="font-size: 56px;"></i>
                                        </div>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-2">Your Cart is Empty</h4>
                                    <p class="text-muted mb-4 col-md-6 mx-auto">
                                        Looks like you haven't added any products yet. Explore our catalog and find
                                        something you love!
                                    </p>
                                    <a href="{{ route('customer.dashboard') }}"
                                        class="btn btn-primary rounded-pill px-5 py-2 shadow-sm">
                                        <i class="mdi mdi-arrow-left me-2"></i> Start Shopping
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .rounded-4 {
            border-radius: 1rem !important;
        }

        .qty-btn {
            transition: all 0.2s ease;
        }

        .qty-btn:hover {
            background-color: #0d6efd !important;
            color: white !important;
        }

        .table> :not(caption)>*>* {
            border-bottom-width: 1px;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "›";
            font-weight: bold;
        }

        .card-header {
            background: linear-gradient(to right, #ffffff, #f8f9fa);
        }

        /* Action buttons */
        .action-buttons .btn {
            transition: all 0.2s ease;
        }

        .action-buttons .btn:hover {
            transform: translateY(-1px);
        }
    </style>
@endsection

@push('scripts')
    <script>
        // ========================================
        // 1. NOTIFICATION FUNCTION
        // ========================================
        function showNotification(type, title, message) {
            // Try to use the existing notification modal if available
            const notifModal = document.getElementById('cartNotificationModal');
            if (notifModal) {
                const icon = document.getElementById('notifIcon');
                const titleEl = document.getElementById('notifTitle');
                const messageEl = document.getElementById('notifMessage');
                const topBar = document.getElementById('notifTopBar');
                const okBtn = document.getElementById('notifOkBtn');
                const iconWrapper = document.getElementById('notifIconWrapper');

                if (icon && titleEl && messageEl) {
                    if (type === 'success') {
                        topBar.style.background = '#198754';
                        iconWrapper.style.background = '#d1e7dd';
                        icon.innerHTML = '<i class="mdi mdi-check-circle text-success"></i>';
                        titleEl.className = 'fw-bold mb-2 text-success';
                        okBtn.className = 'btn btn-success rounded-pill py-2 fw-medium';
                        okBtn.innerHTML = '<i class="mdi mdi-check me-1"></i> Great!';
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
                    $('#cartNotificationModal').modal('show');
                    if (type === 'success') {
                        setTimeout(() => $('#cartNotificationModal').modal('hide'), 2000);
                    }
                    return;
                }
            }
            // Fallback to alert
            alert(title + ': ' + message);
        }

        // ========================================
        // 2. JQUERY READY
        // ========================================
        jQuery(document).ready(function($) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ========================================
            // 3. QUANTITY INCREASE / DECREASE
            // ========================================
            $(document).on('click', '.qty-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $btn = $(this);
                var cartId = $btn.data('id');
                var $display = $('#qty-display-' + cartId);
                var currentQty = parseInt($display.text());

                if ($btn.hasClass('qty-decrease') && currentQty > 1) {
                    currentQty--;
                } else if ($btn.hasClass('qty-increase')) {
                    currentQty++;
                } else {
                    return;
                }

                $('.qty-btn').prop('disabled', true);

                $.ajax({
                    url: '/cart/update/' + cartId,
                    type: 'POST',
                    data: {
                        _method: 'PATCH',
                        quantity: currentQty
                    },
                    success: function(response) {
                        $display.text(currentQty);
                        $('#subtotal-' + cartId).text('₹' + response.subtotal);
                        $('#summary-subtotal').text('₹' + response.total);
                        $('#summary-total').text('₹' + response.total);

                        if (response.cartCount !== undefined) {
                            $('#cartCount').text(response.cartCount);
                        }

                        // Update the buy now button data-quantity attribute
                        $('.buy-now-cart-btn[data-cart-id="' + cartId + '"]').data('quantity',
                            currentQty);


                    },
                    error: function() {
                        showNotification('error', 'Error', 'Failed to update quantity.');
                    },
                    complete: function() {
                        $('.qty-btn').prop('disabled', false);
                    }
                });
            });

            // ========================================
            // 4. BUY NOW FROM CART (WITH AUTO-SELECT)
            // ========================================
            $(document).on('click', '.buy-now-cart-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $btn = $(this);
                var productId = $btn.data('product-id');
                var quantity = $btn.data('quantity');
                var cartId = $btn.data('cart-id');

                // Prevent multiple clicks
                if ($btn.prop('disabled')) {
                    return;
                }

                if (!productId || !quantity) {
                    showNotification('error', 'Error', 'Product information missing.');
                    return;
                }

                $btn.prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Processing...');

                // Store product data in sessionStorage for the invoice page
                var productData = {
                    product_id: productId,
                    quantity: quantity,
                    cart_id: cartId,
                    from_cart: true,
                    from_buy_now: true
                };

                try {
                    sessionStorage.setItem('buy_now_product', JSON.stringify(productData));
                } catch (e) {
                    console.warn('Session storage not available, using URL params instead.');
                }

                // Redirect to invoice creation with URL parameters as fallback
                window.location.href = "{{ route('customer.invoices.create') }}?product_id=" + productId +
                    "&quantity=" + quantity +
                    "&cart_id=" + cartId +
                    "&from_buy_now=1";
            });

            // ========================================
            // 5. CART REMOVE WITH CONFIRMATION
            // ========================================
            $(document).on('submit', 'form[action*="cart.remove"]', function(e) {
                if (!confirm('Are you sure you want to remove this item from your cart?')) {
                    e.preventDefault();
                }
            });
        });

        // ========================================
        // 6. HANDLE BUY NOW ON INVOICE PAGE
        // ========================================
        // This code runs when the invoice creation page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're on the invoice creation page
            if (window.location.pathname.includes('/invoices/create')) {
                var productId = null;
                var quantity = null;
                var cartId = null;

                // Try to get data from sessionStorage first
                try {
                    var storedData = sessionStorage.getItem('buy_now_product');
                    if (storedData) {
                        var data = JSON.parse(storedData);
                        productId = data.product_id;
                        quantity = data.quantity;
                        cartId = data.cart_id;
                        // Clear after use
                        sessionStorage.removeItem('buy_now_product');
                    }
                } catch (e) {
                    console.warn('Error reading from sessionStorage:', e);
                }

                // Fallback to URL parameters
                if (!productId) {
                    var urlParams = new URLSearchParams(window.location.search);
                    productId = urlParams.get('product_id');
                    quantity = urlParams.get('quantity');
                    cartId = urlParams.get('cart_id');
                }

                // Auto-select the product if we have a product ID
                if (productId) {
                    // Wait for the page to fully load
                    setTimeout(function() {
                        var $productSelect = $('#product_id');
                        var $qtyInput = $('#quantity');

                        if ($productSelect.length) {
                            // Set the product value
                            $productSelect.val(productId).trigger('change');

                            // Also try to trigger change on the select if it's a dynamic dropdown
                            if (typeof $productSelect.trigger === 'function') {
                                $productSelect.trigger('change');
                            }
                        }

                        if ($qtyInput.length && quantity) {
                            $qtyInput.val(quantity);
                        }

                        // Auto-trigger tax calculation if needed
                        if (typeof calculateTotal === 'function') {
                            calculateTotal();
                        }

                        // Show a notification
                        if (typeof showNotification === 'function') {
                            showNotification('success', 'Product Loaded',
                                'Product has been automatically selected for your invoice.');
                        }

                    }, 500); // Slight delay to ensure DOM is ready
                }
            }
        });
    </script>
@endpush
