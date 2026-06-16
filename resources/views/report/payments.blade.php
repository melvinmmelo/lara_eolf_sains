@extends('layouts.app')

@section('contents')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Payment Report</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Payment Report</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        <a href="{{ route('report.payments.export') }}?{{ http_build_query(request()->all()) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @include('layouts.errors')

                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totals['customers'] }}</h3>
                                    <p>Customers</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $totals['payments'] }}</h3>
                                    <p>Orders Paid</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-receipt"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>₱{{ number_format($totals['amount_paid'], 2) }}</h3>
                                    <p>Total Collected</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>₱{{ number_format($totals['balance'], 2) }}</h3>
                                    <p>Outstanding Balance</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('report.payments') }}" method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Payment Type</label>
                                    <select name="payment_type" class="form-control">
                                        <option value="">All</option>
                                        @foreach($paymentTypes as $type)
                                            <option value="{{ $type }}" {{ request('payment_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Generate Report
                                    </button>
                                    <a href="{{ route('report.payments') }}" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>DEGIC No</th>
                                        <th>Customer</th>
                                        <th class="text-center">Payments</th>
                                        <th>Payment Type(s)</th>
                                        <th class="text-right">Net Amount</th>
                                        <th class="text-right">Amount Paid</th>
                                        <th class="text-right">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                        <tr>
                                            <td>{{ $customer['degic_no'] ?: 'N/A' }}</td>
                                            <td>{{ $customer['customer_name'] }}</td>
                                            <td class="text-center">{{ $customer['payments'] }}</td>
                                            <td>{{ $customer['payment_types'] }}</td>
                                            <td class="text-right">₱{{ number_format($customer['net_amount'], 2) }}</td>
                                            <td class="text-right">₱{{ number_format($customer['amount_paid'], 2) }}</td>
                                            <td class="text-right">₱{{ number_format($customer['balance'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td colspan="2">TOTAL</td>
                                        <td class="text-center">{{ $totals['payments'] }}</td>
                                        <td></td>
                                        <td class="text-right">₱{{ number_format($totals['net_amount'], 2) }}</td>
                                        <td class="text-right">₱{{ number_format($totals['amount_paid'], 2) }}</td>
                                        <td class="text-right">₱{{ number_format($totals['balance'], 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-danger mx-3">
                            No payments found for the selected filters.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
