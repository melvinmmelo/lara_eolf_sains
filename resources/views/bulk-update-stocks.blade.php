@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bulk Update Stocks</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Bulk Update Stocks</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <form action="{{ route('bulk.update.stocks.page') }}" method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by product code..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-success" onclick="submitBulkUpdate()">Update Selected Products</button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @include('layouts.errors')

                <form id="bulkUpdateForm">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Product Code</th>
                                <th>Current Stock</th>
                                <th>Reserved</th>
                                <th>Add Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="product-select" data-id="{{ $product->id }}">
                                    </td>
                                    <td>{{ $product->product_code }}</td>
                                    <td>{{ $product->stocks }}</td>
                                    <td>{{ $product->reserved }}</td>
                                    <td>
                                        <input type="number" class="form-control quantity-input" min="0" value="0" 
                                            data-id="{{ $product->id }}" disabled>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>

                <div class="mt-2">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('custom_js')
<script>
$(document).ready(function() {
    // Handle select all checkbox
    $('#selectAll').change(function() {
        const isChecked = $(this).prop('checked');
        $('.product-select').prop('checked', isChecked);
        $('.quantity-input').prop('disabled', !isChecked);
    });

    // Handle individual checkboxes
    $('.product-select').change(function() {
        const id = $(this).data('id');
        const isChecked = $(this).prop('checked');
        $(`input[data-id="${id}"].quantity-input`).prop('disabled', !isChecked);

        // Update select all checkbox
        const allChecked = $('.product-select:checked').length === $('.product-select').length;
        $('#selectAll').prop('checked', allChecked);
    });
});

function submitBulkUpdate() {
    const selectedProducts = [];
    
    $('.product-select:checked').each(function() {
        const id = $(this).data('id');
        const quantity = $(`input[data-id="${id}"].quantity-input`).val();
        
        selectedProducts.push({
            id: id,
            quantity: parseInt(quantity)
        });
    });

    if (selectedProducts.length === 0) {
        alert('Please select at least one product');
        return;
    }

    $.ajax({
        url: '{{ route("bulk.update.stocks") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            products: selectedProducts
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Error updating stocks');
            }
        },
        error: function(xhr) {
            alert('Error updating stocks');
        }
    });
}
</script>
@endsection
