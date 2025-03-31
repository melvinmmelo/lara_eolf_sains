@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $title }} - Inbound Summary</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Inbound Summary</li>
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
                <div class="mb-3">
                    <h5>Total Receipts: {{ $receipts_count }}</h5>
                </div>

                <form action="{{ route('report.deliveryPurchaseReceiptSummary') }}" method="GET">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label" for="from_date">From</label>
                                <input type="date" class="form-control" name="from_date" required
                                    value="{{ request('from_date') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="to_date">To</label>
                                <input type="date" class="form-control" name="to_date" required
                                    value="{{ request('to_date') }}">
                            </div>

                            <div class="col-md-2 mt-4">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <table id="drp_summary_tb" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Description</th>
                            <th>Total Quantity</th>
                            <th>Hold Quantity</th>
                            <th>Available Quantity</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th>Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product['code'] }}</td>
                                <td>{{ $product['description'] }}</td>
                                <td>{{ $product['total_quantity'] }}</td>
                                <td>{{ $product['total_hold'] }}</td>
                                <td>{{ $product['available_quantity'] }}</td>
                                <td>{{ $product['unit'] }}</td>
                                <td>{{ number_format($product['price'], 2) }}</td>
                                <td>{{ number_format($product['total_value'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Total</th>
                            <th>{{ collect($products)->sum('total_quantity') }}</th>
                            <th>{{ collect($products)->sum('total_hold') }}</th>
                            <th>{{ collect($products)->sum('available_quantity') }}</th>
                            <th></th>
                            <th></th>
                            <th>{{ number_format(collect($products)->sum('total_value'), 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('custom_js')
<script>
    $(function () {
        $("#drp_summary_tb").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
@endsection
