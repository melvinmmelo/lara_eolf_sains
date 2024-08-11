<div class="row">
    <div class="col-sm-8">
        <div class="table-responsive product-list">

            <table class="table table-bordered table-striped ">
                <thead>
                    <tr>
                        <th></th>
                        <th>Product</th>
                        <th>Unit</th>
                        <th style="width:20%">Quantity</th>
                        <th>Unit Price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="d-md-none"><strong>Items</strong></td>
                    </tr>

                    @php
                        $totalAmount = [];
                    @endphp

                    @foreach ($uiProducts as $product)
                        <tr>
                            <td class="align-middle"><button type="button" class="btn btn-xs btn-danger"
                                    onclick="deleteProduct(`{{ $product['code'] }}`)"><i
                                        class="fas fa-trash"></i></button></td>
                            <td class="align-middle">{{ $product['code'] . ' ' . $product['description'] }} </td>
                            <td class="align-middle">{{ $product['unit'] }}</td>
                            <td class="align-middle">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="button"
                                            class="quantity-left-minus btn btn-warning btn-number btn-xs"
                                            data-type="minus" data-field=""
                                            onclick="minusQtyProduct('{{ $product['code'] }}', 'min');">
                                            <span class="fas fa-minus"></span>
                                        </button>
                                    </div>
                                    <input type="text" id="{{ $product['code'] }}" name="quantity"
                                        class="form-control input-number" value="{{ $product['quantity'] }}"
                                        min="1" max="99999" readonly>
                                    <div class="input-group-append">
                                        <button type="button"
                                            class="quantity-right-plus btn btn-success btn-number btn-xs"
                                            data-type="plus" data-field=""
                                            onclick="plusQtyProduct('{{ $product['code'] }}', 'add')">
                                            <span class="fas fa-plus"></span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <input type="text" name="pcodeprice" id="{{ $product['code'] . '_price' }}"
                                    class="label-input" value="{{ $product['price'] }}" readonly>
                            </td>
                            <td class="align-middle">
                                @php $totalAmount[] = $product['quantity'] * $product['price'] @endphp
                                <input type="text" name="pcodeamt" id="{{ $product['code'] . '_amt' }}"
                                    class="label-input" value="{{ $product['quantity'] * $product['price'] }}"
                                    readonly>
                            </td>
                        </tr>
                    @endforeach

                    <!-- Additional rows here -->
                    <tr>
                        <td colspan="5" class="d-md-none"><strong>Total</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="w-100 d-flex justify-content-end">

            <h6 class="font-weight-bold mr-2">Total:</h6>
            <div>
                <input type="text" name="total" id="total" class="label-input"
                    value="{{ array_sum($totalAmount) }}" readonly>
            </div>
        </div>

        <div id="BOContainer" class="w-100 d-flex justify-content-end">
            <h6 class="font-weight-bold mr-2">BO Amount:</h6>

            <div>
                <input type="text" name="bo_amount" id="bo_amount" class="label-input" value="0" readonly>
            </div>

        </div>
    </div>

    <div class="col-sm-4">
        @include('orderProductSum')
    </div>
</div>
