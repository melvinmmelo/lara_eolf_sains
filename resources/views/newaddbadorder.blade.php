@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('contents')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Bad Order</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Create Bad Order</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                @include('layouts.errors')

                <form action="{{ route('newbo.storetp') }}" method="POST">
                    @csrf

                    <input type="hidden" name="session_bo_id" value="{{ $sessionBo }}" required readonly>

                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <label class="form-label" for="item"><i style="color:red">*</i>Item</label>
                            <select class="form-control select2bs4" id="item" name="item">
                                <option>-- Select Item --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->p_code }}" data-ptype-code="{{ $item->ptype_code }}"
                                        data-price="{{ $item->p_price }}" data-unit="{{ $item->p_unit }}"
                                        data-quantity="{{ $item->p_quant }}">
                                        {{ $item->description }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-2">
                            <label class="form-label" for="price">Unit Price</label>
                            <input type="text" class="form-control" id="price" name="price" readonly>
                        </div>

                        <div class="col-sm-2">
                            <label class="form-label" for="quantity">Quantity</label>
                            <input type="text" class="form-control" id="quantity" name="quantity">
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

                <form action="{{ route('newbo.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <input type="hidden" name="session_bo" class="form-control mb-2 mt-2" value="{{ $sessionBo }}"
                            required readonly>
                    </div>

                    <div class="form-group">

                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="customer"><i style="color:red">*</i>Customer</label>
                                <select id="customer" class="form-control select2bs4" name="equipment_store_id" required>
                                    <option value="0">-- Select Customer --</option>
                                    @foreach ($equipment as $equip)
                                        <option value="{{ $equip->equipmentStore->id }}">
                                            {{ $equip->code . ' ' . $equip->equipmentStore->customer->fullName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-12">
                                <label class="form-label" for="bo_percentage"><i style="color:red">*</i>BO
                                    Percentage</label>
                                <input type="number" class="form-control" id="bo_percentage" name="bo_percentage"
                                    max="100" required>
                            </div>

                            <div class="col-sm-12">
                                <label class="form-label" for="remarks">Remarks</label>
                                <input type="text" class="form-control" id="remarks" name="remarks">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mb-2 mt-2">
                            Save
                        </button>
                    </div>
                </form>


            </div>
        </div>
    </section>
@endsection


@section('custom_js')
    <script>

            const sessionBo = '{{ $sessionBo }}';

            const itemSelect = $('#item');
            const priceInput = $('#price');
            const quantityInput = $('#quantity');



            // Event listener for item selection
            itemSelect.on('change', function() {
                const selectedOption = $(this).find(':selected');
                const price = selectedOption.data('price');
                const quantity = selectedOption.data('quantity');
                priceInput.val(price);

                // focus on quantity input
                quantityInput.focus();

            });

            // convert deleteItem function to ajax request
            function deleteItem(id) {
                $.ajax({
                    url: `/newbo/${id}/delete`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

    </script>
@endsection
