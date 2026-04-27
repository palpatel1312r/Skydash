<x-adminheader />

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <!-- Button to Open the Modal -->
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNewModal">
                            Add New Order
                        </button>

                        <!-- The Modal -->
                        <div class="modal" id="addNewModal">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">Add New Order</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <!-- Modal body -->
                                    {{-- Insert Form --}}
                                    <div class="modal-body">
                                        <form action="{{ URL::to('AddNewOrder') }}" method="POST">
                                            @csrf

                                            <label>Customer Name :</label>
                                            <input type="text" name="fullname" placeholder="Enter Customer Name"
                                                class="form-control mb-2" required>

                                            <label>Email :</label>
                                            <input type="email" name="email" placeholder="Enter Customer Email"
                                                class="form-control mb-2" required>

                                            <label>Bill Amount ($) :</label>
                                            <input type="number" name="bill" placeholder="Enter Bill Amount"
                                                class="form-control mb-2" step="0.01" required>

                                            <label>Phone :</label>
                                            <input type="text" name="phone" placeholder="Enter Phone Number"
                                                class="form-control mb-2" required>

                                            <label>Address :</label>
                                            <textarea name="address" placeholder="Enter Delivery Address" class="form-control mb-2" rows="3" required></textarea>

                                            <label>Customer Status :</label>
                                            <select name="customer_status" class="form-control mb-2" required>
                                                <option value="">Select Customer Status</option>
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>

                                            </select>

                                            <label>Order Status :</label>
                                            <select name="status" class="form-control mb-2" required>
                                                <option value="">Select Order Status</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Accepted">Accepted</option>
                                                <option value="Rejected">Rejected</option>
                                                <option value="Delivered">Delivered</option>
                                            </select>

                                            <input type="submit" value="Create Order" class="btn btn-primary mt-3">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <br>
                        <p class="card-title mb-0">Order List</p>
                        <div class="table-responsive">
                            <table class="table table-striped table-borderless">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Customer Status</th>
                                        <th>Bill</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Order Status</th>
                                        <th>Order Date</th>
                                        <th>Product</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($Order as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->fullname ?? 'N/A' }}</td>
                                            <td>{{ $item->email ?? 'N/A' }}</td>
                                            <td>
                                                @if ($item->customer_status == 'Active')
                                                    <label class="badge badge-success">Active</label>
                                                @else
                                                    <label class="badge badge-danger">Blocked</label>
                                                @endif
                                            </td>
                                            <td>${{ number_format($item->bill, 2) }}</td>
                                            <td>{{ $item->phone ?? 'N/A' }}</td>
                                            <td>{{ $item->address ?? 'N/A' }}</td>
                                            <td>
                                                @if ($item->status == 'Accepted')
                                                    <label class="badge badge-success">Accepted</label>
                                                @elseif ($item->status == 'Rejected')
                                                    <label class="badge badge-danger">Rejected</label>
                                                @elseif ($item->status == 'Delivered')
                                                    <label class="badge badge-info">Delivered</label>
                                                @else
                                                    <label class="badge badge-warning">Pending</label>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="badge badge-info">
                                                    {{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}
                                                </div>
                                            </td>

                                            <td>
                                                <!-- Product Details Button -->
                                                <button type="button" class="btn btn-primary btn-sm"
                                                    data-toggle="modal" data-target="#productModal{{ $item->id }}">
                                                    View Products
                                                </button>

                                                <!-- Product Details Modal -->
                                                <div class="modal fade" id="productModal{{ $item->id }}"
                                                    tabindex="-1" role="dialog"
                                                    aria-labelledby="productModalLabel{{ $item->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="productModalLabel{{ $item->id }}">
                                                                    Order Products - {{ $item->fullname }}
                                                                </h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered">
                                                                        <thead class="thead-light">
                                                                            <tr>
                                                                                <th>Product</th>
                                                                                <th>Picture</th>
                                                                                <th>Price</th>
                                                                                <th>Quantity</th>
                                                                                <th>Subtotal</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($item->products as $oItem)
                                                                                <tr>
                                                                                    <td>{{ $oItem->title ?? 'N/A' }}
                                                                                    </td>
                                                                                    <td>
                                                                                        @if ($oItem->picture)
                                                                                            <img src="{{ URL::asset('uploads/products/' . $oItem->picture) }}"
                                                                                                alt="Product Image"
                                                                                                width="80"
                                                                                                height="60"
                                                                                                style="object-fit: cover;">
                                                                                        @else
                                                                                            <span class="text-muted">No
                                                                                                Image</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>${{ number_format($oItem->price, 2) }}
                                                                                    </td>
                                                                                    <td>{{ $oItem->quantity }}</td>
                                                                                    <td>${{ number_format($oItem->price * $oItem->quantity, 2) }}
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                @if ($item->status == 'Pending')
                                                    <a href="{{ route('changeOrderStatus', ['status' => 'Accepted', 'id' => $item->id]) }}"
                                                        class="btn btn-success btn-sm">Accept</a>
                                                    <a href="{{ route('changeOrderStatus', ['status' => 'Rejected', 'id' => $item->id]) }}"
                                                        class="btn btn-danger btn-sm">Reject</a>
                                                @elseif ($item->status == 'Accepted')
                                                    <a href="{{ route('changeOrderStatus', ['status' => 'Delivered', 'id' => $item->id]) }}"
                                                        class="btn btn-info btn-sm">Completed</a>
                                                @elseif ($item->status == 'Delivered')
                                                    <span class="badge badge-success">Completed</span>
                                                @else
                                                    <a href="{{ route('changeOrderStatus', ['status' => 'Accepted', 'id' => $item->id]) }}"
                                                        class="btn btn-info btn-sm">Accept</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">No orders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- content-wrapper ends -->
    <x-adminfooter />
