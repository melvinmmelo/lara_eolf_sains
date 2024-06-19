@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('contents')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Bad Order Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Bad Orders</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-body">
            @include('layouts.errors')
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form id="finalSaveForm">
                @csrf
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="form-label" for="customer"><i style="color:red">*</i>Customer</label>
                            <select id="customer" class="form-control select2bs4" name="customer_id">
                                <option value="0">-- Select Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" data-store-id="{{ $customer->storeinfo->id ?? '' }}">
                                        {{ $customer->firstname }} {{ $customer->lastname }} ({{ $customer->storeinfo->storename ?? 'No Store Info' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-12">
                            <label class="form-label" for="re_dr">Red. DR:</label>
                            <input type="text" class="form-control" id="re_dr" name="re_dr">
                        </div>

                        <div class="col-sm-12">
                            <label class="form-label" for="bo_percentage">BO Percentage</label>
                            <input type="text" class="form-control" id="bo_percentage" name="bo_percentage">
                        </div>

                        <div class="col-sm-12">
                            <label class="form-label" for="remarks">Remarks</label>
                            <input type="text" class="form-control" id="remarks" name="remarks">
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label class="form-label" for="item"><i style="color:red">*</i>Item</label>
                        <select class="form-control select2bs4" id="item" name="item">
                            <option>-- Select Item --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->p_code }}" data-ptype-code="{{ $item->ptype_code }}" data-price="{{ $item->p_price }}" data-unit="{{ $item->p_unit }}" data-quantity="{{ $item->p_quant }}">
                                    {{ $item->description }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-2">
                        <label class="form-label" for="price">Unit Price</label>
                        <input type="text" class="form-control" id="price" name="price">
                    </div>

                    <div class="col-sm-2">
                        <label class="form-label" for="quantity">Quantity</label>
                        <input type="text" class="form-control" id="quantity" name="quantity">
                    </div>

                    <div class="col-sm-2">
                        <div><label class="form-label" for="cust_fname">&nbsp; </label></div>
                        <button type="button" class="btn btn-success" id="addItemButton">
                            Add
                        </button>
                    </div>
                </div>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <!-- <th>ptype_code</th> -->
                            <th>code</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <!-- Dynamic rows will be appended here -->
                        
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align:right">Total:</th>
                            <th id="totalAmount"></th>
                        </tr>
                    </tfoot>
                </table>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" id="finalSaveButton">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');

    // Set CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    const sessionId = '{{ session()->getId() }}'; // Get session ID from Laravel
    // Select DOM elements
    const customerSelect = $('#customer');
    const itemSelect = $('#item');
    const priceInput = $('#price');
    const quantityInput = $('#quantity');
    const addButton = $('#addItemButton');
    const tableBody = $('#itemsTableBody');
    const totalAmount = $('#totalAmount');
    let total = 0;

    // Initialize select2 for customer and item selection
    // customerSelect.select2();
    // itemSelect.select2();

    customerSelect.on('select2:select', function (e) {
        const data = e.params.data;
        const selectedOption = data.element;
        const customerId = selectedOption.value;
        const storeId = selectedOption.getAttribute('data-store-id');

        console.log(`Selected Customer ID: ${customerId}`);
        console.log(`Selected Store ID: ${storeId}`);
    });

    // Event listener for item selection
    itemSelect.on('change', function() {
        const selectedOption = $(this).find(':selected');
        const price = selectedOption.data('price');
        const quantity = selectedOption.data('quantity');
        priceInput.val(price);
        quantityInput.val(quantity);
    });

    // Event listener for Add button
    addButton.on('click', function() {
        const selectedItem = itemSelect.find(':selected');
        const description = selectedItem.text();
        const ptypeCode = selectedItem.data('ptypeCode');
        const code = selectedItem.val(); // Get the value of the selected item (p_code)
        const unit = selectedItem.data('unit');
        const quantity = quantityInput.val();
        const price = priceInput.val();
        const amount = quantity * price;

        // Save the item to the temporary table via AJAX
        const tempData = {
            customer_id: customerSelect.val(),
            store_id: customerSelect.find(':selected').data('store-id'),
            ptype_code: ptypeCode,
            code: code,
            description: description,
            quantity: quantity,
            price: price,
            amount: amount,
            unit: unit,
            session_id: sessionId // Include the session ID
        };

        $.ajax({
            url: '/save-temp-bad-order',
            method: 'POST',
            data: tempData,
            success: function(response) {
                console.log('Item saved to temporary table');
                const newRow = `<tr>
                                    <td>${code}</td>
                                    <td>${description}</td>
                                    <td>${quantity}</td>
                                    <td>${price}</td>
                                    <td>${amount}</td>
                                </tr>`;
                tableBody.append(newRow);

                total += amount;
                totalAmount.text(total.toFixed(2));

                // Clear inputs
                priceInput.val('');
                quantityInput.val('');
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // Event listener for form submission
    $('#finalSaveForm').on('submit', function(e) {
        e.preventDefault();
        const selectedOption = $('#customer').find('option:selected');
        const customer_id = selectedOption.val();
        const store_id = selectedOption.data('store-id');

        // Ensure these values are included in the form submission
        $(this).append('<input type="hidden" name="customer_id" value="' + customer_id + '">');
        $(this).append('<input type="hidden" name="store_id" value="' + store_id + '">');
        const formData = $(this).serialize();

        $.ajax({
            url: '{{ route("addbadorder.store") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                window.location.href = '{{ route("addbadorder.create") }}';
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});
</script>


@endsection
