@extends('layouts.app')
@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Bad Order Report</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Bad Order Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Bad Orders (Damaged/Returned)</h3></div>
            <div class="card-body">
                <form action="{{ route('reports.bad-order-report') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Period</label>
                            <select name="period" class="form-control">
                                <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                                <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Generate Report</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Bad Order Amount</th>
                                <th>Order Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ $row->customer_name }}</td>
                                    <td class="text-right">₱{{ number_format($row->bo_amount, 2) }}</td>
                                    <td>{{ $row->order_date->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No bad orders found</td></tr>
                            @endforelse
                        </tbody>
                        @if($data->count() > 0)
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">GRAND TOTAL:</th>
                                    <th class="text-right">₱{{ number_format($grandTotal, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
