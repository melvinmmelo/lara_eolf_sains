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
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Vehicles Info</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">

                @include('layouts.errors')

                <table id="example1" class="table table-bordered table-striped">
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
                <form method="POST">

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
                                        <label class="form-label" for="plate_no">Plate No.</label>
                                        <input type="text" class="form-control" name="plate_no">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="brand">Brand</label>
                                        <input type="text" class="form-control" name="brand">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="email">Description</label>
                                        <input type="text" class="form-control" name="desc">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row mb-3">
                                    <div class="col-sm-4">
                                        <label class="form-label" for="type">Type</label>
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
                                        <input type="text" class="form-control" name="desc">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="status">Status</label>
                                        <br>
                                        <input type="checkbox" id="mySwitch" data-bootstrap-switch data-on-text="On"
                                            data-off-text="Off" data-on-color="success" data-off-color="danger">

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
