@extends('layouts.app')
@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Sales by Flavor Report</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Sales by Flavor</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Sales Breakdown by Flavor</h3></div>
            <div class="card-body">
                <form action="{{ route('reports.sales-by-flavor') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                        </div>
                        <div class="col-md-4">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Generate Report</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Flavor</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">Orders Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td>{{ $row['flavor'] }}</td>
                                    <td class="text-right">{{ number_format($row['quantity']) }}</td>
                                    <td class="text-right">₱{{ number_format($row['amount'], 2) }}</td>
                                    <td class="text-right">{{ number_format($row['orders_count']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No data found</td></tr>
                            @endforelse
                        </tbody>
                        @if($data->count() > 0)
                            <tfoot>
                                <tr>
                                    <th>TOTALS:</th>
                                    <th class="text-right">{{ number_format($totalQuantity) }}</th>
                                    <th class="text-right">₱{{ number_format($totalAmount, 2) }}</th>
                                    <th class="text-right">{{ number_format($totalOrders) }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
