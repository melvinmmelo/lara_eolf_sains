@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    @include('layouts.errors')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Inventory Bad Orders</h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory.bad-orders.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> New Inventory Bad Order
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Reference No.</th>
                                    <th>Products</th>
                                    <th>Created By</th>
                                    <th>Date</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($badOrders as $order)
                                    <tr>
                                        <td>{{ $order->reference_name }}</td>
                                        <td>
                                           @foreach ($order->products as $product)
                                                {{ $product['name'] }} ({{ $product['quantity'] }} {{ $product['unit'] }})<br>
                                           @endforeach
                                        </td>
                                        <td>{{ $order->user->fullName ?? 'N/A' }}</td>
                                        <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                        <td>{{ $order->remarks }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No bad orders found</td>
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

<div class="modal fade" id="productsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Products List</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection

@push('scripts')
    <script>
        function showProducts(orderId, products) {
            $('#productsModal').modal('show');
            $('#productsModal').find('.modal-title').text('Products List - Order #' + orderId);
            $('#productsModal').find('tbody').html('');
            products.forEach(function(product) {
                $('#productsModal').find('tbody').append(
                    '<tr>' +
                        '<td>' + product.name + '</td>' +
                        '<td>' + product.quantity + '</td>' +
                        '<td>' + (product.reason || 'N/A') + '</td>' +
                    '</tr>'
                );
            });
        }
    </script>
@endpush