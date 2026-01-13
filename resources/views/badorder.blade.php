@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bad Order Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Bad Orders</li>
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
                    <a href="{{ route('newbo.deducted') }}"><button type="button" class="btn btn-default">
                            Deducted BOs
                        </button></a>

                        <a href="{{ route('newbo.create') }}"><button type="button" class="btn btn-primary">
                            Add New
                        </button></a>
                </div>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>BO %</th>
                            <th>Remarks</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php $grandTotal = []; @endphp
                        @foreach ($badOrders as $badOrder)

                            @php
                                $grandTotal[] = $badOrder->amount;
                            @endphp
                            <tr>
                                <td>{{ $badOrder->degic_code . " " . $badOrder->customer->fullName }}</td>
                                <td>{{ $badOrder->amount }}</td>
                                <td>{{ $badOrder->bo_percentage }}</td>
                                <td>{{ $badOrder->remarks }}</td>
                                <td>{{ $badOrder->created_at }}</td>
                                <td>
                                    <button type="submit" class="btn btn-success"
                                        onclick="window.location.href='{{ route('newbo.edit', $badOrder->id) }}'">Edit</button>


                                    <button type="submit" class="btn btn-danger"
                                        onclick="return deleteBO(`{{ $badOrder->id }}`);">Delete</button>
                                    <button type="button" class="btn btn-default btn-print"
                                        data-bo-id="{{ $badOrder->id }}" onclick="printPage(this)">
                                        <i class="fa-solid fa-print"></i> Print
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total:</th>
                            <th>{{ formatNumber(array_sum($grandTotal)) }}</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary"
                    onclick="window.location.href='{{ route('newbo.create') }}'">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->


        <!-- /.modal-dialog -->
        <div class="modal fade" id="modal-badorder">
            <div class="modal-dialog">
                <form method="POST" action="#">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add bad order</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="code"><i style="color:red">*</i>Customer</label>
                                        <select class="form-control select2bs4" id="customer" name="customer">

                                        </select>
                                    </div>

                                    <div class="col-sm-12">
                                        <label class="form-label" for="name"><i style="color:red">*</i>Outbound</label>
                                        <select class="form-control select2bs4" id="outbound" name="outbound">

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </div>
                        </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

    </section>


    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteHeaderTitle">Delete bad order</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('bo.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="form-group">
                            <input type="text" name="bo_id" class="form-control mb-2 mt-2" required readonly>
                        </div>


                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>

                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
@endsection

@section('custom_js')
    @include('bad_order_js')
@endsection
