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
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNewModal">
                            Add New Products
                        </button>

                        <!-- The Modal -->
                        <div class="modal" id="addNewModal">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">Add New Product</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <!-- Modal body -->
                                    {{-- Insert Form --}}
                                    <div class="modal-body">
                                        <form action="{{ URL::to('AddNewProduct') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf

                                            <label>Title :</label>
                                            <input type="text" name="title" placeholder="Enter The Title"
                                                class="form-control mb-2" required>

                                            <label>Image :</label>
                                            <input type="file" name="image"
                                                class="form-control mb-2"id="input-file" accept="image/*" required>

                                            <label>Description : </label>
                                            <input type="text" name="description" placeholder="Enter The description"
                                                class="form-control mb-2" required>


                                            <label>Price :</label>
                                            <input type="text" name="price" placeholder="Enter The Price ($)"
                                                class="form-control mb-2" required>

                                            <label>Quantity :</label>
                                            <input type="number" name="quantity" placeholder="Enter The Quantity"
                                                class="form-control mb-2" required>

                                            <label>Category :</label>
                                            <select name="category" class="form-control" id="">
                                                <option value="">Select Category</option>
                                                <option value="Accessories">Accessories</option>
                                                <option value="Shoes">Shoes</option>
                                                <option value="Clothes">Clothes</option>

                                            </select>

                                            <label>Type :</label>
                                            <select name="type" class="form-control" id="">
                                                <option value="">Select Type</option>
                                                <option value="Best Sellers">Best Sellers</option>
                                                <option value="new-arrivals">New Arrivals</option>
                                                <option value="sale">Sale</option>

                                            </select>

                                            <input type="submit" value="Save Now" class="btn btn-primary mt-3"
                                                id="">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <br>
                        <p class="card-title mb-0">Top Products</p>
                        <div class="table-responsive">
                            <table class="table table-striped table-borderless">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Image</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Update</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 0;
                                    @endphp
                                    @foreach ($products as $item)
                                        @php
                                            $i++;
                                        @endphp
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $item->title }}</td>
                                            <td>
                                                @if (Str::startsWith($item->image, ['http://', 'https://']))
                                                    <img src="{{ $item->image }}" alt="Product Image"
                                                        class="img-thumbnail" style="width: 80px; height: auto;">
                                                @else
                                                    <img src="{{ asset($item->image) }}" alt="Product Image"
                                                        class="img-thumbnail" style="width: 80px; height: auto;">
                                                @endif
                                            </td>
                                            <td>{{ $item->price }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>
                                                <div class="btn btn-sm btn-success">
                                                    {{ $item->category }}
                                                </div>

                                            </td>
                                            <td>
                                                <div class="btn btn-sm btn-info">
                                                    {{ $item->type }}
                                                </div>
                                            </td>
                                            <td>
                                                <!-- Button to Open the Modal -->
                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                    data-target="#updateModal{{ $i }}">
                                                    Update
                                                </button>

                                                <!-- The Modal -->
                                                <div class="modal" id="updateModal{{ $i }}">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">

                                                            <!-- Modal Header -->
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Update Product</h4>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">&times;</button>
                                                            </div>

                                                            <!-- Modal body -->
                                                            <div class="modal-body">

                                                                {{-- Update Form --}}
                                                                <form action="{{ url('UpdateProduct') }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @csrf

                                                                    <label>Title :</label>
                                                                    <input type="text" name="title"
                                                                        value="{{ $item->title }}"
                                                                        placeholder="Enter The Title"
                                                                        class="form-control mb-2" required>

                                                                    <label>Image :</label>
                                                                    @if ($item->image)
                                                                        <img src="{{ asset($item->image) }}"
                                                                            alt="Product Image" width="100"
                                                                            class="mb-2">
                                                                    @endif

                                                                    <input type="file" name="image"
                                                                        class="form-control mb-2" accept="image/*">

                                                                    <label>Description : </label>
                                                                    <input type="text" name="description"
                                                                        value="{{ $item->description }}"
                                                                        placeholder="Enter The Description"
                                                                        class="form-control mb-2" required>

                                                                    <label>Price :</label>
                                                                    <input type="text" name="price"
                                                                        value="{{ $item->price }}"
                                                                        placeholder="Enter The Price ($)"
                                                                        class="form-control mb-2" required>

                                                                    <label>Quantity :</label>
                                                                    <input type="number" name="quantity"
                                                                        value="{{ $item->quantity }}"
                                                                        placeholder="Enter The Quantity"
                                                                        class="form-control mb-2" required>

                                                                    <label>Category :</label>
                                                                    <select name="category" class="form-control"
                                                                        required>
                                                                        <option value="{{ $item->category }}">
                                                                            {{ $item->category }}</option>
                                                                        <option value="Accessories">Accessories
                                                                        </option>
                                                                        <option value="Shoes">Shoes</option>
                                                                        <option value="Clothes">Clothes</option>
                                                                    </select>

                                                                    <label>Type :</label>
                                                                    <select name="type" class="form-control"
                                                                        required>
                                                                        <option value="{{ $item->type }}">
                                                                            {{ $item->type }}</option>
                                                                        <option value="Best Sellers">Best Sellers
                                                                        </option>
                                                                        <option value="New Arrivals">New Arrivals
                                                                        </option>
                                                                        <option value="Sale">Sale</option>
                                                                    </select>

                                                                    <input type="hidden" name="id"
                                                                        value="{{ $item->id }}">

                                                                    <input type="submit" value="Save Changes"
                                                                        class="btn btn-primary mt-3">
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ URL::to('deleteProduct/' . $item->id) }}"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirmDelete('{{ $item->id }}')">
                                                    Delete
                                                </a>
                                            </td>

                                            <script>
                                                function confirmDelete(productId) {
                                                    if (confirm('Are you sure you want to delete this product?')) {
                                                        window.location.href = "{{ URL::to('deleteProduct/') }}" + productId;
                                                        return true;
                                                    } else {
                                                        return false;
                                                    }
                                                }
                                            </script>

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
