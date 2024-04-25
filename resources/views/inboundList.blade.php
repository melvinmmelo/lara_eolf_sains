<div class="row">
    <div class="col-sm-8">
        <div class="table-responsive">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit</th>
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
                            <td class="align-middle">{{ $product['code'] }} </td>
                            <td class="align-middle">

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="button" class="quantity-left-minus btn btn-danger btn-number"
                                            data-type="minus" data-field=""
                                            onclick="minusQtyProduct('{{ $product['code'] }}', 'min');">
                                            <span class="fas fa-minus"></span>
                                        </button>
                                    </div>
                                    <input type="text" id="{{ $product['code'] }}" name="quantity"
                                        class="form-control input-number" value="{{ $product['quantity'] }}"
                                        min="1" max="99999">
                                    <div class="input-group-append">
                                        <button type="button" class="quantity-right-plus btn btn-success btn-number"
                                            data-type="plus" data-field=""
                                            onclick="plusQtyProduct('{{ $product['code'] }}', 'add')">
                                            <span class="fas fa-plus"></span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">{{ $product['unit'] }}</td>
                            <td class="align-middle">{{ $product['price'] }}</td>
                            <td class="align-middle">{{ $totalAmount[] = $product['quantity'] * $product['price'] }}
                            </td>
                        </tr>
                    @endforeach

                    <!-- Additional rows here -->
                    <tr>
                        <td colspan="5" class="d-md-none"><strong>Total</strong></td>
                    </tr>
                </tbody>
                <tfoot class="desktop-view">
                    <tr>
                        <td colspan="3"></td>
                        <td>Total:</td>
                        <td>{{ array_sum($totalAmount) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="col-sm-4">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Product Type</th>
                    <th>Quantity</th>

                </tr>
            </thead>
            <tbody>

                @foreach ($summary as $summ)
                    <tr>
                        <td>{{ $summ['ptype_code'] }} </td>
                        <td>{{ $summ['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</div>
