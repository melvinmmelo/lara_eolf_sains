@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Product History: {{ $productCode }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('stock-reconciliation.product', $productCode) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Product Details
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="nav nav-tabs" id="historyTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="dpr-tab" data-toggle="tab" href="#dpr" role="tab">
                                DPR History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="order-tab" data-toggle="tab" href="#order" role="tab">
                                Order History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="activity-tab" data-toggle="tab" href="#activity" role="tab">
                                Activity Logs
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" id="historyTabContent">
                        <!-- DPR History -->
                        <div class="tab-pane fade show active" id="dpr" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>DR Number</th>
                                            <th>Date</th>
                                            <th>Quantity</th>
                                            <th>Hold</th>
                                            <th>Net Quantity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($dprHistory as $dpr)
                                            <tr>
                                                <td>{{ $dpr['dr_no'] }}</td>
                                                <td>{{ $dpr['date']->format('Y-m-d H:i') }}</td>
                                                <td>{{ $dpr['quantity'] }}</td>
                                                <td>{{ $dpr['hold'] }}</td>
                                                <td>{{ $dpr['quantity'] - $dpr['hold'] }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $dpr['status'] == 'Completed' ? 'success' : 'secondary' }}">
                                                        {{ $dpr['status'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No DPR history found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Order History -->
                        <div class="tab-pane fade" id="order" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Order Number</th>
                                            <th>Date</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orderHistory as $order)
                                            <tr>
                                                <td>{{ $order['order_no'] }}</td>
                                                <td>{{ $order['date']->format('Y-m-d H:i') }}</td>
                                                <td>{{ $order['quantity'] }}</td>
                                                <td>
                                                    <span class="badge badge-{{ in_array($order['status'], ['Completed', 'Paid']) ? 'success' : 'secondary' }}">
                                                        {{ $order['status'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No order history found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Activity Logs -->
                        <div class="tab-pane fade" id="activity" role="tabpanel">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
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
                                
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $logs->links() }}
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
