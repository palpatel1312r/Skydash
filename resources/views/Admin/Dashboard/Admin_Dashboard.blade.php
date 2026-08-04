@extends('Components.superadminheader')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            {{-- Alerts --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Welcome Admin</h3>
                            <h6 class="font-weight-normal mb-0">Your B2B marketplace is running smoothly!</h6>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATS CARDS --}}
            <div class="row">
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card card-tale">
                        <div class="card-body">
                            <p class="mb-4">Total Products</p>
                            <p class="fs-30 mb-2">{{ $totalProducts }}</p>
                            <p>Active Catalog</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card card-dark-blue">
                        <div class="card-body">
                            <p class="mb-4">Active Dealers</p>
                            <p class="fs-30 mb-2">{{ $activeDealers }}</p>
                            <p>Registered Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card card-light-blue">
                        <div class="card-body">
                            <p class="mb-4">Today's Revenue</p>
                            <p class="fs-30 mb-2">₹{{ number_format($todayRevenue, 0) }}</p>
                            <p>Net Sales</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 grid-margin stretch-card">
                    <div class="card card-light-danger">
                        <div class="card-body">
                            <p class="mb-4">Low Stock Items</p>
                            <p class="fs-30 mb-2 text-danger">{{ $lowStockCount }}</p>
                            <p>Need Immediate Restock</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NEW ARRIVALS SECTION --}}
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge badge-primary p-2 rounded-circle"><i
                                            class="mdi mdi-star text-white"></i></span>
                                    <h4 class="card-title mb-0 ms-2 fw-bold">New Arrivals</h4>
                                </div>
                                <a href="{{ route('products') }}"
                                    class="text-primary small fw-bold text-decoration-none">See more <i
                                        class="mdi mdi-arrow-right"></i></a>
                            </div>

                            {{-- SLIDER WRAPPER --}}
                            <div class="slider-container new-arrivals-container">
                                @foreach ($newArrivals as $product)
                                    <div class="product-slide">
                                        <div class="amazon-card">
                                            <div class="img-wrapper">
                                                @if ($product->image)
                                                    <img src="{{ asset($product->image) }}" alt="{{ $product->title }}">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center text-muted"
                                                        style="height: 160px; width:100%; border-radius:8px;">
                                                        <small>No Image</small>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="card-details">
                                                <h6 class="product-title">{{ $product->title }}</h6>
                                                <div class="price-box">
                                                    <span class="currency">₹</span>{{ number_format($product->price, 0) }}
                                                    <span
                                                        class="mrp text-danger"><del>₹{{ number_format($product->price * 1.4, 0) }}</del></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BEST SELLERS SECTION --}}
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge badge-danger p-2 rounded-circle"><i
                                            class="mdi mdi-fire text-white"></i></span>
                                    <h4 class="card-title mb-0 ms-2 fw-bold">Best Sellers</h4>
                                </div>
                                <a href="{{ route('products') }}"
                                    class="text-primary small fw-bold text-decoration-none">See more <i
                                        class="mdi mdi-arrow-right"></i></a>
                            </div>

                            {{-- SLIDER WRAPPER --}}
                            <div class="slider-container best-sellers-container">
                                @foreach ($bestSellers as $product)
                                    <div class="product-slide">
                                        <div class="amazon-card">
                                            <div class="img-wrapper">
                                                @if ($product->image)
                                                    <img src="{{ asset($product->image) }}" alt="{{ $product->title }}">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center text-muted"
                                                        style="height: 160px; width:100%; border-radius:8px;">
                                                        <small>No Image</small>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="card-details">
                                                <h6 class="product-title">{{ $product->title }}</h6>
                                                <div class="price-box">
                                                    <span class="currency">₹</span>{{ number_format($product->price, 0) }}
                                                    <span
                                                        class="mrp text-danger"><del>₹{{ number_format($product->price * 1.4, 0) }}</del></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RECENT ACTIVITY & ORDERS --}}
            <div class="row">
                <div class="col-md-7 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title mb-3">Recent Activity Log</p>
                            <div class="table-responsive">
                                <table class="table table-striped table-borderless">
                                    <thead>
                                        <tr>
                                            <th>User / Dealer</th>
                                            <th>Action</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentActivities as $activity)
                                            <tr>
                                                <td><strong>{{ $activity['user'] }}</strong></td>
                                                <td>{{ $activity['action'] }}</td>
                                                <td class="text-muted">{{ $activity['time'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Quick Actions</h4>
                            <div class="list-wrapper pt-2">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('products.create') }}"
                                        class="btn btn-primary btn-lg text-start shadow-sm">
                                        <i class="mdi mdi-plus-circle me-2"></i> Add New Product
                                    </a>
                                    <a href="{{ route('admin.customers.create') }}"
                                        class="btn btn-info btn-lg text-start shadow-sm">
                                        <i class="mdi mdi-account-plus me-2"></i> Register New Dealer
                                    </a>
                                    <a href="{{ route('invoices.index') }}"
                                        class="btn btn-warning btn-lg text-start shadow-sm">
                                        <i class="mdi mdi-file-document-edit me-2"></i> Manage Pending Orders
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SLIDER CSS & JS ASSETS --}}
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

    <style>
        /* ===== STAT CARDS ===== */
        .card-tale,
        .card-dark-blue,
        .card-light-blue,
        .card-light-danger {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-tale:hover,
        .card-dark-blue:hover,
        .card-light-blue:hover,
        .card-light-danger:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* ===== SLIDER CONTAINER GRID (FALLBACK) ===== */
        .slider-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            /* 4 Columns */
            gap: 20px;
            padding: 10px 0;
        }

        /* ===== PRODUCT SLIDE CARD ===== */
        .product-slide {
            padding: 0;
            /* No extra padding inside grid */
        }

        .amazon-card {
            background: #ffffff;
            border: 1px solid #e4e7eb;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            transition: all 0.2s ease;
            display: flex !important;
            flex-direction: column;
            align-items: center;
            height: 100%;
            position: relative;
        }

        .amazon-card:hover {
            border-color: #c4c9d1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        /* ===== IMAGE HANDLING ===== */
        .amazon-card .img-wrapper {
            width: 100%;
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .amazon-card img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.2s ease;
        }

        .amazon-card:hover img {
            transform: scale(1.02);
        }

        /* ===== TEXT DETAILS ===== */
        .amazon-card .card-details {
            width: 100%;
            text-align: left;
            padding: 0 2px;
        }

        .amazon-card .product-title {
            font-size: 0.85rem;
            font-weight: 400;
            color: #007185;
            margin-bottom: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 38px;
            line-height: 1.3;
            text-align: left;
        }

        .amazon-card .product-title:hover {
            color: #c7511f;
            text-decoration: underline;
        }

        .amazon-card .price-box {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            font-size: 1.1rem;
            font-weight: 600;
            color: #0f1111;
        }

        .amazon-card .price-box .currency {
            font-size: 0.75rem;
            font-weight: 500;
            margin-right: -2px;
        }

        .amazon-card .price-box .mrp {
            font-size: 0.75rem;
            font-weight: 400;
            color: #565959 !important;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .slider-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 800px) {
            .slider-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 500px) {
            .slider-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // 1. Turn new arrivals into a slider
            $('.new-arrivals-container').slick({
                dots: false,
                arrows: true,
                infinite: true,
                speed: 300,
                slidesToShow: 4,
                /* 4 exact cards */
                slidesToScroll: 1,
                responsive: [{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ],
                // ✅ Force override any display issues
                cssEase: 'linear'
            });

            // 2. Turn best sellers into a slider
            $('.best-sellers-container').slick({
                dots: false,
                arrows: true,
                infinite: true,
                speed: 300,
                slidesToShow: 4,
                /* 4 exact cards */
                slidesToScroll: 1,
                responsive: [{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ],
                cssEase: 'linear'
            });
        });
    </script>
@endsection
