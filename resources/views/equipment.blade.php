@extends('layouts.app')


@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Equipments</li>
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
                    @if (session('success') == 'Equipment deleted successfully!')
                        icon = 'error'; // Set icon to 'error' if message is for deletion
                    @elseif (session('success') == 'Equipment updated successfully!')
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
            @include('layouts.errors')
            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Costumer</th>
                            <th>Store</th>
                            <th>Type</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Owned</th>
                            <th>Date Assigned</th>
                            <th>Status</th>
                            <th>Action</th>



                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>
                            <td><button type="submit" class="btn btn-success btn-sm" data-toggle="modal"
                                    data-target="#modal-manageequipment">Manage</button></td>


                        </tr>


                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Costumer</th>
                            <th>Store</th>
                            <th>Type</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Owned</th>
                            <th>Date Assigned</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>
                    </tfoot>
                </table>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-equipment">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->
        <div class="modal fade" id="modal-equipment">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Equipment</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('equipment.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="ownership">Ownership</label>
                                        <select class="form-control" id="ownership" name="ownership">
                                            <option value="Owned">Owned</option>
                                            <option value="Leased">Leased</option>
                                            <option value="Rented">Rented</option>
                                            <!-- Add more options if needed -->
                                        </select>
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label" for="type">Type</label>
                                        <select class="form-control" id="type" name="type">
                                            <option value="Upright Freezer">Upright Freezer</option>
                                            <option value="Chest Freezer">Chest Freezer</option>
                                            <option value="Drawer Freezer">Drawer Freezer</option>
                                            <option value="Commercial Freezer">Commercial Freezer</option>
                                            <!-- Add more options if needed -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="brand">Brand</label>
                                        <input type="text" class="form-control" id="brand" name="brand">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="price">Price</label>
                                        <!-- <input type="text" class="form-control" id="price" name="price"> -->
                                        <input type="text" pattern="[0-9]*" inputmode="numeric" class="form-control"
                                            id="price" name="price" placeholder="Enter price" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="serial_no">Serial No.</label>
                                        <input type="text" class="form-control" id="serial_no" name="serial_no">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="code">Code</label>
                                        <input type="text" class="form-control" id="code" name="code">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="distributor">Distributor</label>
                                <input type="text" class="form-control" id="distributor" name="distributor">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="date_delivered">Date Delivered</label>
                                <input type="date" class="form-control" id="date_delivered" name="date_delivered">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="date_purchased">Date Purchased</label>
                                <input type="date" class="form-control" id="date_purchased" name="date_purchased">
                            </div>
                    </div>
                    <!-- /.modal-body -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save changes</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <div class="modal fade" id="modal-manageequipment">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Manage Equipment</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('equipment.store') }}" method="POST">
                            @csrf
                            <div class="form-group">

                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label"><strong>Name of Customer:</strong></label>
                                        <div><label class="form-label" for="ownership">Name of Customer:</label></div>

                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label"><strong>Store:</strong></label>
                                        <div><label class="form-label" for="ownership">Name of Customer:</label></div>

                                    </div>



                                </div>
                            </div>
                            <hr>
                            <div class="form-group">

                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label"><strong>Address</strong></label>
                                        <div><label class="form-label" for="ownership">Address:</label></div>
                                        <hr>

                                    </div>


                                </div>
                            </div>

                            <div class="form-group">


                                <div class="row mb-2">
                                    <div class="col-sm-6">

                                        <div class="card card-primary collapsed-card">
                                            <div class="card-header">
                                                <h3 class="card-title">Pull Out</h3>

                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool"
                                                        data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <!-- /.card-tools -->
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <label class="form-label">Model/Serial:</label>
                                                        <input type="text" class="form-control" id=""
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-sm-12">

                                                        <label class="form-label" for="status">Pull out
                                                            Equipment</label>
                                                        <br>
                                                        <input type="checkbox" id="mySwitch" data-bootstrap-switch
                                                            data-on-text="Yes" data-off-text="No" data-on-color="success"
                                                            data-off-color="danger" name="status">
                                                    </div>

                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                        </div>


                                    </div>

                                    <div class="col-sm-6">

                                        <div class="card card-primary collapsed-card">
                                            <div class="card-header">
                                                <h3 class="card-title">Replace</h3>

                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool"
                                                        data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                                <!-- /.card-tools -->
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label class="form-label">Model/Serial</label>
                                                    <select class="form-control select2bs4" style="width: 100%;">
                                                        <option selected="selected">Alabama</option>
                                                        <option>Alaska</option>
                                                        <option>California</option>
                                                        <option>Delaware</option>
                                                        <option>Tennessee</option>
                                                        <option>Texas</option>
                                                        <option>Washington</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">Freezer Status</label>

                                                    <select name="distributor" class="form-control d-block"
                                                        id="cust_dist" onfocus="changeColor('cust_dist')"
                                                        onblur="resetColor('cust_dist')">
                                                        <option>option 1</option>
                                                        <option>option 2</option>
                                                        <option>option 3</option>
                                                        <option>option 4</option>
                                                        <option>option 5</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                        </div>


                                    </div>

                                </div>
                            </div>
                    </div>
                    <!-- /.modal-body -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save changes</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->




    </section>


    <div class="modal fade" id="edit-equipment">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Equipment</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="{{ route('equipment.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="text" class="form-control" name="id" id="equipment_id" required
                            readonly><br>
                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="edit-ownership">Ownership</label>
                                    <select class="form-control" id="edit-ownership" name="ownership">
                                        <option value="Owned">Owned</option>
                                        <option value="Leased">Leased</option>
                                        <option value="Rented">Rented</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="edit-type">Type</label>
                                    <select class="form-control" id="edit-type" name="type">
                                        <option value="Upright Freezer">Upright Freezer</option>
                                        <option value="Chest Freezer">Chest Freezer</option>
                                        <option value="Drawer Freezer">Drawer Freezer</option>
                                        <option value="Commercial Freezer">Commercial Freezer</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="edit-brand">Brand</label>
                                    <input type="text" class="form-control" id="edit-brand" name="brand">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="edit-price">Price</label>
                                    <input type="text" pattern="[0-9]*" inputmode="numeric" class="form-control"
                                        id="edit-price" name="price" placeholder="Enter price" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="edit-serial_no">Serial No.</label>
                                    <input type="text" class="form-control" id="edit-serial_no" name="serial_no">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="edit-code">Code</label>
                                    <input type="text" class="form-control" id="edit-code" name="code">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit-distributor">Distributor</label>
                            <input type="text" class="form-control" id="edit-distributor" name="distributor">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit-date_delivered">Date Delivered</label>
                            <input type="date" class="form-control" id="edit-date_delivered" name="date_delivered">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="edit-date_purchased">Date Purchased</label>
                            <input type="date" class="form-control" id="edit-date_purchased" name="date_purchased">
                        </div>

                </div>
                <!-- /.modal-body -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save changes</button>
                </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>



    <!-- /.content -->
@endsection


@section('custom_js')
    <script>
        function setToUpdateEquipment(id, ownership, type, brand, price, serial_no, code, distributor, date_delivered,
            date_purchased) {
            document.getElementById("equipment_id").value = id;
            document.getElementById("edit-ownership").value = ownership;
            document.getElementById("edit-type").value = type;
            document.getElementById("edit-brand").value = brand;
            document.getElementById("edit-price").value = price;
            document.getElementById("edit-serial_no").value = serial_no;
            document.getElementById("edit-code").value = code;
            document.getElementById("edit-distributor").value = distributor;
            document.getElementById("edit-date_delivered").value = date_delivered;
            document.getElementById("edit-date_purchased").value = date_purchased;

            // Open the modal
            // $('#edit-equipment').modal('show');
        }
    </script>



    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script>
        $(document).ready(function() {
            $('#price').on('input', function() {
                var input = $(this);
                var regex = /^[0-9]*$/;
                if (!regex.test(input.val())) {
                    input.addClass('is-invalid');
                } else {
                    input.removeClass('is-invalid');
                }
            });
        });
    </script>
@endsection
