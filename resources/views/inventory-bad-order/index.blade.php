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
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form action="{{ route('inventory.bad-orders') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search by product name..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('inventory.bad-orders') }}" class="btn btn-outline-danger">
                                                <i class="fas fa-times"></i> Clear
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Reference No. <small class="text-muted"> - Rollback reason</small></th>
                                    <th>Products</th>
                                    <th>Created By</th>
                                    <th>Date</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($badOrders->isEmpty() && request()->has('search'))
                                    <tr>
                                        <td colspan="7" class="text-center">No bad orders found matching your search criteria.</td>
                                    </tr>
                                @else
                                @forelse($badOrders as $order)
                                    <tr>
                                        <td>{{ $order->reference_name }}
                                            @if($order->is_rolled_back)
                                                <small class="text-muted"> - {{ $order->rollback_reason }}</small>
                                            @endif
                                        </td>
                                        <td>
                                           @foreach ($order->products as $product)
                                                {{ $product['name'] }} ({{ $product['quantity'] }} {{ $product['unit'] }})<br>
                                           @endforeach
                                        </td>
                                        <td>{{ $order->user->fullName ?? 'N/A' }}</td>
                                        <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                        <td>{{ $order->remarks }}</td>
                                        <td>
                                            @if($order->is_rolled_back)
                                                <span class="badge badge-warning">Rolled Back</span>
                                                <small class="text-muted d-block">{{ $order->rolled_back_at->format('M d, Y h:i A') }}</small>
                                                <small class="text-muted">by {{ $order->rolledBackBy->fullName ?? 'N/A' }}</small>
                                            @else
                                                <span class="badge badge-success">Active</span>
                                                <br>
                                                @can("admin")
                                                @if($order->canRollback())
                                                    <button type="button" class="btn btn-warning btn-sm mt-1" 
                                                            data-toggle="modal" 
                                                            data-target="#rollbackModal" 
                                                            data-order-id="{{ $order->id }}"
                                                            data-order-reference="{{ $order->reference_name }}">
                                                        <i class="fas fa-undo"></i> Rollback
                                                    </button>
                                                @endif
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No bad orders found</td>
                                    </tr>
                                @endforelse
                                @endif
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

<!-- Rollback Confirmation Modal -->
<div class="modal fade" id="rollbackModal" tabindex="-1" role="dialog" aria-labelledby="rollbackModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rollbackModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning"></i> Confirm Rollback
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rollbackForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> This action will rollback the bad order and restore the stock quantities back to inventory.
                    </div>
                    
                    <p>Are you sure you want to rollback bad order <strong id="rollbackOrderReference"></strong>?</p>
                    
                    <div class="form-group">
                        <label for="rollback_reason">Rollback Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rollback_reason" name="rollback_reason" rows="3" 
                                  placeholder="Please provide a reason for rolling back this bad order..." 
                                  required maxlength="500"></textarea>
                        <small class="form-text text-muted">Maximum 500 characters</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo"></i> Confirm Rollback
                    </button>
                </div>
            </form>
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

        // Handle rollback modal
        $('#rollbackModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var orderId = button.data('order-id');
            var orderReference = button.data('order-reference');
            
            var modal = $(this);
            modal.find('#rollbackOrderReference').text(orderReference);
            modal.find('#rollbackForm').attr('action', '{{ route("inventory.bad-orders.rollback", "__ORDER_ID__") }}'.replace('__ORDER_ID__', orderId));
            modal.find('#rollback_reason').val('');
        });

        // Handle rollback form submission
        $('#rollbackForm').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var originalText = submitBtn.html();
            
            // Disable submit button and show loading
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            // Submit form
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Reload page to show updated data
                    location.reload();
                },
                error: function(xhr) {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html(originalText);
                    
                    // Show error message
                    var errorMessage = 'An error occurred while processing the rollback.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('\n');
                    }
                    
                    alert('Error: ' + errorMessage);
                }
            });
        });
    </script>
@endpush