@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Inbound Inventory</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Inbound Inventory</li>
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

            <div class="card-body table-responsive">

                <div class="pb-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-inventory"">
                        Add New
                    </button>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>DR No.</th>
                            <th>Issue Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($deliveryPurchaseReceipts as $dr)
                            @php
                                $total = 0;
                                if ($dr->products) {
                                    $products = json_decode($dr->products, true);
                                    foreach ($products as $product) {
                                        $total += $product['quantity'] * $product['price'];
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $dr->dr_no }}</td>
                                <td>{{ $dr->issue_date }}</td>
                                <td>{{ formatNumber($total) }}</td>
                                <td>{{ $dr->status }}</td>
                                <td>{{ $dr->created_at }}</td>
                                <td>
                                    @if ($dr->status == 'Encoding')
                                        <a href="{{ route('drp.products', ['dprId' => $dr->id]) }}"><button type="button"
                                                class="btn btn-primary">
                                                <i class="fas fa-plus"></i></button></a>
                                    @endif

                                    @if ($dr->status == 'Completed')
                                        <a href="{{ route('drp.products', ['dprId' => $dr->id]) }}"><button type="button"
                                                class="btn btn-primary">
                                                <i class="fas fa-eye"></i></button></a>
                                    @endif
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                    <tfoot>
                        <tr>
                            <th>DR No.</th>
                            <th>Issue Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-inventory">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->
        <div class="modal fade" id="modal-inventory">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create Inventory</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form action="{{ route('delivery-purchase-receipts.store') }}" method="post">
                            @csrf


                            <input type="hidden" class="form-control" name="user_id" id="user_id"
                                value="{{ auth()->user()->id }}" required readonly>


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
                                        <label class="form-label" for="dr_no">DR. No.</label>
                                        <input type="text" class="form-control" name="dr_no" id="dr_no" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="issue_date">Issue Date</label>
                                        <input type="date" class="form-control" name="issue_date" id="issue_date"
                                            value="" required>



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
@endsection
