@extends('layouts.app')
@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Delivery Receipt Report</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Delivery Receipt Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Delivery Receipts</h3></div>
            <div class="card-body">
                <form action="{{ route('reports.delivery-receipt-report') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>DR Search</label>
                            <input type="text" name="dr_search" class="form-control" value="{{ $drSearch }}" placeholder="DR Number">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Generate Report</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>DR Number</th>
                                <th>Customer Name</th>
                                <th>Order Date</th>
                                <th class="text-right">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td>{{ $row->degic_no }}</td>
                                    <td>{{ $row->customer_name }}</td>
                                    <td>{{ $row->order_date->format('Y-m-d') }}</td>
                                    <td class="text-right">₱{{ number_format($row->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No delivery receipts found</td></tr>
                            @endforelse
                        </tbody>
                        @if($data->count() > 0)
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">GRAND TOTAL:</th>
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
