@extends('layouts.app')

@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sales by Freezer</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Sales by Freezer</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        @include('layouts.errors')

        <div class="card">
            <div class="card-body">
                <form action="{{ route('report.sales-by-freezer') }}" method="GET" class="mb-4">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="year">Year</label>
                                <select name="year" id="year" class="form-control">
                                    @foreach ($years as $y)
                                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </form>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Monthly Monitoring of Sales per Freezer (Jan-{{ $year }} to Dec-{{ $year }})</h5>
                    @if (count($rows) > 0)
                        <a href="{{ route('report.sales-by-freezer.export', request()->query()) }}"
                            class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    @endif
                </div>

                @if (count($rows) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm text-nowrap">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>DEGIC Code</th>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <th class="text-right">{{ \Carbon\Carbon::create($year, $m, 1)->format('M-Y') }}</th>
                                    @endfor
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>{{ $row['customer_name'] }}{{ $row['store_name'] !== '' ? ' ('.$row['store_name'].')' : '' }}</td>
                                        <td>{{ $row['degic_no'] }}</td>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <td class="text-right">
                                                {{ $row['months'][$m] != 0 ? number_format($row['months'][$m], 2) : '' }}
                                            </td>
                                        @endfor
                                        <td class="text-right font-weight-bold">{{ number_format($row['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <th colspan="2">Total</th>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <th class="text-right">{{ number_format($totals['months'][$m], 2) }}</th>
                                    @endfor
                                    <th class="text-right">{{ number_format($totals['grand'], 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <p class="text-muted small mb-0">
                        A row is one freezer (DEGIC code) under one customer; orders without a DEGIC code
                        are shown as N/A. Amount is order-line revenue only (quantity &times; price) and
                        excludes the delivery service fee, discounts and bad-order deductions &mdash; so the
                        grand total will not match the Sales report.
                    </p>
                @else
                    <div class="alert alert-info mb-0">No sales found for {{ $year }}.</div>
                @endif
            </div>
        </div>
    </section>
@endsection
