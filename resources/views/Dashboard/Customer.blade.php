<x-adminheader />


<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">

            </div>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <!-- Button to Open the Modal -->
                        {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNewModal">
                            Add New User
                        </button> --}}

                        <!-- The Modal -->
                        {{-- <div class="modal" id="addNewModal">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                
                                    <div class="modal-header">
                                        <h4 class="modal-title">Add New User</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                           
                                    <div class="modal-body">
                                        <form action="{{ URL::to('AddNewCustomer') }}" method="POST">
                                            @csrf

                                            <label>Full Name :</label>
                                            <input type="text" name="fullname" placeholder="Enter Full Name"
                                                class="form-control mb-2" required>

                                            <label>Email :</label>
                                            <input type="email" name="email" placeholder="Enter Email Address"
                                                class="form-control mb-2" required>

                                            <label>Role :</label>
                                            <select name="role" class="form-control mb-2" required>
                                                <option value="">Select role</option>
                                                <option value="Admin">Admin</option>
                                                <option value="Customer">Customer</option>

                                            </select>

                                            <label>Status :</label>
                                            <select name="status" class="form-control mb-2" required>
                                                <option value="">Select Status</option>
                                                <option value="Active">Active</option>
                                                <option value="Blocked">Blocked</option>

                                            </select>

                                            <input type="submit" value="Save Customer" class="btn btn-primary mt-3">
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <p class="card-title mb-0">Customer List</p>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-striped table-borderless">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>E-mail</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th>Update</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->fullname }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->role }}</td>
                                            <td>{{ $item->created_at }}</td>
                                            <td>
                                                @if ($item->status == 'Active')
                                                    <label class="badge badge-success">Active</label>
                                                @else
                                                    <label class="badge badge-danger">Blocked</label>
                                                @endif

                                            <td>
                                                <div class="d-flex flex-column flex-md-row gap-2">
                                                    @if ($item->status == 'Active')
                                                        <a class="btn btn-sm btn-danger"
                                                            href="{{ URL::to('changeCustomerStatus/Blocked/' . $item->id) }}">
                                                            Block
                                                        </a>
                                                    @else
                                                        <a class="btn btn-sm btn-info"
                                                            href="{{ URL::to('changeCustomerStatus/Active/' . $item->id) }}">
                                                            Unblock
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Button to open modal -->
                                                <button type="button"
                                                    class="btn {{ $item->status == 'Block' ? 'btn-primary' : 'btn-primary' }}"
                                                    data-toggle="modal" data-target="#updateModal{{ $item->id }}">
                                                    Update
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal" id="updateModal{{ $item->id }}">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Update Customer</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ url('UpdateCustomer') }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="id"
                                                                        value="{{ $item->id }}">

                                                                    <label>Name:</label>
                                                                    <input type="text" name="fullname"
                                                                        value="{{ $item->fullname }}"
                                                                        class="form-control mb-2">

                                                                    <label>Email:</label>
                                                                    <input type="text" name="email"
                                                                        value="{{ $item->email }}"
                                                                        class="form-control mb-2">

                                                                    <label>Role:</label>
                                                                    <select name="role" class="form-control mb-2">
                                                                        <option value="Admin"
                                                                            {{ $item->role == 'Admin' ? 'selected' : '' }}>
                                                                            Admin</option>
                                                                        <option value="Customer"
                                                                            {{ $item->role == 'Customer' ? 'selected' : '' }}>
                                                                            Customer</option>
                                                                    </select>

                                                                    <label>Status:</label>
                                                                    <select name="status" class="form-control mb-2">
                                                                        <option value="Active"
                                                                            {{ $item->status == 'Active' ? 'selected' : '' }}>
                                                                            Active</option>
                                                                        <option value="Block"
                                                                            {{ $item->status == 'Block' ? 'selected' : '' }}>
                                                                            Block</option>
                                                                    </select>

                                                                    <button type="submit"
                                                                        class="btn btn-success mt-2">Save
                                                                        Changes</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <!-- content-wrapper ends -->
    <!-- partial:partials/_footer.html -->
    <x-adminfooter />
