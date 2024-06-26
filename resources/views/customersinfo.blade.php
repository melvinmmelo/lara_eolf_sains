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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Customers</li>
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
                <div class="pb-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-customer">
                        Add New
                    </button>
                </div>
                <table id="customer_tb" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            {{-- <th>ID</th>
                            <th>Branch</th> --}}
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Tin no.</th>
                            <th>Store Name</th>
                            <th>Equipments</th>
                            <th>Store Address</th>
                            <th>Created at</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            @foreach ($customer->stores as $store)
                                <tr>
                                    {{-- <td>{{ $customer->id }}</td> --}}
                                    {{-- <td>{{ $customer->branch_code }}</td> --}}
                                    <td>{{ $customer->lastname }}, {{ $customer->firstname }} {{ $customer->middlename }}
                                    </td>
                                    <td>{{ $customer->contact_no }}</td>
                                    <td>{{ $customer->tin }}</td>
                                    <td>{{ $store->storename ?? '' }}</td>
                                    <td>
                                        @if ($store->equipmentStores->isNotEmpty())
                                            @foreach ($store->equipmentStores as $equipmentStore)
                                                {{ $equipmentStore->equipment->code }}
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @else
                                            No Equipment Assigned
                                        @endif
                                    </td>
                                    <td>
                                        @if ($store)
                                            <a href="#"
                                                onclick="setMapModalInfo(`{{ $store->brgy }}`,`{{ $store->subdivision }}`,`{{ $store->city }}`,`{{ $store->longitude }}`, `{{ $store->latitude }}`)">
                                                {{ $store->brgy }}, {{ $store->subdivision }}, {{ $store->city }}
                                            </a>
                                        @else
                                            No address available
                                        @endif
                                    </td>
                                    <td>{{ $customer->date_created }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop1" type="button"
                                                class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                <a class="dropdown-item"
                                                    href="/store-info?customer_id={{ $customer->id }}&customer_name={{ urlencode($customer->firstname . ' ' . $customer->lastname) }}"
                                                    role="button">Store</a>
                                                <a class="dropdown-item"
                                                    href="/equipment-store?store_id={{ $store->id }}&store_name={{ $store->storename }}&customer_id={{ $customer->id }}&customer_name={{ $customer->lastname }}, {{ $customer->firstname }} {{ $customer->middlename }}"
                                                    role="button">Equipment</a>
                                                <a href="#" class="dropdown-item" data-toggle="modal"
                                                    data-target="#editModal"
                                                    onclick="setToUpdatecustomer('{{ $customer->id }}','{{ $customer->branch_code }}','{{ $customer->lastname }}','{{ $customer->firstname }}','{{ $customer->middlename }}','{{ $customer->contact_no }}','{{ $customer->companyname }}','{{ $customer->tin }}',`{{ $customer->longitude }}`,`{{ $customer->latitude }}`,`{{ $customer->region }}`,`{{ $customer->province }}`,`{{ $customer->city }}`,`{{ $customer->brgy }}`,`{{ $customer->subdivision }}`,'{{ $store->id }}',`{{ $store->storename }}`,'{{ $store->contactno }}','{{ $store->region }}','{{ $store->province }}','{{ $store->city }}','{{ $store->brgy }}','{{ $store->subdivision }}',`{{ $store->latitude }}`,`{{ $store->longitude }}`,'{{ $store->listype }}','{{ $store->length_stay }}','{{ $store->remarks }}')">Edit</a>

                                                <a href="{{ route('customer.store.destroy', ['customer' => $customer->id, 'store' => $store->id]) }}"
                                                    class="dropdown-item"
                                                    onclick="event.preventDefault();
                                        if(confirm('Are you sure you want to delete this store?')) {
                                            document.getElementById('delete-store-form-{{ $customer->id }}-{{ $store->id }}').submit();
                                        }">
                                                    Delete
                                                </a>

                                                <form id="delete-store-form-{{ $customer->id }}-{{ $store->id }}"
                                                    method="POST"
                                                    action="{{ route('customer.store.destroy', ['customer' => $customer->id, 'store' => $store->id]) }}"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                    @foreach ($store->equipmentStores as $equipmentStore)
                                                        <input type="hidden" name="equipment_ids[]"
                                                            value="{{ $equipmentStore->equipment_id }}">
                                                    @endforeach
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach


                    </tbody>
                    <tfoot>
                        <tr>
                            {{-- <th>ID</th>
                            <th>Branch</th> --}}
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Tin no.</th>
                            <th>Store Name</th>
                            <th>Equipments</th>
                            <th>Store Address</th>
                            <th>Created at</th>
                            <th></th>
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
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_city">City</label>
                                                        <select class="form-control" id="cust_city" name="city">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_brgy">Barangay</label>
                                                        <select class="form-control" id="cust_brgy" name="brgy">
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
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="is_store" id="is_store"
                                                            value="1">
                                                        Home based
                                                    </label>
                                                </div>
                                            </div>

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
                                                        <label class="form-label" for="cust_region2">Region</label>
                                                        <select class="form-control" id="cust_region2" name="region2">>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_prov2">Province</label>
                                                        <select class="form-control" id="cust_prov2" name="province2">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_city2">City</label>
                                                        <select class="form-control" id="cust_city2" name="city2">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="cust_brgy2">Barangay</label>
                                                        <select class="form-control" id="cust_brgy2" name="brgy2">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="subdivision2">Subdivision:</label>
                                                        <input type="text" class="form-control" id="cust_subdivision2"
                                                            name="subdivision2">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="latitude2">Latitude:</label>
                                                        <input type="text" class="form-control" id="cust_latitude2"
                                                            name="latitude2">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="longitude2">Longitude:</label>
                                                        <input type="text" class="form-control" id="cust_longitude2"
                                                            name="longitude2">
                                                    </div>
                                                </div>

                                                  <a href="#" data-toggle="modal"
                                                            data-target="#setLatLongMap">Get</a>
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


        </form>
        </div>
        </div>

        <!---star areh ng edut -->
        <div class="modal fade custom-modal" id="editModal">
            <div class="modal-dialog modal-xl">

                <form method="POST" action="{{ route('customer.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Customer Info</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <input type="hidden" name ="distributor" value="n/a">
                            <input type="hidden" name="e_branch_code" id="e_branch_code" value="" required
                                readonly>


                            Customer ID <input type="text" class="form-control" name="id" id="id" required readonly><br>
                            Store ID <input type="text" class="form-control" name="store_id" id="store_id" required readonly><br>
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
                                                        <label class="form-label" for="e_region">Region</label>
                                                        <input type="text" id="e_region" name="e_region"
                                                            class="form-control">
                                                        <!-- <select class="form-control" id="e_region" name="e_region">

                                                                                                                                        </select> -->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="e_province">Province</label>
                                                        <input type="text" id="e_province" name="e_province"
                                                            class="form-control">
                                                        <!-- <select class="form-control" id="e_province" name="e_province">
                                                                                                                                        </select> -->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="e_city">City</label>
                                                        <input type="text" id="e_city" name="e_city"
                                                            class="form-control">
                                                        <!-- <select class="form-control" id="e_city" name="e_city">
                                                                                                                                        </select> -->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="e_brgy">Barangay</label>
                                                        <input type="text" id="e_brgy" name="e_brgy"
                                                            class="form-control">
                                                        <!-- <select class="form-control" id="e_brgy" name="e_brgy">
                                                                                                                                        </select> -->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="subdivision">Subdivision</label>
                                                        <input type="text" class="form-control" id="subdivision"
                                                            name="subdivision">
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
                                                        <label class="form-label" for="e_region2">Region</label>
                                                        <input type="text" id="e_region2" name="e_region2"
                                                            class="form-control">
                                                        <!-- <select class="form-control" id="e_region2" name="e_region2">
                                                                                                                                        </select> -->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="e_province2">Province</label>
                                                        <input type="text" id="e_province2" name="e_province2"
                                                            class="form-control">
                                                        <!-- <select class="form-control" id="e_province2" name="e_province2">
                                                                                                                                        </select> -->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="e_city2">City</label>
                                                        <input type="text" id="e_city2" name="e_city2"
                                                            class="form-control">
                                                        <!-- <select class="form-control" id="e_city2" name="e_city2">
                                                                                                                                        </select> -->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="e_brgy2">Barangay</label>
                                                        <input type="text" id="e_brgy2" name="e_brgy2"
                                                            class="form-control">
                                                        <!-- <select class="form-control" id="e_brgy2" name="e_brgy2">
                                                                                                                                        </select> -->
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="row mb-1">
                                                    <div class="col-sm-12">
                                                        <label class="form-label" for="subdivision2">Subdivision:</label>
                                                        <input type="text" class="form-control" id="subdivision2"
                                                            name="subdivision2">
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

                                                 <a href="#" data-toggle="modal"
                                                            data-target="#setLatLongMap">Get</a>
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
                            <button type="submit" class="btn btn-success">Save changes</button>
                        </div>
                    </div>
            </div>
            </form>
        </div>
        </div>
    </section>

    <!-- /.content -->


    <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapModalLabel">
                        <div class="modalMapInfo"></div>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    {{-- <iframe src="#" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe> --}}

                    {{-- <iframe src="#" width="100%" height="450" style="border:0;" allowfullscreen=""
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> --}}

                    <div id="mapView" style="height: 400px; width: 100%;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="setLatLongMap" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapModalLabel">
                        <div class="modalMapInfo"></div>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="map" style="height: 400px; width: 100%;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></div>

                </div>
            </div>
        </div>
    </div>
@endsection


@section('custom_js')

<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#example1')) {
            $('#example1').DataTable({
                "order": [
                    [0, 'asc'] // Sorting by the second column (Name)
                ]
            });
        }
    });
</script>



    <script>
        let map2;
        let map;


        function setMapModalInfo(brgy, subd, city, long, lat) {

            map2 = L.map('mapView').setView([lat, long], 18);

            $('.modalMapInfo').html(brgy + ', ' + subd + ', ' + city);

            // update iframe src
            // $('iframe').attr('src', 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3022.0452163943947!2d' + long + '!3d' + lat + '!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDQyJzQ2LjAiTiA3NMKwMDBBJzIxLjAiVw!5e0!3m2!1sen!2sus!4v1716257108716!5m2!1sen!2sus'
            // );

            // $('iframe').attr('src', 'https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3861.801149005104!2d' + long +
            //     '!3d' + lat +
            //     '!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTTCsDMzJzEyLjEiTiAxMjHCsDAxJzE0LjYiRQ!5e0!3m2!1sen!2sph!4v1718089739967!5m2!1sen!2sph' +
            //     '&q=' + lat + ',' + long);


            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map2);

            var marker = L.marker([lat, long]).addTo(map2);

            marker.on('click', function() {
                loadStreetView(lat, long);
            });


            $('#mapModal').modal('show');

        }

        // Function to load street view
        function loadStreetView(lat, long) {
            var url = `https://www.openstreetcam.org/map/@${lat}/${long}/17`;
            window.open(url, '_blank');
        }

        $('#mapModal').on('shown.bs.modal', function() {
            setTimeout(function() {
                if (map2) {
                    map2.invalidateSize();
                }
            }, 0);
        });

        // function setToUpdatecustomer(uid, ebcode, ln, fn, mn, con, cm, tin, long, lat, reg, prov, city, brgy, subv,
        //     store_id,
        //     storename, contactno, reg2, prov2, city2, brgy2, subv2, lat2, long2, listype, length_stay, remarks) {
        // Populate customer information fields
        function setToUpdatecustomer(uid, ebcode, ln, fn, mn, con, cm, tin, long, lat, reg, prov, city, brgy, subv,
            store_id, storename, contactno, reg2, prov2, city2, brgy2, subv2, lat2, long2, listype, length_stay, remarks) {
            //             console.log('subdivision2:', subv2);
            // console.log('latitude2:', lat2);
            // console.log('longitude2:', long2);

            document.getElementById("id").value = uid;

            document.getElementById("e_branch_code").value = ebcode;
            document.getElementById("lastname").value = ln;
            document.getElementById("firstname").value = fn;
            document.getElementById("middlename").value = mn;
            document.getElementById("contact_no").value = con;
            document.getElementById("companyname").value = cm;
            document.getElementById("tin").value = tin;
            document.getElementById("e_region").value = reg;
            document.getElementById("e_province").value = prov;
            document.getElementById("e_city").value = city;
            document.getElementById("e_brgy").value = brgy;
            document.getElementById("subdivision").value = subv;


            // Populate store information fields
            document.getElementById("store_id").value = store_id;
            document.getElementById("storename").value = storename;
            document.getElementById("contactno2").value = contactno;
            document.getElementById("e_region2").value = reg2;
            document.getElementById("e_province2").value = prov2;
            document.getElementById("e_city2").value = city2;
            document.getElementById("e_brgy2").value = brgy2;
            document.getElementById("subdivision2").value = subv2;

            document.getElementById("listype2").value = listype;
            document.getElementById("length_stay2").value = length_stay;
            document.getElementById("remarks2").value = remarks;
        }

        $('#modal-equipment').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var customerId = button.data('customer-id');
            var storeId = button.data('store-id');
            var modal = $(this);
            modal.find('input[name="customer_id"]').val(customerId);
            modal.find('input[name="store_id"]').val(storeId);
        });

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

                        $.each(response, function(key, value) {
                            $('#cust_region2').append('<option value="' + value.code + '">' +
                                value
                                .name + '</option>');
                        });


                        $.each(response, function(key, value) {
                            $('#e_region').append('<option value="' + value.code + '">' +
                                value.name + '</option>');
                        });

                        $.each(response, function(key, value) {
                            $('#e_region2').append('<option value="' + value.code + '">' +
                                value.name + '</option>');
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

                        $.each(response, function(key, value) {
                            $('#e_province').append('<option value="' + value.code + '">' +
                                value
                                .name + '</option>');
                        });

                        $.each(response, function(key, value) {
                            $('#e_province2').append('<option value="' + value.code + '">' +
                                value
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

                        $.each(response, function(key, value) {
                            $('#e_city').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });

                        $.each(response, function(key, value) {
                            $('#e_city2').append('<option value="' + value.code + '">' + value
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

                        $.each(response, function(key, value) {
                            $('#e_brgy').append('<option value="' + value.code + '">' + value
                                .name + '</option>');
                        });

                        $.each(response, function(key, value) {
                            $('#e_brgy2').append('<option value="' + value.code + '">' + value
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

            // Event listener for Region dropdown change

            $('#cust_region').change(function() {
                var regionId = $(this).val();
                if (regionId) {
                    // Populate Province dropdown based on selected region
                    populateProvinceDropdown(regionId);
                    populateProvinceDropdown2(regionId);

                }
            });

            // Event listener for Province dropdown change
            $('#cust_prov').change(function() {
                var provinceId = $(this).val();
                if (provinceId) {
                    // Populate City dropdown based on selected province
                    populateCityDropdown(provinceId);
                    populateCityDropdown2(provinceId);

                }
            });
            // Event listener for brgy dropdown change
            $('#cust_city').change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Populate brgy dropdown based on selected province
                    populateBrgyDropdown(cityId);
                    populateBrgyDropdown2(cityId);

                }
            });

            $('#cust_region2').change(function() {
                var regionId = $(this).val();
                if (regionId) {
                    // Populate Province dropdown based on selected region
                    populateProvinceDropdown2(regionId);

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
            // Event listener for brgy dropdown change
            $('#cust_city2').change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Populate brgy dropdown based on selected province
                    populateBrgyDropdown2(cityId);
                }
            });



            // EDIT MODAL REGION, PROVINCE, CITY, BRGY

            $("#e_region").change(function() {
                var regionId = $(this).val();
                if (regionId) {
                    // Populate Province dropdown based on selected region
                    populateProvinceDropdown(regionId);
                    populateProvinceDropdown2(regionId);
                }
            });

            // Event listener for Province dropdown change
            $("#e_province").change(function() {
                var provinceId = $(this).val();
                if (provinceId) {
                    // Populate City dropdown based on selected province
                    populateCityDropdown(provinceId);
                    populateCityDropdown2(provinceId);
                }
            });
            // Event listener for brgy dropdown change
            $("#e_city").change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Populate brgy dropdown based on selected province
                    populateBrgyDropdown(cityId);
                    populateBrgyDropdown2(cityId);
                }
            });

            $("#e_region2").change(function() {
                var regionId = $(this).val();
                if (regionId) {
                    // Populate Province dropdown based on selected region
                    populateProvinceDropdown2(regionId);
                }
            });

            // Event listener for Province dropdown change
            $("#e_province2").change(function() {
                var provinceId = $(this).val();
                if (provinceId) {
                    // Populate City dropdown based on selected province
                    populateCityDropdown2(provinceId);
                }
            });
            // Event listener for brgy dropdown change
            $("#e_city2").change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Populate brgy dropdown based on selected province
                    populateBrgyDropdown2(cityId);
                }
            });

            // EDIT MODAL REGION, PROVINCE, CITY, BRGY

            $("#e_region").change(function() {
                var regionId = $(this).val();
                if (regionId) {
                    // Populate Province dropdown based on selected region
                    editPopulateProvinceDropdown(regionId);
                    editPopulateProvinceDropdown2(regionId);

                }
            });

            // Event listener for Province dropdown change
            $("#e_province").change(function() {
                var provinceId = $(this).val();
                if (provinceId) {
                    // Populate City dropdown based on selected province
                    editPopulateCityDropdown(provinceId);
                    editPopulateCityDropdown2(provinceId);
                }
            });
            // Event listener for brgy dropdown change
            $("#e_city").change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Populate brgy dropdown based on selected province
                    editPopulateBrgyDropdown(cityId);
                    editPopulateBrgyDropdown2(cityId);
                }
            });

            $("#e_region2").change(function() {
                var regionId = $(this).val();
                if (regionId) {
                    // Populate Province dropdown based on selected region
                    editPopulateProvinceDropdown2(regionId);
                }
            });

            // Event listener for Province dropdown change
            $("#e_province2").change(function() {
                var provinceId = $(this).val();
                if (provinceId) {
                    // Populate City dropdown based on selected province
                    editPopulateCityDropdown2(provinceId);
                }
            });
            // Event listener for brgy dropdown change
            $("#e_city2").change(function() {
                var cityId = $(this).val();
                if (cityId) {
                    // Populate brgy dropdown based on selected province
                    editPopulateBrgyDropdown2(cityId);
                }
            });


            // Initial population of Region dropdown
            populateRegionDropdown();

        });

        window.onload = function() {
            var checkbox = document.getElementById('is_store');
            var companyNameInput = document.querySelector('input[name="companyname"]');
            var storeNameInput = document.querySelector('input[name="storename"]');

            var contacNo = document.querySelector('input[name="contact_no"]');
            var storeContacNo = document.querySelector('input[name="contactno2"]');

            var cust_region = document.querySelector('select[name="region"]');
            var cust_prov = document.querySelector('select[name="province"]');
            var cust_city = document.querySelector('select[name="city"]');
            var cust_brgy = document.querySelector('select[name="brgy"]');

            var cust_region2 = document.querySelector('select[name="region2"]');
            var cust_prov2 = document.querySelector('select[name="province2"]');
            var cust_city2 = document.querySelector('select[name="city2"]');
            var cust_brgy2 = document.querySelector('select[name="brgy2"]');

            checkbox.addEventListener('change', function() {
                if (this.checked) {

                    storeNameInput.value = companyNameInput.value;
                    storeContacNo.value = contacNo.value;

                    // get selected region, province, city, brgy
                    // and set it to store region, province, city, brgy
                    cust_region2.value = cust_region.value;
                    cust_prov2.value = cust_prov.value;
                    cust_city2.value = cust_city.value;
                    cust_brgy2.value = cust_brgy.value;



                }
            });
        }

        $('#setLatLongMap').on('shown.bs.modal', function() {
            if (!map) {

                map = L.map('map').setView([17.6022249, 121.6770603], 10);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                map.on('click', function(e) {

                    var lat = e.latlng.lat;
                    var lng = e.latlng.lng;

                    document.getElementById('latitude2').value = lat;
                    document.getElementById('longitude2').value = lng;

                    document.getElementById('cust_latitude2').value = lat;
                    document.getElementById('cust_longitude2').value = lng;


                    $('#setLatLongMap').modal('hide');

                });
            }

            setTimeout(function() {
                if (!map) {
                    map.invalidateSize();
                }
            }, 0);
        });
    </script>
@endsection
