@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Customers Info</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Customer Info</li>
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
                            <th>Contact Nos</th>
                            <th>Address</th>
                            <th>Store Name</th>
                            <th>Equipments</th>
                            <th>Store Address</th>
                            <th>Status</th>
                            <th>Action</th>


                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>


                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Contact Nos</th>
                            <th>Address</th>
                            <th>Store Name</th>
                            <th>Equipments</th>
                            <th>Store Address</th>
                            <th>Status</th>
                            <th>Action</th>

                        </tr>

                    </tfoot>
                </table>


            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-customer">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->


        <div class="modal fade custom-modal" id="modal-customer">
            <div class="modal-dialog modal-xl">
                <form method="POST" action="#">
                    @csrf

                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Customer Info</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">



                            <div class="row mb-2">
                                {{-- customer info card --}}
                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-header bg-primary">
                                            Customer
                                        </div>
                                        <div class="card-body">

                                            <div class="form-group">
                                                <div class="row mb-3">
                                                    <div class="col-sm-4">
                                                        <label class="form-label" for="cust_lname">Last Name:</label>
                                                        <input type="text" class="form-control" id="cust_lname"
                                                            name="lastname">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label" for="cust_fname">First Name:</label>
                                                        <input type="text" class="form-control" id="cust_fname"
                                                            name="firstname">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label" for="cust_mname">Middle Name:</label>
                                                        <input type="text" class="form-control" id="cust_mname"
                                                            name="middlename">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_contact">Contact No.:</label>
                                                        <input type="text" class="form-control" id="cust_contact"
                                                            name="contact_no">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_comp">Company Name:</label>
                                                        <input type="text" class="form-control" id="cust_comp"
                                                            name="companyname">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_tin">TIN:</label>
                                                        <input type="text" class="form-control" id="cust_tin"
                                                            name="tin">
                                                    </div>
                                                </div>
                                            </div>
                                            <h6> Residential Address </h6>
                                            <hr>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_region">Region</label>
                                                        <select class="form-control" id="cust_region" name="region">
                                                            <!-- <option></option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_prov">Province</label>
                                                        <select class="form-control" id="cust_prov" name="province">
                                                            <!-- <option></option> -->
                                                            <!-- <option>option 1</option>
                                                                                <option>option 2</option>
                                                                                <option>option 3</option>
                                                                                <option>option 4</option>
                                                                                <option>option 5</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_city">City</label>
                                                        <select class="form-control" id="cust_city" name="city">
                                                            <!-- <option></option> -->
                                                            <!-- <option>option 1</option>
                                                                                <option>option 2</option>
                                                                                <option>option 3</option>
                                                                                <option>option 4</option>
                                                                                <option>option 5</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_brgy">Barangay</label>
                                                        <select class="form-control" id="cust_brgy" name="brgy">
                                                            <!-- <option></option> -->
                                                            <!-- <option>option 1</option>
                                                                                    <option>option 2</option>
                                                                                    <option>option 3</option>
                                                                                    <option>option 4</option>
                                                                                    <option>option 5</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_subd">Subdivision</label>
                                                        <input type="text" class="form-control" id="cust_subd"
                                                            name="subdivision">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="latitude">Latitude</label>
                                                        <input type="text" class="form-control" name="latitude">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="longitude">Longitude</label>
                                                        <input type="text" class="form-control" name="longitude">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                {{-- Store Info card --}}




                                <div class="col-sm-6">
                                    <div class="card">
                                        <div class="card-header bg-primary">
                                            Store
                                        </div>
                                        <div class="card-body">


                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="storename">Store Name</label>
                                                        <input type="text" class="form-control" name="storename">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="name">Contact No.</label>
                                                        <input type="text" class="form-control" name="contactno">
                                                    </div>
                                                </div>
                                            </div>


                                            <h6> Store Address </h6>
                                            <hr>
                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="region">Region</label>
                                                        <select class="form-control" id="cust_region" name="region">
                                                            <!-- <option>option 1</option>
                                                                                                                                                <option>option 2</option>
                                                                                                                                                <option>option 3</option>
                                                                                                                                                <option>option 4</option>
                                                                                                                                                <option>option 5</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="name">Province</label>
                                                        <select class="form-control" id="cust_prov" name="province">
                                                            <!-- <option>option 1</option>
                                                                                                                                                                    <option>option 2</option>
                                                                                                                                                                    <option>option 3</option>
                                                                                                                                                                    <option>option 4</option>
                                                                                                                                                                    <option>option 5</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="name">City</label>
                                                        <select class="form-control" id="cust_city" name="city">
                                                            <!-- <option>option 1</option>
                                                                                                                                                                    <option>option 2</option>
                                                                                                                                                                    <option>option 3</option>
                                                                                                                                                                    <option>option 4</option>
                                                                                                                                                                    <option>option 5</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="name">Barangay</label>
                                                        <select class="form-control" id="cust_brgy" name="brgy">
                                                            <!-- <option>option 1</option>
                                                                                                                                                                    <option>option 2</option>
                                                                                                                                                                    <option>option 3</option>
                                                                                                                                                                    <option>option 4</option>
                                                                                                                                                                    <option>option 5</option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="subdivision">Subdivision:</label>
                                                        <input type="text" class="form-control" id="subdivision"
                                                            name="subdivision">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="latitude">Latitude:</label>
                                                        <input type="text" class="form-control" id="latitude"
                                                            name="latitude">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="longitude">Longitude:</label>
                                                        <input type="text" class="form-control" id="longitude"
                                                            name="longitude">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="listype">List Type:</label>
                                                        <select class="form-control" id="listype" name="listype">
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
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="remarks">Remarks</label>
                                                        <!-- <textarea class="form-control" rows="3" id="remarks" name="remarks"></textarea> -->
                                                        <input type="type" class="form-control" name="remarks"
                                                            id="remarks">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save
                                changes</button>
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
