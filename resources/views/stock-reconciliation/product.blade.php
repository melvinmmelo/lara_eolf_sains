@extends('layouts.app')

@section('contents')
<div class="container-fluid">

    @include('layouts.errors')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Product Stock Details: {{ $item->product_code }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('stock-reconciliation.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('stock-reconciliation.history', $item->product_code) }}" class="btn btn-info">
                            <i class="fas fa-history"></i> View History
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Current Stock Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Product Code:</th>
                                            <td>{{ $item->product_code }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description:</th>
                                            <td>{{ $item->product_description }}</td>
                                        </tr>
                                        <tr>
                                            <th>Current Stock:</th>
                                            <td>{{ $item->stocks }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reserved:</th>
                                            <td>{{ $item->reserved }}</td>
                                        </tr>
                                        <tr>
                                            <th>Available:</th>
                                            <td>{{ $item->stocks - $item->reserved }}</td>
                                        </tr>
                                        <tr>
                                            <th>Unit:</th>
                                            <td>{{ $item->unit }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Expected Stock Information (Based on Transactions)</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Total Inbounds:</th>
                                            <td>{{ $totalInbounds }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Orders:</th>
                                            <td>{{ $totalOrders }}</td>
                                        </tr>
                                        <tr>
                                            <th>Stock Based on Transactions:</th>
                                            <td class="{{ $remainingStocksBasedOnTransactions != 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $remainingStocksBasedOnTransactions > 0 ? '+' : '' }}{{ $remainingStocksBasedOnTransactions }}
                                            </td>
                                        </tr>   

                                        <tr>
                                            <th>Item Master Data Reserved:</th>
                                            <td>{{ $item->reserved }}</td>
                                        </tr>

                                        <tr>
                                            <th>Item Master Data Stock:</th>
                                            <td>{{ $item->stocks }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0">Stock Adjustment</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('stock-reconciliation.fix', $item->product_code) }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="new_stock">New Stock Value:</label>
                                                    <input type="number" name="new_stock" id="new_stock" 
                                                           class="form-control" value="{{ $remainingStocksBasedOnTransactions }}" required min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="new_reserved">New Reserved Value:</label>
                                                    <input type="number" name="new_reserved" id="new_reserved" 
                                                           class="form-control" value="0" required min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="notes">Adjustment Notes:</label>
                                                    <input type="text" name="notes" id="notes" class="form-control"
                                                           placeholder="Reason for adjustment" required>
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="submit" class="btn btn-warning btn-block"
                                                        onclick="return confirm('Are you sure you want to adjust the stock values?')">
                                                    Update Stock
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Inbound Stock (DPRs)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>DR Number</th>
                                                    <th>Date</th>
                                                    <th>Quantity</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($dprHistory as $dpr)
                                                    <tr>
                                                        <td>{{ $dpr['dr_no'] }}</td>
                                                        <td>{{ $dpr['date']->format('Y-m-d') }}</td>
                                                        <td>{{ $dpr['quantity'] - $dpr['hold'] }}</td>
                                                        <td>{{ $dpr['status'] }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">No DPR records found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <th colspan="2">Total:</th>
                                                    <th>{{ $totalInbounds }}</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" style="max-height: 300px; overflow-y: auto;">
                            <div class="card mb-4">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">Ordered Stock (Orders)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Order Number</th>
                                                    <th>Date</th>
                                                    <th>Quantity</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $orderSum = [] @endphp
                                                @forelse($orderHistory as $order)
                                                    <tr>
                                                        <td>{{ $order['order_no'] }}</td>
                                                        <td>{{ $order['date']->format('Y-m-d') }}</td>
                                                        <td>@php echo $orderSum[] = $order['quantity'] @endphp</td>
                                                        <td>{{ $order['status'] }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">No order records found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light">
                                                    <th colspan="2">Total:</th>
                                                    <th>{{ array_sum($orderSum) }}</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">Recent Activity</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>User</th>
                                                    <th>Action</th>
                                                    <th>Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($logs as $log)
                                                    <tr>
                                                        <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>{{ optional($log->causer)->fullName ?? 'System' }}</td>
                                                        <td>{{ $log->description }}</td>
                                                        <td>
                                                            @if($log->properties->count() > 0)
                                                                <button class="btn btn-sm btn-link" type="button" 
                                                                        data-toggle="collapse" data-target="#log-{{ $log->id }}">
                                                                    Show Details
                                                                </button>
                                                                <div class="collapse mt-2" id="log-{{ $log->id }}">
                                                                    <div class="card card-body">
                                                                        <pre>{{ json_encode($log->properties->toArray(), JSON_PRETTY_PRINT) }}</pre>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                No details
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">No activity logs found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
