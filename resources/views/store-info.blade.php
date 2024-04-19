@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Store Info</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Store Info</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Store List</h3>

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
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Branch</th>
                            <th>Contact No.</th>
                            <th>Address</th>


                        </tr>
                    </thead>
                    <tbody>
                        <tr>
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

                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Code</th>
                            <th>Branch</th>
                            <th>Contact No.</th>
                            <th>Address</th>


                        </tr>
                    </tfoot>
                </table>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#storeModal">Add Store
                    Info</button>
            </div>

            <!-- /.card-footer-->
        </div>
        <div class="modal fade" id="storeModal" tabindex="-1" aria-labelledby="storeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="storeModalLabel">Store Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-8">
                                        <label class="form-label" for="cust_lname">Store Name:</label>
                                        <input type="text" class="form-control" id="cust_lname">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label" for="cust_contact">Contact No.:</label>
                                        <input type="text" class="form-control" id="cust_contact">
                                    </div>
                                </div>
                            </div>



                            <h6>Store Address</h6>
                            <hr class="dotted-hr">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label class="form-label" for="cust_region">Region</label>
                                        <select class="form-control" id="cust_region">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="cust_prov">Province</label>
                                        <select class="form-control" id="cust_prov">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label" for="cust_city">City</label>
                                        <select class="form-control" id="cust_city">
                                            <option>option 1</option>
                                            <option>option 2</option>
                                            <option>option 3</option>
                                            <option>option 4</option>
                                            <option>option 5</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <label class="form-label" for="cust_brgy">Barangay</label>
                                            <select class="form-control" id="cust_brgy">
                                                <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="cust_subd">Subdivision:</label>
                                            <input type="text" class="form-control" id="cust_subd">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="cust_lat">Latitude:</label>
                                            <input type="text" class="form-control" id="cust_lat">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label" for="cust_long">Longitude:</label>
                                            <input type="text" class="form-control" id="cust_long">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <label for="store_list">List Type:</label>
                                            <select class="form-control" id="store_region">
                                                <option>option 1</option>
                                                <option>option 2</option>
                                                <option>option 3</option>
                                                <option>option 4</option>
                                                <option>option 5</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="store_length">Length of Stay:</label>
                                            <select class="form-control" id="store_prov">
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
                                        <div class="col-sm-12">
                                            <label for="store_remarks">Remarks</label>
                                            <textarea class="form-control" rows="3" id="store_remarks"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success swalDefaultSuccess">Save
                                    changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
@endsection
