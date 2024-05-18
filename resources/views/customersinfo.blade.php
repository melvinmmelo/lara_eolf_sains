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
                            <th>Branch</th>
                            <th>Name</th>
                            <th>Contact Nos</th>
                            <th>Tin no.</th>
                            <th>Store Name</th>
                            <th>Equipments</th>
                            <th>Store Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>{{ $customer->branch_code }}</td>
                                <td>{{ $customer->lastname }}, {{ $customer->firstname }} {{ $customer->middlename }}</td>
                                <td>{{ $customer->contact_no }}</td>
                                <td>{{ $customer->tin }}</td>
                                <td>{{ $customer->storeinfo->storename ?? '' }}</td>
                                <td>
                                    @if (optional($customer->equipmentStores)->isNotEmpty())
                                        @foreach ($customer->equipmentStores as $equipmentStore)
                                            {{ $equipmentStore->equipment_id }}
                                            @if (!$loop->last)
                                                , <!-- Add comma if it's not the last equipment -->
                                            @endif
                                        @endforeach
                                    @else
                                        No equipment available <!-- Display a message if there are no equipment stores -->
                                    @endif
                                </td>
                                <td>{{ $customer->storeinfo->brgy }}, {{ $customer->storeinfo->subdivision }},
                                    {{ $customer->storeinfo->city }}</td>
                                <td>
                                    <a class="btn btn-success btn-sm"
                                        href="/equipment-store?store_id={{ $customer->storeinfo->id }}&store_name={{ $customer->storeinfo->storename }}&customer_id={{ $customer->id }}&customer_name={{ $customer->lastname }}, {{ $customer->firstname }} {{ $customer->middlename }}"
                                        role="button">Equipment</a>
                                    <!-- add the edit button here -->
                                    <button class="btn btn-primary btn-sm edit-btn" data-toggle="modal"
                                        data-target="#editModal"
                                        onclick="setToUpdatecustomer('{{ $customer->id }}','{{ $customer->lastname }}','{{ $customer->firstname }}','{{ $customer->middlename }}','{{ $customer->contact_no }}','{{ $customer->companyname }}','{{ $customer->tin }}','{{ $customer->longitude }}','{{ $customer->latitude }}','{{ $customer->region }}','{{ $customer->province }}','{{ $customer->city }}','{{ $customer->brgy }}','{{ $customer->subdivision }}','{{ $customer->storeinfo->id }}','{{ $customer->storeinfo->storename }}','{{ $customer->storeinfo->contactno }}','{{ $customer->storeinfo->region }}','{{ $customer->storeinfo->province }}','{{ $customer->storeinfo->city }}','{{ $customer->storeinfo->brgy }}','{{ $customer->storeinfo->subdivision }}','{{ $customer->storeinfo->latitude }}','{{ $customer->storeinfo->longitude }}','{{ $customer->storeinfo->listype }}','{{ $customer->storeinfo->length_stay }}','{{ $customer->storeinfo->remarks }}')">Edit</button>
                                    <form method="POST" action="{{ route('customer.destroy', $customer->id) }}"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this customer info?')"
                                            class="btn btn-danger btn-sm">Delete</button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
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

                <form method="POST" action="/customers/store">
                    @csrf
                    <input type="hidden" name ="distributor" value="n/a">
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
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="branch_code">Branch Code</label>
                                                        <input type="text" class="form-control" name="branch_code"
                                                            id="branch_code" value="{{ session('branch_code') }}" required
                                                            readonly>

                                                    </div>
                                                </div>
                                            </div>

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
                                                        <label class="form-label" for="contactno2">Contact No.</label>
                                                        <input type="text" class="form-control" name="contactno2">
                                                    </div>
                                                </div>
                                            </div>


                                            <h6> Store Address </h6>
                                            <hr>
                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="region2">Region</label>
                                                        <select class="form-control" id="cust_region2" name="region2">
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
                                                        <label class="form-label" for="province2">Province</label>
                                                        <select class="form-control" id="cust_prov2" name="province2">
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
                                                        <label class="form-label" for="city2">City</label>
                                                        <select class="form-control" id="cust_city2" name="city2">
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
                                                        <label class="form-label" for="brgy2">Barangay</label>
                                                        <select class="form-control" id="cust_brgy2" name="brgy2">
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
                                                        <label class="form-label" for="subdivision2">Subdivision:</label>
                                                        <input type="text" class="form-control" id="subdivision2"
                                                            name="subdivision">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="latitude2">Latitude:</label>
                                                        <input type="text" class="form-control" id="latitude2"
                                                            name="latitude2">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="longitude2">Longitude:</label>
                                                        <input type="text" class="form-control" id="longitude2"
                                                            name="longitude2">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="listype">Least Type:</label>
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
                                                        <label class="form-label" for="longitude2">Length of Stay:</label>
                                                        <input type="text" class="form-control" id="length_stay"
                                                            name="length_stay">
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

        <!---star areh ng edut -->
        <div class="modal fade custom-modal" id="editModal">
            <div class="modal-dialog modal-xl">

                <form method="POST" action="/customers/store">
                    @csrf

                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Customer Info</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <input type="hidden" name ="distributor" value="n/a">
                            Customer ID <input type="text" class="form-control" name="id" id="id"
                                required readonly><br>
                            Store ID <input type="text" class="form-control" name="store_id" id="store_id" required
                                readonly><br>
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
                                                        <input type="text" class="form-control" id="lastname"
                                                            name="lastname">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label" for="cust_fname">First Name:</label>
                                                        <input type="text" class="form-control" id="firstname"
                                                            name="firstname">
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label class="form-label" for="cust_mname">Middle Name:</label>
                                                        <input type="text" class="form-control" id="middlename"
                                                            name="middlename">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_contact">Contact No.:</label>
                                                        <input type="text" class="form-control" id="contact_no"
                                                            name="contact_no">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_comp">Company Name:</label>
                                                        <input type="text" class="form-control" id="companyname"
                                                            name="companyname">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_tin">TIN:</label>
                                                        <input type="text" class="form-control" id="tin"
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
                                                        <select class="form-control" id="region" name="region">
                                                            <!-- <option></option> -->
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_prov">Province</label>
                                                        <select class="form-control" id="province" name="province">
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
                                                        <select class="form-control" id="city" name="city">
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
                                                        <select class="form-control" id="brgy" name="brgy">
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
                                                        <input type="text" class="form-control" id="subdivision"
                                                            name="subdivision">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="latitude">Latitude</label>
                                                        <input type="text" class="form-control" name="latitude"
                                                            id="latitude">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="longitude">Longitude</label>
                                                        <input type="text" class="form-control" name="longitude"
                                                            id="longitude">
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
                                                        <input type="text" class="form-control" name="storename"
                                                            id="storename">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="contactno2">Contact No.</label>
                                                        <input type="text" class="form-control" name="contactno2"
                                                            id="contactno2">
                                                    </div>
                                                </div>
                                            </div>


                                            <h6> Store Address </h6>
                                            <hr>
                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="region2">Region</label>
                                                        <select class="form-control" id="cust_region2" name="region2">
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
                                                        <label class="form-label" for="province2">Province</label>
                                                        <select class="form-control" id="cust_prov2" name="province2">
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
                                                        <label class="form-label" for="city2">City</label>
                                                        <select class="form-control" id="cust_city2" name="city2">
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
                                                        <label class="form-label" for="brgy2">Barangay</label>
                                                        <select class="form-control" id="cust_brgy2" name="brgy2">
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
                                                        <label class="form-label" for="subdivision2">Subdivision:</label>
                                                        <input type="text" class="form-control" id="subdivision2"
                                                            name="subdivision">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="latitude2">Latitude:</label>
                                                        <input type="text" class="form-control" id="latitude2"
                                                            name="latitude2">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="longitude2">Longitude:</label>
                                                        <input type="text" class="form-control" id="longitude2"
                                                            name="longitude2">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="listype">List Type:</label>
                                                        <select class="form-control" id="listype2" name="listype">
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
                                                        <label class="form-label" for="longitude2">Length of Stay:</label>
                                                        <input type="text" class="form-control" id="length_stay2"
                                                            name="length_stay">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="remarks">Remarks</label>
                                                        <!-- <textarea class="form-control" rows="3" id="remarks" name="remarks"></textarea> -->
                                                        <input type="type" class="form-control" name="remarks"
                                                            id="remarks2">
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


@section('custom_js')
    <script>
        function setToUpdatecustomer(uid, ln, fn, mn, con, cm, tin, long, lat, reg, prov, city, brgy, subv, store_id,
            storename, contactno, reg2, prov2, city2, brgy2, subv2, lat2, long2, listype, length_stay, remarks) {
            // Populate customer information fields
            document.getElementById("id").value = uid;

            document.getElementById("lastname").value = ln;
            document.getElementById("firstname").value = fn;
            document.getElementById("middlename").value = mn;
            document.getElementById("contact_no").value = con;
            document.getElementById("companyname").value = cm;
            document.getElementById("tin").value = tin;
            document.getElementById("region").value = reg;
            document.getElementById("province").value = prov;
            document.getElementById("city").value = city;
            document.getElementById("brgy").value = brgy;
            document.getElementById("subdivision").value = subv;
            document.getElementById("longitude").value = long;
            document.getElementById("latitude").value = lat;

            // Populate store information fields
            document.getElementById("store_id").value = store_id;
            document.getElementById("storename").value = storename;
            document.getElementById("contactno2").value = contactno;
            document.getElementById("cust_region2").value = reg2;
            document.getElementById("cust_prov2").value = prov2;
            document.getElementById("cust_city2").value = city2;
            document.getElementById("cust_brgy2").value = brgy2;
            document.getElementById("subdivision2").value = subv2;
            document.getElementById("latitude2").value = lat2;
            document.getElementById("longitude2").value = long2;
            document.getElementById("listype2").value = listype;
            document.getElementById("length_stay2").value = length_stay;
            document.getElementById("remarks2").value = remarks;
        }
    </script>





    <script>
        $('#modal-equipment').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var customerId = button.data('customer-id');
            var storeId = button.data('store-id');
            var modal = $(this);
            modal.find('input[name="customer_id"]').val(customerId);
            modal.find('input[name="store_id"]').val(storeId);
        });
    </script>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script>
        $(document).ready(function() {

            // Function to populate the Region dropdown
            function populateRegionDropdown() {
                // Clear existing options
                $('#cust_region').empty();

                // Add a blank option as the first option
                $('#cust_region').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-regions", // Route to fetch regions from your server
                    success: function(response) {
                        // $('#cust_region').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#cust_region').append('<option value="' + value.code + '">' +
                                value.name + '</option>');
                        });
                    }
                });
            }

            function populateRegionDropdown2() {
                // Clear existing options

                $('#region').empty();

                // Add a blank option as the first option
                $('#region').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-regions", // Route to fetch regions from your server
                    success: function(response) {
                        // $('#cust_region').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#region').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }
            // Function to populate the Province dropdown based on the selected region
            function populateProvinceDropdown(regionId) {
                $('#cust_prov').empty();

                // Add a blank option as the first option
                $('#cust_prov').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-provinces/" + regionId, // Route to fetch provinces based on region
                    success: function(response) {
                        // $('#cust_prov').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#cust_prov').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }

            function populateProvinceDropdown2(regionId) {
                $('#province').empty();

                // Add a blank option as the first option
                $('#province').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-provinces/" + regionId, // Route to fetch provinces based on region
                    success: function(response) {
                        // $('#cust_prov').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#province').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }

            // Function to populate the City dropdown based on the selected province
            function populateCityDropdown(provinceId) {
                $('#cust_city').empty();

                // Add a blank option as the first option
                $('#cust_city').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-cities/" + provinceId, // Route to fetch cities based on province
                    success: function(response) {
                        // $('#cust_city').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#cust_city').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }

            function populateCityDropdown2(provinceId) {
                $('#city').empty();

                // Add a blank option as the first option
                $('#city').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-cities/" + provinceId, // Route to fetch cities based on province
                    success: function(response) {
                        // $('#cust_city').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#city').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }

            function populateBrgyDropdown(cityId) {
                $('#cust_brgy').empty();

                // Add a blank option as the first option
                $('#cust_brgy').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-brgy/" + cityId, // Route to fetch cities based on city/mun
                    success: function(response) {
                        // $('#cust_brgy').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#cust_brgy').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }

            function populateBrgyDropdown2(cityId) {
                $('#brgy').empty();

                // Add a blank option as the first option
                $('#brgy').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-brgy/" + cityId, // Route to fetch cities based on city/mun
                    success: function(response) {
                        // $('#cust_brgy').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#brgy').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }
            // Initial population of Region dropdown
            populateRegionDropdown();
            populateRegionDropdown2();
            // Event listener for Region dropdown change
            $('#cust_region').change(function() {
                var regionId = $(this).val();
                if (regionId) {
                    // Populate Province dropdown based on selected region
                    populateProvinceDropdown(regionId);
                }
            });
            $('#region').change(function() {
                var regionId = $(this).val();
                if (regionId) {
                    // Populate Province dropdown based on selected region
                    populateProvinceDropdown2(regionId);
                }
            });

            // Event listener for Province dropdown change
            $('#cust_prov').change(function() {
                var provinceId = $(this).val();
                if (provinceId) {
                    // Populate City dropdown based on selected province
                    populateCityDropdown(provinceId);
                }
            });
            $('#province').change(function() {
                var provinceId = $(this).val();
                if (provinceId) {
                    // Populate City dropdown based on selected province
                    populateCityDropdown2(provinceId);
                }
            });
            // Event listener for brgy dropdown change
            $('#cust_city').change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Populate brgy dropdown based on selected province
                    populateBrgyDropdown(cityId);
                }
            });
            $('#city').change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Populate brgy dropdown based on selected province
                    populateBrgyDropdown2(cityId);
                }
            });
        });
    </script>



    <script>
        $(document).ready(function() {

            // Function to populate the Region dropdown
            function populateRegionDropdown2() {
                // Clear existing options
                $('#cust_region2').empty();

                // Add a blank option as the first option
                $('#cust_region2').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-regions", // Route to fetch regions from your server
                    success: function(response) {
                        // $('#cust_region').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#cust_region2').append('<option value="' + value.code + '">' +
                                value.name + '</option>');
                        });
                    }
                });
            }

            function populateRegionDropdown22() {
                // Clear existing options

                $('#region2').empty();

                // Add a blank option as the first option
                $('#region2').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-regions", // Route to fetch regions from your server
                    success: function(response) {
                        // $('#cust_region').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#region2').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }
            // Function to populate the Province dropdown based on the selected region
            function populateProvinceDropdown2(regionId) {
                $('#cust_prov2').empty();

                // Add a blank option as the first option
                $('#cust_prov2').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-provinces/" + regionId, // Route to fetch provinces based on region
                    success: function(response) {
                        // $('#cust_prov').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#cust_prov2').append('<option value="' + value.code + '">' +
                                value
                                .name + '</option>');
                        });
                    }
                });
            }

            function populateProvinceDropdown22(regionId) {
                $('#province').empty();

                // Add a blank option as the first option
                $('#province').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-provinces/" + regionId, // Route to fetch provinces based on region
                    success: function(response) {
                        // $('#cust_prov').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#province').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }

            // Function to populate the City dropdown based on the selected province
            function populateCityDropdown2(provinceId) {
                $('#cust_city2').empty();

                // Add a blank option as the first option
                $('#cust_city2').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-cities/" + provinceId, // Route to fetch cities based on province
                    success: function(response) {
                        // $('#cust_city').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#cust_city2').append('<option value="' + value.code + '">' +
                                value
                                .name + '</option>');
                        });
                    }
                });
            }

            function populateCityDropdown22(provinceId) {
                $('#city2').empty();

                // Add a blank option as the first option
                $('#city2').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-cities/" + provinceId, // Route to fetch cities based on province
                    success: function(response) {
                        // $('#cust_city').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#city2').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }

            function populateBrgyDropdown2(cityId) {
                $('#cust_brgy2').empty();

                // Add a blank option as the first option
                $('#cust_brgy2').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-brgy/" + cityId, // Route to fetch cities based on city/mun
                    success: function(response) {
                        // $('#cust_brgy').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#cust_brgy2').append('<option value="' + value.code + '">' +
                                value
                                .name + '</option>');
                        });
                    }
                });
            }

            function populateBrgyDropdown22(cityId) {
                $('#brgy2').empty();

                // Add a blank option as the first option
                $('#brgy2').append('<option value="">Please select</option>');
                $.ajax({
                    type: "GET",
                    url: "/get-brgy/" + cityId, // Route to fetch cities based on city/mun
                    success: function(response) {
                        // $('#cust_brgy').empty(); // Clear existing options
                        $.each(response, function(key, value) {
                            $('#brgy2').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });
                    }
                });
            }
            // Initial population of Region dropdown
            populateRegionDropdown2();
            populateRegionDropdown22();
            // Event listener for Region dropdown change
            $('#cust_region2').change(function() {
                var regionId = $(this).val();
                if (regionId) {
                    // Populate Province dropdown based on selected region
                    populateProvinceDropdown2(regionId);
                }
            });
            $('#region2').change(function() {
                var regionId = $(this).val();
                if (regionId) {
                    // Populate Province dropdown based on selected region
                    populateProvinceDropdown22(regionId);
                }
            });

            // Event listener for Province dropdown change
            $('#cust_prov2').change(function() {
                var provinceId = $(this).val();
                if (provinceId) {
                    // Populate City dropdown based on selected province
                    populateCityDropdown2(provinceId);
                }
            });
            $('#province2').change(function() {
                var provinceId = $(this).val();
                if (provinceId) {
                    // Populate City dropdown based on selected province
                    populateCityDropdown22(provinceId);
                }
            });
            // Event listener for brgy dropdown change
            $('#cust_city2').change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Populate brgy dropdown based on selected province
                    populateBrgyDropdown2(cityId);
                }
            });
            $('#city2').change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Populate brgy dropdown based on selected province
                    populateBrgyDropdown22(cityId);
                }
            });
        });
    </script>
@endsection
