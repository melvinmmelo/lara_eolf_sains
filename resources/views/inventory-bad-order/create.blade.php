@extends('layouts.app')

@section('custom_css')
   <style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 14px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 14px;
    }
   </style>
@endsection

@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @include('layouts.errors')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                       Create New Inventory Bad Order
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('inventory.bad-orders') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('inventory.bad-orders.store') }}" 
                          method="POST" 
                          id="badOrderForm">
                        @csrf
                        @if(isset($badOrder))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reference_name">Reference no. <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('reference_name') is-invalid @enderror" 
                                           id="reference_name" 
                                           name="reference_name" 
                                           value="{{ old('reference_name', $reference_name ?? '') }}" 
                                           required>
                                    @error('reference_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <small class="text-danger">This is system auto generated.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                              id="remarks" 
                                              name="remarks" 
                                              rows="1">{{ old('remarks', $remarks ?? '') }}</textarea>
                                    @error('remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Products List</h6>
                                <button type="button" class="btn btn-light btn-sm" id="addProductBtn">
                                    <i class="fas fa-plus"></i> Add Product
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="45%">Product <span class="text-danger">*</span></th>
                                                <th width="15%">Quantity <span class="text-danger">*</span></th>
                                                <th width="30%">Reason</th>
                                                <th width="10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productsContainer">
                                            @if(isset($badOrder) && $badOrder->products)
                                                @foreach(json_decode($badOrder->products, true) as $index => $product)
                                                    <tr class="product-row">
                                                    <td>
                                                        <select class="form-control select2bs4 product-select" 
                                                                name="products[{{ $index }}][code]" required>
                                                            <option value="">Select Product</option>
                                                            @foreach($itemMasterData as $item)
                                                                <option value="{{ $item->id }}" 
                                                                    data-code="{{ $item->product_code }}"
                                                                    data-name="{{ $item->product_description }}"
                                                                    data-unit="{{ $item->unit }}"
                                                                    {{ $product['code'] == $item->product_code ? 'selected' : '' }}>
                                                                    {{ $item->product_code }} - {{ $item->product_description }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               name="products[{{ $index }}][quantity]" 
                                                               value="{{ $product['quantity'] }}" 
                                                               required 
                                                               min="1">
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="products[{{ $index }}][reason]" 
                                                               value="{{ $product['reason'] ?? '' }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm remove-product">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div id="noProductsMessage" class="text-center text-muted py-3 {{ isset($badOrder) && $badOrder->products ? 'd-none' : '' }}">
                                    No products added yet. Click "Add Product" to start adding products.
                                </div>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Bad Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let productIndex = {{ isset($badOrder) ? count(json_decode($badOrder->products, true)) : 0 }};

    // Initialize select2 for existing product selects
    $('.product-select').select2();

    $('#addProductBtn').click(function() {
        const template = `
            <tr class="product-row">
                    <td>
                        <select class="form-control select2bs4 product-select" name="products[${productIndex}][id]" required>
                            <option value="">Select Product</option>
                            @foreach($itemMasterData as $item)
                                <option value="{{ $item->id }}" 
                                    data-code="{{ $item->product_code }}"
                                    data-name="{{ $item->product_description }}"
                                    data-unit="{{ $item->unit }}">
                                    {{ $item->product_code }} - {{ $item->product_description }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="products[${productIndex}][quantity]" required min="1">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="products[${productIndex}][reason]">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-product">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
        `;

        $('#productsContainer').append(template);
        $('#noProductsMessage').addClass('d-none');
        
        // Initialize select2 for the new select
        $('.product-select').last().select2();
        
        productIndex++;
    });

    // Remove product row
    $(document).on('click', '.remove-product', function() {
        $(this).closest('.product-row').remove();
        if ($('.product-row').length === 0) {
            $('#noProductsMessage').removeClass('d-none');
        }
    });

    // Form validation before submit
    $('#badOrderForm').submit(function(e) {
        if ($('.product-row').length === 0) {
            e.preventDefault();
            alert('Please add at least one product to the bad order.');
            return false;
        }
        return true;
    });
});
</script>
@endpush
@endsection
