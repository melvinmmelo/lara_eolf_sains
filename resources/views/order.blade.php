@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Orders</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')


        <!-- Default box -->
        <div class="card">

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Order No.</th>
                            <th>Date created</th>
                            <th>Price Level</th>
                            <th>Customer</th>
                            <th>Store</th>
                            <th>Equipment</th>
                            <th>Delivery Person</th>
                            <th>Vehicle</th>
                            <th>Status</th>
                            <th>Date updated</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inbounds as $inbound)
                            <tr>
                                <td>{{ $inbound->id }}</td>
                                <td>{{ $inbound->f_created_at }}</td>
                                <td>{{ $inbound->priceLevel->pl_name }}</td>
                                <td>{{ $inbound->customer->fullName }}</td>
                                <td>{{ $inbound->store->storename }}</td>
                                <td>{{ $inbound->equipment->serial_no }}</td>
                                <td>{{ $inbound->driver->name }}</td>
                                <td>{{ $inbound->vehicle->plateno }}</td>
                                <td>{{ $inbound->status }}</td>
                                <td>{{ $inbound->f_updated_at }}</td>
                                <td>
                                    @if ($inbound->status == 'Pending')
                                        <a href="{{ route('order.processTwo', ['inbound' => $inbound->id]) }}"><button
                                                class="btn btn-primary">Continue</button></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Order No.</th>
                            <th>Date</th>
                            <th>Price Level</th>
                            <th>Customer</th>
                            <th>Store</th>
                            <th>Equipment</th>
                            <th>Delivery Person</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-orders">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->
        <div class="modal fade" id="modal-orders">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form action="{{ route('order.submitProcessOne') }}" method="post">
                            @csrf

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="branch_code">Branch Code</label>
                                        <input type="text" class="form-control" name="branch_code" id="branch_code"
                                            value="{{ session('branch_code') }}" required readonly>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="equipment">Equipment</label>
                                        <select class="form-control equipment w-100 select2bs4" name="equipment"
                                            id="equipment" onchange="setCustomerName(this.value)" required>
                                            <option value="">--Select--</option>
                                            @foreach ($equipment as $equip)
                                                <option value="{{ $equip->id }}">{{ $equip->type . "-" . $equip->equipment->serial_no }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="customer"><i style="color:red">*</i>Customer</label>
                                        <input type="hidden" class="form-control" name="customer_id" id="customer_id" required readonly>
                                        <textarea class="form-control" rows="3" name="customer" id="customer" required readonly></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="deliveryPerson"><i style="color:red">*</i>Delivery
                                            Person</label>
                                        <select class="form-control" name="deliveryPerson" id="deliveryPerson" required>
                                            @foreach ($drivers as $driver)
                                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="vehicle"><i style="color:red">*</i>Vehicle</label>
                                        <select class="form-control" name="vehicle" id="vehicle" required>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}">{{ $vehicle->plateno }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="pricelevel_id"><i
                                                style="color:red">*</i>Price Level</label>
                                        <select class="form-control" name="pricelevel_id" id="pricelevel_id" required>
                                            @foreach ($pricing as $plevel)
                                                <option value="{{ $plevel->id }}">{{ $plevel->pl_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Next</button>
                            </div>

                        </form>
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

@section('custom_js')
    <script>
        // function setCustomerName(str) {
        //     if (str == '') {
        //         return;
        //     }

        //     var equipment = document.getElementById('equipment').value;

        //     // document.getElementById('customer').value = equipment;

        //     // get to get who owns the equipment
        //     var xmlhttp = new XMLHttpRequest();
        //     xmlhttp.onreadystatechange = function() {
        //         if (this.readyState == 4 && this.status == 200) {
        //             // convert the string to json

        //             // this.responseText = JSON.parse(this.responseText);

        //             console.log(this.responseText.customer_name);


        //             document.getElementById('customer_id').value = this.responseText.customer_id;
        //             document.getElementById('customer').value = this.responseText.customer_name;
        //         }
        //     };
        //     xmlhttp.open("GET", "/get-equipmentcustomerstore/" + str, true);
        //     xmlhttp.send();
        // }

        function setCustomerName(str) {
            $.ajax({
                type: "GET",
                url: "/get-equipmentcustomerstore/" + str,
                success: function(response) {
                    // console.log(response);
                    document.getElementById('customer_id').value = response.customer_id;
                    document.getElementById('customer').value = response.customer_name;
                }
            });
        }
    </script>
@endsection
