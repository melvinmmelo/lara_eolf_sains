@extends('layouts.app')

@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sales by Product Type</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Sales by Product Type</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        @include('layouts.errors')

        <div class="card">
            <div class="card-body">
                <form action="{{ route('report.sales-by-product-type') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="report_type">Period</label>
                                <select name="report_type" id="report_type" class="form-control">
                                    <option value="daily" {{ request('report_type', 'daily') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ request('report_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ request('report_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    <option value="custom" {{ request('report_type') == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                                </select>
                            </div>

                            <div id="custom-date-range" class="d-none">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Generate Report
                            </button>

                            <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='{{ route('report.sales-by-product-type') }}'">
                                <i class="fas fa-redo"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Period: {{ $periodLabel }}</h5>
                    @if (count($rows) > 0)
                        <a href="{{ route('report.sales-by-product-type.export', request()->query()) }}"
                            class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    @endif
                </div>

                @if (count($rows) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Product Type</th>
                                    <th>Code</th>
                                    <th class="text-right">Quantity</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td>{{ $row['name'] }}</td>
                                        <td>{{ $row['ptype_code'] }}</td>
                                        <td class="text-right">{{ number_format($row['quantity']) }}</td>
                                        <td class="text-right">{{ number_format($row['amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <th colspan="2">Total</th>
                                    <th class="text-right">{{ number_format($totals['quantity']) }}</th>
                                    <th class="text-right">{{ number_format($totals['amount'], 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <p class="text-muted small mb-0">
                        Amount is order-line revenue only (quantity &times; price). It excludes the
                        delivery service fee, discounts and bad-order deductions, which are recorded per
                        order rather than per product type &mdash; so this total will not match the
                        grand total on the Sales report.
                    </p>
                @else
                    <div class="alert alert-info mb-0">No sales found for the selected period.</div>
                @endif
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.getElementById('report_type').addEventListener('change', function() {
            document.getElementById('custom-date-range').classList.toggle('d-none', this.value !== 'custom');
        });

        if (document.getElementById('report_type').value === 'custom') {
            document.getElementById('custom-date-range').classList.remove('d-none');
        }
    </script>
@endpush
