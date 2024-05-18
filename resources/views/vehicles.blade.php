@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Vehicles</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @if (session('success'))
            <script>
                // JavaScript code to trigger SweetAlert pop-up message
                document.addEventListener('DOMContentLoaded', function() {
                    // Set default icon
                    let icon = 'success';

                    // Check if success message is "Customer deleted successfully!"
                    @if (session('success') == 'Vehicle deleted successfully!')
                        icon = 'error'; // Set icon to 'error' if message is for deletion
                    @elseif (session('success') == 'Vehicle updated successfully!')
                        icon = 'success'; // Set icon to 'success' if message is for update
                    @endif

                    // Show SweetAlert pop-up message with the determined icon
                    Swal.fire({
                        icon: icon,
                        title: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>
        @endif
        <!-- Default box -->
        <div class="card">

            <div class="card-body">

                @include('layouts.errors')

                <!-- <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Plate No.</th>
                                            <th>Brand</th>
                                            <th>Type</th>
                                            <th>Capacity</th>
                                            <th>Status</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>aaa</td>
                                            <td>aaa</td>
                                            <td>aaa</td>
                                            <td>aaa</td>
                                            <td>aaa</td>

                                        </tr>

                                        <tr>
                                            <td>aaa</td>
                                            <td>aaa</td>
                                            <td>aaa</td>
                                            <td>aaa</td>
                                            <td>aaa</td>


                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Plate No.</th>
                                            <th>Brand</th>
                                            <th>Type</th>
                                            <th>Capacity</th>
                                            <th>Status</th>

                                        </tr>
                                    </tfoot>
                                </table> -->
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Plate No.</th>
                            <th>Brand</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehicles as $vehicle)
                            <tr>
                                <td>{{ $vehicle->id }}</td>
                                <td>{{ $vehicle->plateno }}</td>
                                <td>{{ $vehicle->brand }}</td>
                                <td>{{ $vehicle->type }}</td>
                                <td>{{ $vehicle->size }}</td>
                                <td>{{ $vehicle->capacity }}</td>
                                <td>{{ $vehicle->status }}</td>
                                <td>
                                    <!-- <a class="btn btn-success btn-sm" href="{{ route('vehicle.edit', $vehicle->id) }}">Edit</a> -->
                                    <button class="btn btn-primary btn-sm edit-btn" data-toggle="modal"
                                        data-target="#editVehicle"
                                        onclick="setToUpdateVehicle('{{ $vehicle->id }}','{{ $vehicle->plateno }}','{{ $vehicle->brand }}','{{ $vehicle->description }}','{{ $vehicle->type }}','{{ $vehicle->size }}','{{ $vehicle->capacity }}','{{ $vehicle->remarks }}','{{ $vehicle->status }}')">Edit</button>
                                    <form method="POST" action="{{ route('vehicle.destroy', $vehicle->id) }}"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-vehicle">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->


        <div class="modal fade" id="modal-vehicle">
            <div class="modal-dialog">
                <form method="POST" action="/vehicles/store">
                    @csrf

                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Vehicle</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="plate_no"><i style="color:red">*</i>Plate No.</label>
                                        <input type="text" class="form-control" name="plateno" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="brand"><i style="color:red">*</i>Brand</label>
                                        <input type="text" class="form-control" name="brand" required>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="email">Description</label>
                                        <input type="text" class="form-control" name="description">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="form-label" for="type"><i style="color:red">*</i>Type</label>
                                        <select class="form-control d-block" name="type">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-4">
                                        <label class="form-label" for="size">Size</label>
                                        <select class="form-control d-block" name="size">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label" for="capacity">Capacity</label>
                                        <input type="text" class="form-control" name="capacity">
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="email">Remarks</label>
                                        <input type="text" class="form-control" name="remarks">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="status">Status</label>
                                        <br>
                                        <input type="checkbox" id="mySwitch" data-bootstrap-switch
                                            data-on-text="Available" data-off-text="Off" data-on-color="success"
                                            data-off-color="danger" name="status">

                                        <div style="margin-bottom: 20px"></div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Save
                                        changes</button>
                                </div>
                            </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        </form>
        </div>
        </div>

    </section>

    <!-- Edit Vehicle Modal -->
    <div class="modal fade" id="editVehicle" tabindex="-1" aria-labelledby="editCustomerModalLabel"
        aria-hidden="true">
        <!-- <div class="modal fade" id="modal-vehicle"> -->
        <div class="modal-dialog">
            <form id="editVehicleForm" method="POST" action="{{ route('vehicle.update') }}">
                @csrf
                @method('PATCH')

                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Vehicle</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" class="form-control" name="id" id="id" required readonly><br>
                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="plateno">Plate No.</label>
                                    <input type="text" class="form-control" name="plateno" id="plateno">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="brand">Brand</label>
                                    <input type="text" class="form-control" name="brand" id="brand">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="form-label" for="description">Description</label>
                                    <input type="text" class="form-control" name="description" id="description">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label class="form-label" for="type">Type</label>
                                    <select class="form-control d-block" name="type" id="type">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                        <option>option 5</option>
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label" for="size">Size</label>
                                    <select class="form-control d-block" name="size" id="size">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                        <option>option 5</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label" for="capacity">Capacity</label>
                                    <input type="text" class="form-control" name="capacity" id="capacity">
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="form-label" for="email">Remarks</label>
                                    <input type="text" class="form-control" name="remarks" id="remarks">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="form-label" for="status">Status</label>
                                    <br>
                                    <input type="checkbox" id="mySwitch" data-bootstrap-switch data-on-text="Available"
                                        data-off-text="Off" data-on-color="success" data-off-color="danger"
                                        name="status">

                                    <div style="margin-bottom: 20px"></div>

                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save
                                    changes</button>
                            </div>
                        </div>
            </form>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    </form>
    </div>
    </div>

    <!-- /.content -->
@endsection


@section('custom_js')
    <script>
        function setToUpdateVehicle(uid, plateno, brand, description, type, size, capacity, remarks, status) {
            document.getElementById("id").value = uid;
            document.getElementById("plateno").value = plateno;
            document.getElementById("brand").value = brand;
            document.getElementById("description").value = description;
            document.getElementById("type").value = type;
            document.getElementById("size").value = size;
            document.getElementById("capacity").value = capacity;
            document.getElementById("remarks").value = remarks;
            document.getElementById("status").value = status;


        }
    </script>
@endsection
