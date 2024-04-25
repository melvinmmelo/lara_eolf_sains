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

        <!-- Default box -->
        <div class="card">

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Owned</th>
                            <th>Type</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Serial No.</th>
                            <th>Code</th>
                            <th>Date Assigned</th>
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
                            <td>aaa</td>
                            <td>aaa</td>
                            <td>aaa</td>



                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Owned</th>
                            <th>Type</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th>Serial No.</th>
                            <th>Code</th>
                            <th>Date Assigned</th>
                            <th>Status</th>

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

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="price_code">Ownership</label>
                                    <select class="form-control" id="price_code">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                        <option>option 5</option>
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label" for="price_unit">Type</label>
                                    <select class="form-control" id="price_unit">
                                        <option>option 1</option>
                                        <option>option 2</option>
                                        <option>option 3</option>
                                        <option>option 4</option>
                                        <option>option 5</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="price-quantity">Brand</label>
                                    <input type="text" class="form-control" id="cust_lname" name="lastname">
                                </div>


                                <div class="col-sm-6">
                                    <label class="form-label" for="price">Price</label>
                                    <input type="text" class="form-control" id="cust_lname" name="lastname">
                                </div>


                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-8">
                                    <label class="form-label" for="price-quantity">Serial No.</label>
                                    <input type="text" class="form-control" id="cust_lname" name="lastname">
                                </div>


                                <div class="col-sm-4">
                                    <label class="form-label" for="price">Code</label>
                                    <input type="text" class="form-control" id="cust_lname" name="lastname">
                                </div>


                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <label class="form-label" for="price-quantity">Distributor</label>
                                    <input type="text" class="form-control" id="cust_lname" name="lastname">
                                </div>

                            </div>
                        </div>


                        <div class="form-group">
                            <label class="form-label">Date Delivered</label>
                            <div class="input-group">
                                <input type="date" class="form-control float-right">

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Date Purchased</label>
                            <div class="input-group">
                                <input type="date" class="form-control float-right">

                            </div>
                        </div>




                        <div class="modal-footer">
                            <button type="button" class="btn btn-success swalDefaultSuccess">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->



        </div>
    </section>


    <!-- /.content -->
@endsection
