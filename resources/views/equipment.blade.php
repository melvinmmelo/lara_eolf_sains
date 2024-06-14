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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Equipment</li>
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
                <div class="pb-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-equipment">
                        Add New
                    </button>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Model</th>
                            <th>Serial No.</th>
                            <th>Code</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Date Assigned</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipments as $equipment)
                            <tr>
                                <td>{{ $equipment->model }}</td>
                                <td>{{ $equipment->serial_no }}</td>
                                <td>{{ $equipment->code }}</td>
                                <td>{{ $equipment->equipmentStore->customer->fullName ?? '' }}</td>
                                <td>{!! statusBadge($equipment->status) !!}</td>
                                <td>{{ $equipment->equipmentStore->dateCreated ?? '' }}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-btn" data-toggle="modal"
                                        data-target="#edit-equipment"
                                        onclick="setToUpdateEquipment('{{ $equipment->id }}','{{ $equipment->ownership }}','{{ $equipment->type }}','{{ $equipment->brand }}','{{ $equipment->price }}','{{ $equipment->serial_no }}','{{ $equipment->model }}','{{ $equipment->code }}','{{ $equipment->distributor }}','{{ $equipment->date_delivered }}','{{ $equipment->date_purchased }}')">Edit</button>

                                    {{-- <form action="{{ route('equipment.destroy', $equipment->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this equipment?')">Delete</button>
                                    </form> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Model</th>
                            <th>Serial No.</th>
                            <th>Code</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Date Assigned</th>
                            <th></th>
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

                            <input type="hidden" class="form-control" name="branch_code" id="branch_code"
                            value="{{ session('branch_code') }}" required readonly>

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="ownership"><i
                                                style="color:red">*</i>Ownership</label>
                                        <select class="form-control" id="ownership" name="ownership" required>
                                            <option value="Owned">Owned</option>
                                            <option value="Leased">Leased</option>
                                            <option value="Rented">Rented</option>
                                            <!-- Add more options if needed -->
                                        </select>
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label" for="type"><i style="color:red">*</i>Type</label>
                                        <select class="form-control" id="type" name="type" required>
                                            <option value="Hard Top">Hard Top</option>
                                            <option value="Glass Top">Glass Top</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="brand"><i style="color:red">*</i>Brand</label>
                                        <input type="text" class="form-control" id="brand" name="brand" required>
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label" for="model"><i style="color:red">*</i>Model</label>
                                        <select name="model" id="model" class="form-control">
                                            <option value="EFE-3002">EFE-3002</option>
                                            <option value="EFE-3802">EFE-3802</option>
                                            <option value="EFE-4602">EFE-4602</option>
                                            <option value="EFE-5002">EFE-5002</option>
                                            <option value="BD-650">BD-650</option>
                                            <option value="SD-350">SD-350</option>
                                            <option value="SD-450">SD-450</option>
                                            <option value="Fujidenzo">Fujidenzo</option>
                                            <option value="EFL-6005">EFL-6005</option>
                                        </select>
                                    </div>


                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">

                                    <div class="col-sm-6">
                                        <label class="form-label" for="price"><i style="color:red">*</i>Price</label>
                                        <input type="text" pattern="[0-9]*" inputmode="numeric" class="form-control"
                                            id="price" name="price" placeholder="Enter price" required>
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label" for="serial_no"><i style="color:red">*</i>Serial
                                            No.</label>
                                        <input type="text" class="form-control" id="serial_no" name="serial_no">
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="code"><i style="color:red">*</i>Code</label>
                                        <input type="text" class="form-control" id="code" name="code"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" id="distributor" name="distributor">
                            <!-- </div> -->
                            <div class="form-group">
                                <label class="form-label" for="date_delivered"><i style="color:red">*</i>Date
                                    Delivered</label>
                                <input type="date" class="form-control" id="date_delivered" name="date_delivered"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="date_purchased"><i style="color:red">*</i>Date
                                    Purchased</label>
                                <input type="date" class="form-control" id="date_purchased" name="date_purchased"
                                    required>
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
                        <input type="hidden" class="form-control" name="id" id="equipment_id" required readonly>

                        <input type="hidden" class="form-control" name="branch_code" id="branch_code"
                            value="{{ session('branch_code') }}" required readonly>

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
                                        <option value="Hard Top">Hard Top</option>
                                        <option value="Glass Top">Glass Top</option>
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
                                    <label class="form-label" for="e_model">Model</label>
                                    <select name="e_model" id="e_model" class="form-control">
                                        <option value="EFE-3002">EFE-3002</option>
                                        <option value="EFE-3802">EFE-3802</option>
                                        <option value="EFE-4602">EFE-4602</option>
                                        <option value="EFE-5002">EFE-5002</option>
                                        <option value="BD-650">BD-650</option>
                                        <option value="SD-350">SD-350</option>
                                        <option value="SD-450">SD-450</option>
                                        <option value="Fujidenzo">Fujidenzo</option>
                                        <option value="EFL-6005">EFL-6005</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="edit-code">Code</label>
                                    <input type="text" class="form-control" id="edit-code" name="code">
                                </div>
                            </div>
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
        function setToUpdateEquipment(id, ownership, type, brand, price, serial_no, model, code, date_delivered,
            date_purchased) {
            document.getElementById("equipment_id").value = id;
            document.getElementById("edit-ownership").value = ownership;
            document.getElementById("edit-type").value = type;
            document.getElementById("edit-brand").value = brand;
            document.getElementById("edit-price").value = price;
            document.getElementById("edit-serial_no").value = serial_no;
            document.getElementById("e_model").value = model;

            document.getElementById("edit-code").value = code;
            document.getElementById("edit-date_delivered").value = date_delivered;
            document.getElementById("edit-date_purchased").value = date_purchased;
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
