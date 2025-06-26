@extends('layouts.app')

@section('contents')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Sales Report</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Sales Report</li>
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
                        <a href="{{ route('report.sales.export') }}?{{ http_build_query(request()->all()) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel"></i> Export to Excel
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @include('layouts.errors')

                    @if(isset($sales) && count($sales) > 0)
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totals['count'] }}</h3>
                                    <p>Total Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>₱{{ number_format($totals['grandTotal'], 2) }}</h3>
                                    <p>Total Sales</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-money-bill"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $totals['completedCount'] }}</h3>
                                    <p>Completed & Paid Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $totals['pendingCount'] }}</h3>
                                    <p>Pending Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('report.sales') }}" method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Report Type</label>
                                    <select name="report_type" id="report_type" class="form-control">
                                        <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>Daily</option>
                                        <option value="weekly" {{ request('report_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="yearly" {{ request('report_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        <option value="custom" {{ request('report_type') == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                                    </select>
                                </div>

                                <div id='custom-date-range' class="d-none">
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

                                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('report.sales') }}'">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
            </div>

            @if(isset($sales) && count($sales) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order No</th>
                                    <th>DEGIC No</th>
                                    <th>Customer</th>
                                    <th>Store</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->order_date->format('Y-m-d') }}</td>
                                        <td>{{ $sale->code }}</td>
                                        <td>{{ $sale->degic_no ?: 'N/A' }}</td>
                                        <td>{{ $sale->customer_name }}</td>
                                        <td>{{ $sale->store_name }}</td>
                                        <td>
                                            <span class="badge {{ $sale->status == 'Completed' ? 'badge-success' : 'badge-warning' }}">
                                                {{ $sale->status }}
                                            </span>
                                        </td>
                                        <td>₱{{ number_format($sale->grandTotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="font-weight-bold"></td>
                                    <td class="font-weight-bold">₱{{ number_format($sales->sum('grandTotal'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="d-flex justify-content-center align-items-center mt-3">
                            {{ $sales->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif

               @if(count($sales) == 0)
                    <div class="alert alert-danger mx-3">
                        No records found.
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.getElementById('report_type').addEventListener('change', function() {
        const customDateRange = document.getElementById('custom-date-range');
        customDateRange.classList.toggle('d-none', this.value !== 'custom');
    });

    // Show custom date range if it's selected on page load
    if (document.getElementById('report_type').value === 'custom') {
        document.getElementById('custom-date-range').classList.remove('d-none');
    }
</script>
@endpush
