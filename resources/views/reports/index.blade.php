@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Reports</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Payment Report -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-money-bill-wave"></i> Payment Report
                        </h5>
                        <p class="card-text">View customer payments grouped by customer with date filters.</p>
                        <a href="{{ route('reports.payment-report') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sales by Product Type -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-chart-bar"></i> Sales by Product Type
                        </h5>
                        <p class="card-text">Sales breakdown by product type with quantities and totals.</p>
                        <a href="{{ route('reports.sales-by-product-type') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Inbound Summary per Flavor -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-ice-cream"></i> Inbound Summary per Flavor
                        </h5>
                        <p class="card-text">Inbound orders grouped by product flavor/variant.</p>
                        <a href="{{ route('reports.inbound-summary-per-flavor') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Expenses Report -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-receipt"></i> Expenses Report
                        </h5>
                        <p class="card-text">Track expenses by category with date and category filters.</p>
                        <a href="{{ route('reports.expenses-report') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sales by Freezer -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-snowflake"></i> Sales by Freezer Report
                        </h5>
                        <p class="card-text">Sales grouped by freezer/equipment with totals.</p>
                        <a href="{{ route('reports.sales-by-freezer') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bad Order Report -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-exclamation-triangle"></i> Bad Order Report
                        </h5>
                        <p class="card-text">Track damaged/returned products with period filters.</p>
                        <a href="{{ route('reports.bad-order-report') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Delivery Receipt Report -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-truck"></i> Delivery Receipt Report
                        </h5>
                        <p class="card-text">List delivery receipts with date filters and DR search.</p>
                        <a href="{{ route('reports.delivery-receipt-report') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sales by Flavor -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-chart-pie"></i> Sales by Flavor Report
                        </h5>
                        <p class="card-text">Sales breakdown by product flavor with quantities and amounts.</p>
                        <a href="{{ route('reports.sales-by-flavor') }}" class="btn btn-primary">
                            View Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
