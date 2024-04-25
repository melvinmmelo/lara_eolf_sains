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
            <div class="card-header">
                <h3 class="card-title">Orders Info</h3>

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
                            <th>Date created</th>
                            <th>Store</th>
                            <th>Equipment</th>
                            <th>Order No.</th>
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
                                <td>{{ $inbound->f_created_at }}</td>
                                <td></td>
                                <td>{{ $inbound->vehicle_id }}</td>
                                <td>{{ $inbound->driver->name }}</td>
                                <td>{{ $inbound->vehicle->plateno }}</td>
                                <td></td>
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
                            <th>Date</th>
                            <th>Store</th>
                            <th>Equipment</th>
                            <th>Order No.</th>
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
                                        <label class="form-label" for="equipment">Equipment</label>
                                        <select class="form-control" name="equipment" id="equipment">
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
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="customer">Customer</label>
                                        <textarea class="form-control" rows="3" name="customer" id="customer"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="deliveryPerson">Delivery Person</label>
                                        <select class="form-control" name="deliveryPerson" id="deliveryPerson">
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
                                        <label class="form-label" for="vehicle">Vehicle</label>
                                        <select class="form-control" name="vehicle" id="vehicle">
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}">{{ $vehicle->plateno }}</option>
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
