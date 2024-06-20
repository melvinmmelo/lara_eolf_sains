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
                <div class="pb-2">
                    <a href="{{ route('order.create') }}"><button type="button" class="btn btn-primary">
                            Add New
                        </button></a>
                </div>
                <div class="tbContainer">
                    <table id="example3" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date created</th>
                                <th>Order No.</th>
                                <th>Degic No.</th>
                                <th>Customer</th>
                                <th>Invoice Amount</th>
                                <th>Balance Due</th>
                                <th>Status</th>
                                <th>Days Overdue</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inbounds as $inbound)
                                @php
                                    $total = $inbound->totalAmount;
                                @endphp

                                <tr>
                                    <td>{{ $inbound->f_created_at }}</td>
                                    <td>{{ $inbound->id }}</td>
                                    <td>{{ $inbound->equipment->serial_no }}</td>
                                    <td>{{ $inbound->customer->fullName }}</td>
                                    <td><span class="label label-primary">{{ $total }}</span></td>
                                    <td>{{ $total - $inbound->delivered_amount }}</td>
                                    <td>{{ $inbound->status }}</td>
                                    <td>{{ number_format($inbound->created_at->diffInDays(now()), 0) }}</td>
                                    <td>
                                        @if ($inbound->status == 'Encoding')
                                            <a href="{{ route('order.processTwo', ['inbound' => $inbound->id]) }}"><button
                                                    class="btn btn-primary">Continue</button></a>
                                        @endif

                                        @if ($inbound->status == 'Completed')
                                            <a href="#" data-target="#modalAddAmountDelivered"
                                                data-toggle="modal"><button class="btn btn-danger"
                                                    onclick="setObId(`{{ $inbound->id }}`)">Update</button></a>
                                        @endif

                                        @if ($inbound->is_with_badOrder)
                                            <button class="btn btn-xs btn-danger">W/ BO</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Date created</th>
                                <th>Order No.</th>
                                <th>Degic No.</th>
                                <th>Customer</th>
                                <th>Invoice Amount</th>
                                <th>Balance Due</th>
                                <th>Status</th>
                                <th>Days Overdue</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <a href="{{ route('order.create') }}"><button type="button" class="btn btn-primary">
                        Add New
                    </button></a>
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
                                                <option value="{{ $equip->id }}">
                                                    {{ $equip->equipment->code . ' ' . $equip->customer->fullName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="customer"><i style="color:red">*</i>Customer</label>
                                        <input type="hidden" class="form-control" name="customer_id" id="customer_id"
                                            required readonly>
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
                                            <option value="">--Select--</option>
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
                                            <option value="">--Select--</option>
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
                                        <label class="form-label" for="pricelevel_id"><i style="color:red">*</i>Price
                                            Level</label>
                                        <select class="form-control" name="pricelevel_id" id="pricelevel_id" required>
                                            <option value="">--Select--</option>
                                            @foreach ($pricing as $plevel)
                                                <option value="{{ $plevel->id }}">{{ $plevel->pl_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="form-group">
                                <label class="form-label" for="bad_order_id"><i style="color:red">*</i>Bad Order</label>
                                <select class="form-control" name="bad_order_id" id="bad_order_id">
                                    <option value="">--Select--</option>
                                    @foreach ($badOrders as $badOrder)
                                        <option value="{{ $badOrder->id }}">{{ $badOrder->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}

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


    @include('modalAddAmountDelivered')
@endsection
