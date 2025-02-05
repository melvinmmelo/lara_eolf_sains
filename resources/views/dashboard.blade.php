@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            @include('layouts.errors')
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        @can('admin')
        <div class="container-fluid">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ \App\Models\Product::count() }}</h3>
                            <p>Total Products</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <a href="{{ route('products.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($todays_orders_count) }}</h3>
                            <p>Today's Orders</p>
                            <h5>₱{{ number_format($todays_orders_amount, 2) }}</h5>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($todays_paid_count) }}</h3>
                            <p>Today's Paid Orders</p>
                            <h5>₱{{ number_format($todays_paid_amount, 2) }}</h5>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ number_format($todays_completed_count) }}</h3>
                            <p>Today's Completed</p>
                            <h5>₱{{ number_format($todays_completed_amount, 2) }}</h5>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Sales Chart -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Monthly Sales Overview {{ date('Y') }}</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" style="min-height: 300px; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Yearly Sales Comparison</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="yearlyChart" style="min-height: 300px; height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pending Deliveries</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <table class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Order No</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pending_deliveries as $order)
                                    <tr>
                                        <td>{{ $order->code }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>
                                            @if(!$order->deliveryReceipt)
                                                <span class="badge badge-warning">Pending Delivery</span>
                                            @else
                                                <span class="badge badge-info">Processing</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No pending deliveries</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Orders</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <table class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Order No</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_orders as $order)
                                    <tr>
                                        <td>{{ $order->code }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if(!$order->deliveryReceipt)
                                                <span class="badge badge-warning">Pending</span>
                                            @else
                                                <span class="badge badge-success">Processing</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Items -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Low Stock Items</h3>
            </div>
            <div class="card-body table-responsive p-0" style="height: 300px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Branch</th>
                            <th>Stock</th>
                            <th>Reserved</th>
                            <th>Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($low_stock_items as $item)
                        <tr>
                            <td>{{ $item['product_name'] }}</td>
                            <td>{{ $item['branch_code'] }}</td>
                            <td>{{ $item['stocks'] }}</td>
                            <td>{{ $item['reserved'] }}</td>
                            <td>
                                <span class="badge badge-{{ $item['available'] <= 5 ? 'danger' : 'warning' }}">
                                    {{ $item['available'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No low stock items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
      <!-- Activity Log -->
      <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Activities</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <table class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent_activities as $activity)
                                    <tr>
                                       
                                        <td>

                                            {{ $activity->description }}
                                           
                                        </td>
                                        <td>{{ $activity->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No recent activities</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
</div>@endcan

          
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Sales Chart
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesData = @json($monthly_sales);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.map(item => item.month),
            datasets: [{
                label: 'Monthly Sales (₱)',
                data: salesData.map(item => item.amount),
                backgroundColor: 'rgba(60, 141, 188, 0.2)',
                borderColor: 'rgba(60, 141, 188, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + new Intl.NumberFormat().format(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Sales: ₱' + new Intl.NumberFormat().format(context.raw);
                        }
                    }
                }
            }
        }
    });

    // Yearly Sales Chart
    var yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
    var yearlyData = @json($yearly_sales);
    
    new Chart(yearlyCtx, {
        type: 'bar',
        data: {
            labels: yearlyData.map(item => item.year),
            datasets: [{
                label: 'Yearly Sales (₱)',
                data: yearlyData.map(item => item.amount),
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',    // Green
                    'rgba(0, 123, 255, 0.8)',    // Blue
                    'rgba(23, 162, 184, 0.8)'    // Cyan
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(0, 123, 255, 1)',
                    'rgba(23, 162, 184, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + new Intl.NumberFormat().format(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Sales: ₱' + new Intl.NumberFormat().format(context.raw);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush