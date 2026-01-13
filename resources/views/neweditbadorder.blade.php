@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Bad Order {{ $badOrder->id }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Edit Bad Order</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                @include('layouts.errors')

                <form action="{{ route('newbo.additem') }}" method="POST">
                    @csrf

                    <input type="hidden" name="new_bad_order_id" value="{{ $badOrder->id }}" required readonly>

                    {{-- <input type="hidden" name="session_bo_id" value="{{ $sessionBo }}" required readonly> --}}

                    <div class="row mb-4">

                        <div class="col-sm-4">
                            <label class="form-label" for="item"><i style="color:red">*</i>Price Level</label>
                            <select class="form-control select2bs4" id="priceLevel" name="priceLevel">
                                <option>-- Select Pricing Level --</option>
                                @foreach ($pricingLevels as $priceLevel)
                                    <option value="{{ $priceLevel->id }}">
                                        {{ $priceLevel->pl_desc }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label" for="item"><i style="color:red">*</i>Item</label>
                            <select class="form-control select2bs4" id="item" name="item">
                                <option value="">-- Select Item --</option>
                                @if(count($badOrderPrices) > 0)
                                    @foreach ($badOrderPrices as $badPrice)
                                        <option value="{{ $badPrice->ptype_code }}" data-ptype-code="{{ $badPrice->ptype_code }}"
                                            data-price="{{ $badPrice->price }}">
                                            {{ $badPrice->ptype_name . ' (' . $badPrice->ptype_code . ')' . ' - ' . number_format($badPrice->price, 2) }}
                                        </option>
                                    @endforeach
                                @else
                                    @foreach ($items as $item)
                                        <option value="{{ $item->code }}" data-ptype-code="{{ $item->code }}"
                                            data-price="0">
                                            {{ $item->name . ' (' . $item->code . ')' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <label class="form-label" for="price">Unit Price</label>
                            <input type="text" class="form-control" id="price" name="price" required readonly>
                        </div>

                        <div class="col-sm-2">
                            <label class="form-label" for="quantity">Quantity</label>
                            <input type="text" class="form-control" id="quantity" name="quantity" required>
                        </div>

                        <div class="col-sm-2">
                            <div><label class="form-label" for="cust_fname">&nbsp; </label></div>
                            <button type="submit" class="btn btn-success" id="addItemButton">
                                Add
                            </button>
                        </div>
                    </div>
                </form>

                <table id="" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Amount</th>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($boProducts as $product)
                            <tr>
                                <td>{{ $product->ptype_code }}</td>
                                <td>{{ $product->description }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $product->amount }}</td>
                                <td>
                                    <button class="btn btn-danger" onclick="deleteItem({{ $product->id }})">
                                        <i class="fa fa-trash"></i></button>

                                    <button class="btn btn-success"
                                        onclick="editItem(`{{ $product->id }}`, `{{ $product->quantity }}`)">
                                        <i class="fa fa-pencil"></i></button>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align:right">Total:</th>
                            <th id="totalAmount">{{ formatNumber($totalAmount) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    </section>

    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('newbo.updateboitem') }}">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit bad order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="item_id" name="item_id" required readonly>
                                    <label class="form-label" for="code"><i style="color:red">*</i>Quantity</label>
                                    <input type="text" class="form-control" id="quantity" name="quantity" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save changes</button>
                        </div>
                    </div>
            </form>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection


@section('custom_js')
    <script>
        const itemSelect = $('#item');
        const priceInput = $('#price');
        const quantityInput = $('#quantity');
        const priceLevelSelect = $('#priceLevel');

        // Event listener for price level change - load items via AJAX
        priceLevelSelect.on('change', function() {
            const selectedPriceLevel = $(this).val();

            if (!selectedPriceLevel) {
                // Clear item dropdown
                itemSelect.empty();
                itemSelect.append('<option value="">-- Select Item --</option>');
                priceInput.val('');
                return;
            }

            // Fetch bad order prices for the selected price level
            fetch(`/bo-get-prices-by-level/${selectedPriceLevel}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Bad order prices:', data);

                    // Clear and populate item dropdown
                    itemSelect.empty();
                    itemSelect.append('<option value="">-- Select Item --</option>');

                    if (data && data.length > 0) {
                        data.forEach(function(badPrice) {
                            const optionText = `${badPrice.ptype_name} (${badPrice.ptype_code}) - ${parseFloat(badPrice.price).toFixed(2)}`;
                            const option = $('<option>', {
                                value: badPrice.ptype_code,
                                'data-ptype-code': badPrice.ptype_code,
                                'data-price': badPrice.price,
                                text: optionText
                            });
                            itemSelect.append(option);
                        });
                    } else {
                        itemSelect.append('<option value="">No prices found for this level</option>');
                    }

                    // Reinitialize select2 if used
                    if (itemSelect.hasClass('select2bs4')) {
                        itemSelect.select2('destroy');
                        itemSelect.select2({
                            theme: 'bootstrap4'
                        });
                    }

                    priceInput.val('');
                })
                .catch(error => {
                    console.log('Error fetching bad order prices:', error);
                    alert('Error loading items for selected price level.');
                });
        });

        // Event listener for item selection
        itemSelect.on('change', function() {
            const selectedOption = $(this).find(':selected');
            const price = selectedOption.data('price');
            priceInput.val(price);

            // focus on quantity input
            quantityInput.focus();
        });

        // convert deleteItem function to ajax request
        function deleteItem(id) {

            console.log(id);

            $.ajax({
                url: `/newbo-item/${id}/delete`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    location.reload();
                },
                error: function(error) {
                    alert('Error deleting item.');
                    console.log(error);
                }
            });
        }

        function getPricing() {

            let item = itemSelect.val();
            let priceLevel = priceLevelSelect.val();

            if (!item || !priceLevel) {
                return;
            }

            fetch(`/bo-get-price/${priceLevel}/${item}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Price data:', data);
                    if (data.p_price) {
                        priceInput.val(data.p_price);
                    } else {
                        priceInput.val('0');
                        alert('Price not found for selected item and price level.');
                    }
                })
                .catch(error => {
                    console.log('Error fetching price:', error);
                    priceInput.val('0');
                });
        }

        itemSelect.on('change', getPricing);

        function editItem(id, quantity) {
            document.getElementById('item_id').value = id;
            document.getElementById('quantity').value = quantity;
            $('#editModal').modal('show');
        }
    </script>
@endsection
