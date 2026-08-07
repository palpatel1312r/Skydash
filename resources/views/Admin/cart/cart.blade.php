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

            {{-- Breadcrumb --}}
            <div class="row mb-3">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0 mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('customer.dashboard') }}"
                                    class="text-decoration-none text-muted">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active text-primary fw-medium" aria-current="page">Shopping Cart
                            </li>
                        </ol>
                    </nav>
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
                                                <th class="ps-4 py-3" style="width: 42%;">Product</th>
                                                <th class="py-3" style="width: 14%;">Unit Price</th>
                                                <th class="py-3 text-center" style="width: 20%;">Quantity</th>
                                                <th class="py-3 text-end" style="width: 14%;">Subtotal</th>
                                                <th class="pe-4 py-3 text-end" style="width: 10%;">Action</th>
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

                                                    {{-- Remove --}}
                                                    <td class="pe-4 py-4 text-end">
                                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                                                            onsubmit="return confirm('Are you sure you want to remove this item?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-outline-danger btn-sm rounded-circle"
                                                                style="width: 38px; height: 38px;" title="Remove item">
                                                                <i class="mdi mdi-delete-outline"></i>
                                                            </button>
                                                        </form>
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
                                                            <i class="mdi mdi-credit-card-outline me-2"></i> Proceed to
                                                            Checkout
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
                                        Looks like you haven’t added any products yet. Explore our catalog and find
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
    </style>
@endsection

@push('scripts')
    <script>
        // Keep your existing quantity update + notification scripts here
        // (the ones you already have working)

        function showNotification(type, title, message) {
            const icon = document.getElementById('notifIcon');
            const titleEl = document.getElementById('notifTitle');
            const messageEl = document.getElementById('notifMessage');

            if (type === 'success') {
                icon.innerHTML = '<i class="mdi mdi-check-circle text-success"></i>';
                titleEl.className = 'fw-bold mb-2 text-success';
            } else {
                icon.innerHTML = '<i class="mdi mdi-alert-circle text-danger"></i>';
                titleEl.className = 'fw-bold mb-2 text-danger';
            }

            titleEl.textContent = title;
            messageEl.textContent = message;

            const modal = new bootstrap.Modal(document.getElementById('notificationModal'));
            modal.show();
        }

        jQuery(document).ready(function($) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Quantity Increase / Decrease
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
                        $('#cartCount').text(response.cartCount);
                    },
                    error: function() {
                        showNotification('error', 'Error', 'Failed to update quantity.');
                    },
                    complete: function() {
                        $('.qty-btn').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endpush
