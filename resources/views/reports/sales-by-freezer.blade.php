@extends('layouts.app')
@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Sales by Freezer Report</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Sales by Freezer</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Sales by Equipment/Freezer</h3></div>
            <div class="card-body">
                <form action="{{ route('reports.sales-by-freezer') }}" method="GET" class="mb-3">
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
                                <th>Equipment Name</th>
                                <th>Equipment Code</th>
                                <th>Customer</th>
                                <th class="text-right">Orders Count</th>
                                <th class="text-right">Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td>{{ $row->equipment_name }}</td>
                                    <td>{{ $row->equipment_code }}</td>
                                    <td>{{ $row->customer_name }}</td>
                                    <td class="text-right">{{ $row->orders_count }}</td>
                                    <td class="text-right">₱{{ number_format($row->total_sales, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">No data found</td></tr>
                            @endforelse
                        </tbody>
                        @if($data->count() > 0)
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">GRAND TOTAL:</th>
                                    <th class="text-right">₱{{ number_format($grandTotal, 2) }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
