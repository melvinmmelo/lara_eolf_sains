@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Update Stocks</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Update Stocks</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <form action="{{ route('update.stocks.page') }}" method="GET" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by product code..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                @include('layouts.errors')

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Reserved</th>
                            <th>Hold</th>
                            <th>Stocks</th>
                            <th>Last Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->reserved }}</td>
                                <td>{{ $product->hold_quantity }}</td>
                                <td>{{ $product->stocks }}</td>
                                <td>{{ $product->updated_at }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" 
                                        onclick="openUpdateModal('{{ $product->id }}', '{{ $product->stocks }}', '{{ $product->reserved }}', '{{ $product->product_code }}')">
                                        Update
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="mt-2">
                {{ $products->links('pagination::bootstrap-4') }}
                </div>
            </div>

        </div>


    </section>

    <!-- Update Form Modal -->
    <div class="modal" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Stocks</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateForm">
                        <input type="hidden" id="productId" name="id">
                        <div class="form-group">
                            <label>Product Code</label>
                            <input type="text" class="form-control" id="productCode" readonly>
                        </div>
                        <div class="form-group">
                            <label>Stocks</label>
                            <input type="number" class="form-control" id="stocks" name="stocks" required min="0">
                        </div>
                        <div class="form-group">
                            <label>Reserved</label>
                            <input type="number" class="form-control" id="reserved" name="reserved" required min="0">
                        </div>
                    </form>
                </div>
                <div class="modal-body" id="errorMessages" style="display: none;">
                    <div class="alert alert-danger"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitUpdate()">Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
<script>
function openUpdateModal(id, stocks, reserved, productCode) {
    $('#productId').val(id);
    $('#productCode').val(productCode);
    $('#stocks').val(stocks);
    $('#reserved').val(reserved);
    $('#errorMessages').hide();
    $('#updateModal').modal({
        show: true,
        backdrop: 'static',
        keyboard: false
    });
}

function submitUpdate() {
    const data = {
        id: $('#productId').val(),
        stocks: $('#stocks').val(),
        reserved: $('#reserved').val(),
        _token: '{{ csrf_token() }}'
    };

    $.ajax({
        url: '{{ route("update.item.stocks") }}',
        method: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                $('#updateModal').modal('hide');
                location.reload();
            } else {
                showError(response.message);
            }
        },
        error: function(xhr) {
            showError('Error updating stocks');
        }
    });
}

function showError(message) {
    $('#errorMessages').show().find('.alert').text(message);
}
</script>
@endsection
