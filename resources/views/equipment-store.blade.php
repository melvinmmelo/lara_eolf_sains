@extends('layouts.app')


@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h4>Customer: <b>{{ request()->query('customer_name') }}</b> <br> Store:
                        <b>{{ request()->query('store_name') }}</b>
                    </h4>
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
                            <!-- <th>ID</th> -->
                            <th>Type</th>
                            <th>Brand</th>
                            <th>Serial</th>
                            <th>Code</th>
                            <th>Owned</th>
                            <th>Date Assigned</th>
                            {{-- <th>Pull Status</th> --}}
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipments as $equipmentSt)
                            <tr>

                                <td>{{ $equipmentSt->type }}</td>
                                <td>{{ $equipmentSt->brand }}</td>
                                <td>{{ $equipmentSt->serial }}</td>
                                <td>{{ $equipmentSt->equipment->code }}</td>
                                <td>{{ $equipmentSt->owned }}</td>
                                <td>{{ $equipmentSt->created_at }}</td>
                                {{-- <td>{{ $equipmentSt->pull_status }}</td> --}}
                                <td>{{ $equipmentSt->remarks }}</td>
                                <td>

                                    <button type="button" class="btn btn-success btn-sm manage-btn" data-toggle="modal"
                                        data-target="#modal-manageequipment" data-serial="{{ $equipmentSt->serial }}"
                                        data-equipment-id="{{ $equipmentSt->equipment_id }}">Manage</button>

                                    <form action="{{ route('equipment-store.destroy', $equipmentSt->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="equipment_id" value="{{ $equipmentSt->equipment_id }}">
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this equipment store entry?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Type</th>
                            <th>Brand</th>
                            <th>Serial</th>
                            <th>Code</th>
                            <th>Owned</th>
                            <th>Date Assigned</th>
                            <th>Remarks</th>
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Equipment</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('equipment-store.store') }}" method="POST">

                            @csrf
                            <input type="hidden" name="customer_id" value="{{ request()->input('customer_id') }}">
                            <input type="hidden" name="store_id" value="{{ request()->input('store_id') }}">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="remarks">Equipment List</label>
                                        <div class="form-group">
                                            <select name="equipment_id[]" class="duallistbox" multiple="multiple" required>
                                                @foreach ($availableEquipments as $equipment)
                                                    <option value="{{ $equipment->id }}">{{ $equipment->code }}</option>
                                                @endforeach
                                            </select>
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
                        <form id="manage-equipment-form" action="{{ route('equipment-store.updatePullStatus') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ request()->query('customer_id') }}">
                            <input type="hidden" name="store_id" value="{{ request()->query('store_id') }}">
                            <input type="hidden" name="pull_equipment_id" id="modal-equipment-id">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label class="form-label"><strong>Name of Customer:</strong></label>
                                        <div>
                                            <label class="form-label"
                                                for="ownership">{{ request()->query('customer_name') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"><strong>Store Name:</strong></label>
                                        <div>
                                            <label class="form-label"
                                                for="ownership">{{ request()->query('store_name') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label"><strong>Address</strong></label>
                                        <div>
                                            <label class="form-label"
                                                for="ownership">{{ $equipments[0]->storeinfo->subdivision ?? '' }},
                                                {{ $equipments[0]->storeinfo->brgy ?? '' }},
                                                {{ $equipments[0]->storeinfo->city ?? '' }}</label>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="card card-primary mb-3">
                                    <div class="card-header">
                                        <h3 class="card-title">Pull Out</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label class="form-label">Model/Serial:</label>
                                            <input type="text" name="serial" class="form-control" id="modal-serial"
                                                readonly>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="status">Remarks</label>
                                            <!-- <input type="text" name="remarks" class="form-control"> -->
                                            <select class="form-control" name="remarks" required>
                                                <option value="DEFFECTIVE COMPRESSOR">DEFFECTIVE COMPRESSOR</option>
                                                <option value="NOT COOLING">NOT COOLING</option>
                                                <option value="STOP SELLING">STOP SELLING</option>
                                                <option value="SYSTEM LEAK">SYSTEM LEAK</option>
                                                <option value="CONDEMNED">CONDEMNED</option>
                                                <option value="RETURN TO SUPPLIER">RETURN TO SUPPLIER</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">Replace</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="replace_equipment_id">Equipment List</label>
                                            <select name="replace_equipment_id[]" class="duallistbox"
                                                multiple="multiple">
                                                @foreach ($availableEquipments as $equipment)
                                                    <option value="{{ $equipment->id }}">{{ $equipment->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save changes</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.manage-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    var serial = this.getAttribute('data-serial');
                    var equipmentId = this.getAttribute('data-equipment-id');
                    document.getElementById('modal-serial').value = serial;
                    document.getElementById('modal-equipment-id').value = equipmentId;
                });
            });
        });


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
