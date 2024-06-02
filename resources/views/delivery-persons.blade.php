@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Delivery Persons</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Delivery persons</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">

            <div class="card-body">

                @include('layouts.errors')

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Contact No.</th>
                            <th>Status</th>
                            <th>Price Level</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($drivers as $driver)
                            <tr>
                                <td>{{ $driver->name }}</td>
                                <td>{{ $driver->address }}</td>
                                <td>{{ $driver->contact }}</td>
                                <td>{{ $driver->status }}</td>
                                <td>{{ $driver->priceLevel->pl_name }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#modal-edit" onclick="setToUpdate('{{ $driver->id }}',
                                        '{{ $driver->name }}', '{{ $driver->address }}', '{{ $driver->contact }}',
                                        '{{ $driver->status }}')">Edit</button>

                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Contact No.</th>
                            <th>Status</th>
                            <th>Price Level</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>


            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-devpersons">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->


        <div class="modal
                    fade" id="modal-devpersons">
            <div class="modal-dialog">
                <form method="POST" action="/Drivers/store">
                    @csrf

                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add delivery person</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="name"><i style="color:red">*</i>Name</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address"><i style="color:red">*</i>Address</label>
                                        <input type="text" class="form-control" name="address" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="contact"><i style="color:red">*</i>Contact
                                            No.</label>
                                        <input type="text" class="form-control" name="contact" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="contact"><i style="color:red">*</i>Default Price Level
                                            </label>
                                        <select class="form-control" name="price_level" required>
                                            @foreach ($priceLevels as $priceLevel)
                                                <option value="{{ $priceLevel->id }}">{{ $priceLevel->pl_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="status">Status</label>
                                        <br>
                                        <input type="checkbox" id="mySwitch" data-bootstrap-switch data-on-text="On"
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
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->

                </form>
            </div>
        </div>

        <div class="modal
                    fade" id="modal-edit">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('delivery-person.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit delivery person</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_name"><i style="color:red">*</i>Name</label>
                                        <input type="hidden" class="form-control" name="e_id" required readonly>
                                        <input type="text" class="form-control" name="e_name" required>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_address"><i style="color:red">*</i>Address</label>
                                        <input type="text" class="form-control" name="e_address" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_contact"><i style="color:red">*</i>Contact
                                            No.</label>
                                        <input type="text" class="form-control" name="e_contact" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="contact"><i style="color:red">*</i>Default Price Level
                                            </label>
                                        <select class="form-control" name="e_price_level" required>
                                            @foreach ($priceLevels as $priceLevel)
                                                <option value="{{ $priceLevel->id }}">{{ $priceLevel->pl_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_status">Status</label>
                                        <br>
                                        <input type="checkbox" id="mySwitch" data-bootstrap-switch data-on-text="On"
                                            data-off-text="Off" data-on-color="success" data-off-color="danger"
                                            name="e_status">

                                        <div style="margin-bottom: 20px"></div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Save
                                        changes</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->

                </form>
            </div>
        </div>

    </section>



    <!-- /.content -->
@endsection

@section('custom_js')
    <script>
        function setToUpdate(id, name, address, contact, status) {
            $('input[name="e_id"]').val(id);
            $('input[name="e_name"]').val(name);
            $('input[name="e_address"]').val(address);
            $('input[name="e_contact"]').val(contact);
            if (status == 'Active') {
                $('input[name="e_status"]').bootstrapSwitch('state', true);
            } else {
                $('input[name="e_status"]').bootstrapSwitch('state', false);
            }
        }
    </script>
@endsection
