@extends('layouts.app')

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

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12">
                        
                        <label class="form-label" for="customer"><i style="color:red">*</i>Customer</label>
                        <select id="customer" class="form-control select2" name="customer">
                        <option value="0" data-inbound-id="0">-- Select Customer --</option>
                            @foreach($customers as $customer)
                                @php
                                    $inbound = $customer->inbounds->first();
                                @endphp
                                @if ($inbound)
                                    <option value="{{ $customer->id }}" data-inbound-id="{{ $inbound->id }}">
                                        {{ $inbound->id }} - {{ $customer->firstname }} {{ $customer->lastname }} ({{ $customer->storeinfo->storename ?? '' }})
                                    </option>
                                @endif
                            @endforeach

                        </select>
                        <!-- <select id="customer" class="form-control select2bs4">
    <option value="0" data-inbound-id="0">-- Select Customer --</option>
    <option value="1" data-inbound-id="1">Customer 1</option>
    <option value="2" data-inbound-id="2">Customer 2</option>
</select> -->
<!-- <select id="item" class="form-control"></select> -->
                    </div>

                    <div class="col-sm-12">
                        <label class="form-label" for="reddr">Red. DR:</label>
                        <input type="text" class="form-control" id="reddr" name="reddr">
                    </div>

                    <div class="col-sm-12">
                        <label class="form-label" for="bo_percentage">BO Percentage</label>
                        <input type="text" class="form-control" name="bo_percentage">
                    </div>

                    <div class="col-sm-12">
                        <label class="form-label" for="remarks">Remarks</label>
                        <input type="text" class="form-control" name="remarks">
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-6">
                    <label class="form-label" for="item"><i style="color:red">*</i>Item</label>
                    <select class="form-control select2bs4" id="item" name="item">
                        <!-- Options will be populated by JavaScript -->
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
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-primary" id="saveButton">
                Save
            </button>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');

    const customerSelect = $('#customer');
    const itemSelect = $('#item');
    const priceInput = $('#price');
    const quantityInput = $('#quantity');
    const addButton = $('#addItemButton');
    const tableBody = $('#itemsTableBody');
    const tableFooter = $('#itemsTableFoot');

    if (customerSelect.length) {
        console.log('Customer select element found');

        // Initialize select2
        customerSelect.select2();

        customerSelect.on('select2:select', function (e) {
            const data = e.params.data;
            const selectedOption = data.element;
            const customerId = selectedOption.value;
            const inboundId = selectedOption.getAttribute('data-inbound-id');

            console.log(`Selected Customer ID: ${customerId}`);
            console.log(`Selected Inbound ID: ${inboundId}`);

            if (!customerId || !inboundId || customerId === "0" || inboundId === "0") {
                console.error('Customer ID or Inbound ID is missing or invalid');
                itemSelect.html(''); // Clear items dropdown if no valid selection
                return;
            }

            // Fetch products based on customer ID and inbound ID
            fetch(`/get-products/${inboundId}/${customerId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Fetched products:', data);
                    itemSelect.html(''); // Clear previous options

                    if (Array.isArray(data)) {
                        data.forEach(product => {
                            const option = new Option(
                                `${product.description}`,
                                product.code
                            );
                            option.dataset.price = product.price;
                            option.dataset.quantity = product.quantity;
                            itemSelect.append(option);
                        });
                    } else {
                        console.error('Fetched data is not an array:', data);
                    }
                })
                .catch(error => console.error('Error fetching products:', error));
        });

        // Trigger change event to load initial selection if there's a pre-selected value
        if (customerSelect.val()) {
            customerSelect.trigger('change');
        }
    } else {
        console.error('Customer select element not found');
    }

    // Event listener for item selection
    itemSelect.on('change', function() {
        const selectedOption = $(this).find(':selected');
        const price = selectedOption.data('price');
        const quantity = selectedOption.data('quantity');

        // Set quantity and price inputs
        priceInput.val(price);
        quantityInput.val(quantity);
    });

    // Event listener for Add button
    addButton.on('click', function() {
        const selectedItem = itemSelect.find(':selected');
        const description = selectedItem.text();
        const quantity = quantityInput.val();
        const price = priceInput.val();
        const amount = quantity * price;

        // Append a new row to the table with the item details
        const newRow = `<tr>
                            <td>${description}</td>
                            <td>${quantity}</td>
                            <td>${price}</td>
                            <td>${amount}</td>
                        </tr>`;
        tableBody.append(newRow);

        // Update the total amount
        updateTotal();
    });

    function updateTotal() {
        let totalAmount = 0;
        tableBody.find('tr').each(function() {
            const amount = parseFloat($(this).find('td:nth-child(4)').text());
            totalAmount += amount;
        });
        tableFooter.find('td:nth-child(4)').text(totalAmount);
    }

    // Initialize total on page load
    updateTotal();
});

$(document).ready(function() {
    $('#customer').select2();
});

</script>





<!-- <script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');

    const customerSelect = $('#customer');
    const itemSelect = $('#item');
    const priceInput = $('#price');
    const quantityInput = $('#quantity');
    const addButton = $('#addItemButton');
    const tableBody = $('#itemsTableBody');

    if (customerSelect.length) {
        console.log('Customer select element found');

        // Initialize select2
        customerSelect.select2();

        customerSelect.on('select2:select', function (e) {
            const data = e.params.data;
            const selectedOption = data.element;
            const customerId = selectedOption.value;
            const inboundId = selectedOption.getAttribute('data-inbound-id');

            console.log(`Selected Customer ID: ${customerId}`);
            console.log(`Selected Inbound ID: ${inboundId}`);

            if (!customerId || !inboundId || customerId === "0" || inboundId === "0") {
                console.error('Customer ID or Inbound ID is missing or invalid');
                itemSelect.html(''); // Clear items dropdown if no valid selection
                return;
            }

            // Fetch products based on customer ID and inbound ID
            fetch(`/get-products/${inboundId}/${customerId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Fetched products:', data);
                    itemSelect.html(''); // Clear previous options

                    if (Array.isArray(data)) {
                        data.forEach(product => {
                            const option = new Option(
                                `${product.description}`,
                                product.code
                            );
                            option.dataset.price = product.price;
                            option.dataset.quantity = product.quantity;
                            itemSelect.append(option);
                        });
                    } else {
                        console.error('Fetched data is not an array:', data);
                    }
                })
                .catch(error => console.error('Error fetching products:', error));
        });

        // Trigger change event to load initial selection if there's a pre-selected value
        if (customerSelect.val()) {
            customerSelect.trigger('change');
        }
    } else {
        console.error('Customer select element not found');
    }

    // Event listener for item selection
    itemSelect.on('change', function() {
        const selectedOption = $(this).find(':selected');
        const price = selectedOption.data('price');
        const quantity = selectedOption.data('quantity');

        // Set quantity and price inputs
        priceInput.val(price);
        quantityInput.val(quantity);
    });

    // Event listener for Add button
    addButton.on('click', function() {
        const selectedItem = itemSelect.find(':selected');
        const description = selectedItem.text();
        const quantity = quantityInput.val();
        const price = priceInput.val();
        const amount = quantity * price;

        // Append a new row to the table with the item details
        const newRow = `<tr>
                            <td>${description}</td>
                            <td>${quantity}</td>
                            <td>${price}</td>
                            <td>${amount}</td>
                        </tr>`;
        tableBody.append(newRow);
    });
});

$(document).ready(function() {
    $('#customer').select2();
});

</script> -->




<!-- <script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');

    const customerSelect = $('#customer');
    const itemSelect = $('#item');
    const priceInput = $('#price');
    const quantityInput = $('#quantity');

    if (customerSelect.length) {
        console.log('Customer select element found');

        // Initialize select2
        customerSelect.select2();

        customerSelect.on('select2:select', function (e) {
            const data = e.params.data;
            const selectedOption = data.element;
            const customerId = selectedOption.value;
            const inboundId = selectedOption.getAttribute('data-inbound-id');

            console.log(`Selected Customer ID: ${customerId}`);
            console.log(`Selected Inbound ID: ${inboundId}`);

            if (!customerId || !inboundId || customerId === "0" || inboundId === "0") {
                console.error('Customer ID or Inbound ID is missing or invalid');
                itemSelect.html(''); // Clear items dropdown if no valid selection
                return;
            }

            // Fetch products based on customer ID and inbound ID
            fetch(`/get-products/${inboundId}/${customerId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Fetched products:', data);
                    itemSelect.html(''); // Clear previous options

                    if (Array.isArray(data)) {
                        data.forEach(product => {
                            const option = new Option(
                                `${product.description}`,
                                product.code
                            );
                            option.dataset.price = product.price;
                            option.dataset.quantity = product.quantity;
                            itemSelect.append(option);
                        });
                    } else {
                        console.error('Fetched data is not an array:', data);
                    }
                })
                .catch(error => console.error('Error fetching products:', error));
        });

        // Trigger change event to load initial selection if there's a pre-selected value
        if (customerSelect.val()) {
            customerSelect.trigger('change');
        }
    } else {
        console.error('Customer select element not found');
    }

    // Event listener for item selection
    itemSelect.on('change', function() {
        const selectedOption = $(this).find(':selected');
        const price = selectedOption.data('price');
        const quantity = selectedOption.data('quantity');

        // Set quantity and price inputs
        priceInput.val(price);
        quantityInput.val(quantity);
    });
});

$(document).ready(function() {
    $('#customer').select2();
});

</script> -->


@endsection
