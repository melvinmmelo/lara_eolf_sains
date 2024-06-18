@extends('layouts.app')

@section('custom_css')
    <style>
        .fixBtn {
            min-width: 100px;
        }

        .input-number {
            text-align: center;
        }

        @media (max-width: 767px) {

            .table-responsive table td,
            .table-responsive table th {
                display: block;
                width: 100%;
            }

            .table-responsive table th {
                display: none;
            }

            .align-middle {
                text-align: left;
                padding: 8px;
            }

            .d-md-table-header {
                display: table-header-group !important;
                border: none !important;
            }

            .d-md-none {
                display: none;
            }


            .desktop-view {
                display: none;
            }
        }

        .buttontypes {
            margin: 5px;
        }

        .product-list {
            max-height: 580px;
            overflow: auto;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: #edf4fc;
        }

        .btn-warning {
            background-color: #f5d760 !important;
        }
    </style>
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Ordering

                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Ordering</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')

        <!-- Default box -->
        <div class="card">
            <form action="{{ route('inbound.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <input type="hidden" name="inboundId" id="inboundId" class="label-input" value="{{ $inboundId }}"
                        required readonly>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="form-label" for="price-quantity">Price Level</label>
                                <input type="text" class="form-control" id="#" name="#"
                                    value="{{ $defaultPriceLevel->pl_name }}" readonly>
                            </div>

                            <div class="col-sm-3">
                                <label class="form-label" for="price-quantity">Delivery Person</label>
                                <input type="text" class="form-control" id="#" name="#"
                                    value="{{ $deliveryPerson->name }}" readonly>
                            </div>

                            <div class="col-sm-3">
                                <label class="form-label" for="price-quantity">Vehicle</label>
                                <input type="text" class="form-control" id="#" name="#"
                                    value="{{ $vehicle->plateno }}" readonly>
                            </div>

                            <div class="col-sm-3">
                                <label class="form-label" for="price-quantity">Customer</label>
                                <input type="text" class="form-control" id="#" name="#"
                                    value="{{ $equipmentSerial }} {{ $customerName }}" readonly>

                                <input type="text" class="form-control" id="bad_order_id" name="bad_order_id" value="" readonly>
                                <input type="text" class="form-control" id="bo_amount" name="bo_amount" value="" readonly>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-1">

                            <div class="product-list" style="min-height: 520px;">
                                <label class="form-label" for="button types">Types</label>
                                <div class="">
                                    @foreach ($productTypes as $type)
                                        <button type="button" class="btn btn-primary w-100 mb-2"
                                            onclick="getProducts('{{ $type->code }}')">{{ $type->code }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-1">
                            <div style="min-height: 520px;">
                                <label class="form-label" for="button types">Quantity</label>
                                <div class="">
                                    <button type="button" class="btn btn-primary w-100 mb-2" onclick="setQty(1)">1</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2" onclick="setQty(2)">2</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2" onclick="setQty(3)">3</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2" onclick="setQty(4)">4</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2" onclick="setQty(5)">5</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2"
                                        onclick="setQty(10)">10</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2"
                                        onclick="setQty(15)">15</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2"
                                        onclick="setQty(20)">20</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2"
                                        onclick="setQty(25)">25</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2"
                                        onclick="setQty(30)">30</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2"
                                        onclick="setQty(35)">35</button>
                                    <button type="button" class="btn btn-primary w-100 mb-2"
                                        onclick="setQty(40)">40</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-1">
                            <label for="qty_toadd">Quantity</label>
                            <div>
                                <input type="text" name="qty_toadd" id="qty_toadd" class="form-control"
                                    value="1">
                            </div>
                            <div class="product-list" id="productsListContainer">
                                <div id="productsList"></div>
                            </div>
                        </div>

                        <div class="col-sm-9">
                            <div>
                                <div id="inboundList">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="table-responsive product-list">
                                                <table class="table table-bordered table-striped">
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
                                                        @php $totalAmount = []; @endphp

                                                        @if (count($inboundList))
                                                            @foreach ($inboundList as $product)
                                                                <tr>
                                                                    <td class="align-middle"><button type="button"
                                                                            class="btn btn-xs btn-danger"
                                                                            onclick="deleteProduct('{{ $inboundId }}', `{{ $product['code'] }}`)"><i
                                                                                class="fas fa-trash"></i></button></td>
                                                                    <td class="align-middle">
                                                                        {{ $product['code'] . ' ' . $product['description'] }}
                                                                    </td>
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
                                                                            <input type="text"
                                                                                id="{{ $product['code'] }}"
                                                                                name="quantity"
                                                                                class="form-control input-number"
                                                                                value="{{ $product['quantity'] }}"
                                                                                min="1" max="99999">
                                                                            <div class="input-group-append">
                                                                                <button type="button"
                                                                                    class="quantity-right-plus btn btn-success btn-number btn-xs"
                                                                                    class="quantity-right-plus btn btn-success btn-number btn-xs"
                                                                                    data-type="plus" data-field=""
                                                                                    onclick="plusQtyProduct('{{ $product['code'] }}', 'add')">
                                                                                    <span class="fas fa-plus"></span>
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <input type="text" name="pcodeprice"
                                                                            id="{{ $product['code'] . '_price' }}"
                                                                            class="label-input"
                                                                            value="{{ $product['price'] }}" readonly>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        @php $totalAmount[] = $product['quantity'] * $product['price'] @endphp
                                                                        <input type="text" name="pcodeamt"
                                                                            id="{{ $product['code'] . '_amt' }}"
                                                                            class="label-input"
                                                                            value="{{ $product['quantity'] * $product['price'] }}"
                                                                            readonly>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="5" class="d-md-none">
                                                                    <strong>Items</strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-middle text-center" colspan="6">No
                                                                    data
                                                                    available.
                                                                </td>
                                                            </tr>

                                                            <!-- Additional rows here -->
                                                            <tr>
                                                                <td colspan="5" class="d-md-none">
                                                                    <strong>Total</strong>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                    <tfoot class="desktop-view">
                                                        <tr>
                                                            <td colspan="4"></td>
                                                            <td>Total:</td>
                                                            <td><input type="text" name="total" id="total"
                                                                    class="label-input"
                                                                    value="{{ array_sum($totalAmount) }}" readonly></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            @if (count($summary))
                                                @include('orderProductSum')
                                            @else
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Product Type</th>
                                                            <th>Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="2" class="text-center">No data available</td>
                                                        </tr>
                                                    </tbody>

                                                </table>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg">
                            <div class="form-checkbox">
                                <input type="checkbox" id="withInvoice" name="with_invoice" value="on">
                                <label for="withInvoice">With Invoice</label>
                            </div>

                            <div class="form-checkbox">
                                <input type="checkbox" id="isBadPricing" name="bad_order" value="on">
                                <label for="isBadPricing">Bad order</label>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <a href="{{ route('inbound.destroy', ['inbound' => $inboundId]) }}"
                        onclick="return discardIn()"><button type="button" class="btn btn-danger">Discard</button></a>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
                <!-- /.card-footer-->
            </form>
        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->
@endsection

@section('custom_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/js/select2.min.js"
        integrity="sha512-9p/L4acAjbjIaaGXmZf0Q2bV42HetlCLbv8EP0z3rLbQED2TAFUlDvAezy7kumYqg5T8jHtDdlm1fgIsr5QzKg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        const total = document.getElementById("total").value ?? 0;

        let totalBadOrder = 0;

        function deleteProduct(inboundId, pcode) {
            if (inboundId == "" || pcode == "") {
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("inboundList").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "/delete-inboundin/" + inboundId + "/" + pcode, true);
                xmlhttp.send();
            }
        }

        function setQty(qty) {
            document.getElementById("qty_toadd").value = qty;
        }



        function discardIn() {
            return confirm('Are you sure you want to discard this order?');
        }

        function fetchLastInsertedBadPricing(customerId, storeId) {

            if (customerId == "" || storeId == "") {
                return;
            }

            if (totalBadOrder > 0) {
                var newTotal = total - totalBadOrder;
                document.getElementById("total").value = newTotal;
                console.log(newTotal + " deducted.");
            } {

                fetch(`/lastBadOrderOfCustomer/${customerId}/${storeId}`)
                    .then(response => response.text())
                    .then(data => {

                        data = JSON.parse(data);
                        console.log(data);

                        if(data == 0){
                            alert("No bad order found.");
                            return;
                        }

                        const badOrderId = data.id;
                        totalBadOrder = data.amount; // ! update this
                        var newTotal = total - totalBadOrder;

                        document.getElementById("bad_order_id").value = badOrderId;
                        document.getElementById("bo_amount").value = totalBadOrder;

                        document.getElementById("total").value = newTotal;
                        console.log(newTotal + " deducted.");
                    });
            }
        }

        document.getElementById("isBadPricing").addEventListener('click', function() {
            if (this.checked) {
                fetchLastInsertedBadPricing("{{ $inbound->customer_id }}", "{{ $inbound->store_id }}");
            } else {
                var total = document.getElementById("total").value;
                var newTotal = parseInt(total) + parseInt(totalBadOrder);
                document.getElementById("total").value = newTotal;
                console.log(newTotal + " added.");
            }
        });


        $(document).ready(function() {

            document.getElementById("productsListContainer").display = "none";

            var quantitiy = 0;
            $('.quantity-right-plus').click(function(e) {

                // Stop acting like a button
                e.preventDefault();

                // If is not undefined

                // Get the field name
                var quantity = parseInt($('#quantity').val());

                // If is not undefined

                $('#quantity').val(quantity + 1);


                // Increment

            });

            $('.quantity-left-minus').click(function(e) {
                // Stop acting like a button
                e.preventDefault();
                // Get the field name
                var quantity = parseInt($('#quantity').val());

                // If is not undefined

                // Increment
                if (quantity > 0) {
                    $('#quantity').val(quantity - 1);
                }
            });

        });

        function getProducts(code) {
            if (code == "") {
                document.getElementById("productsList").innerHTML = "";
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("productsList").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "/productsin/" + code, true);
                xmlhttp.send();
            }
        }

        function addProduct(code) {
            const qty = document.getElementById("qty_toadd").value;
            if (code == "") {
                document.getElementById("inboundList").innerHTML = "";
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    try {
                        var jsonRes = JSON.parse(this.responseText);
                        if (jsonRes.error) {
                            alert("Error adding product to order.");
                            return;
                        }


                    } catch (error) {
                        if (this.readyState == 4 && this.status == 200) {
                            document.getElementById("inboundList").innerHTML = this.responseText;
                            return true;
                        }
                    }

                };
                xmlhttp.open("GET", "/inboundin/" + code + "/" + qty, true);
                xmlhttp.send();
            }
        }

        function minusQtyProduct(code) {
            const qty = document.getElementById(code).value;

            if (qty > 1) {

                updateQty(code, 'min')
                document.getElementById(code).value = parseInt(qty) - 1;
                const newQty = parseInt(qty) - 1;
                document.getElementById(code).value = newQty;
                const pcodePrice = document.getElementById(code + "_price");
                const pcodeAmt = document.getElementById(code + "_amt");
                const total = document.getElementById("total");
                pcodeAmt.value = parseInt(pcodePrice.value) * newQty;
                total.value = parseInt(total.value) - parseInt(pcodePrice.value);
            }

        }

        function plusQtyProduct(code) {
            const qty = document.getElementById(code).value;
            if (qty < 99999) {
                updateQty(code, 'add')
                const newQty = parseInt(qty) + 1;
                document.getElementById(code).value = newQty;
                const pcodePrice = document.getElementById(code + "_price");
                const pcodeAmt = document.getElementById(code + "_amt");
                const total = document.getElementById("total");
                pcodeAmt.value = parseInt(pcodePrice.value) * newQty;
                total.value = parseInt(total.value) + parseInt(pcodePrice.value);
            }

        }

        function updateQty(productCode, action) {
            const inboundId = document.getElementById("inboundId").value;
            // console.log({inboundId, productCode, action});
            if (inboundId == "") {
                // document.getElementById("inboundProdInput").innerHTML = "";
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log("ok");

                        document.getElementById("orderProductSum").innerHTML = this.responseText;
                        return true;
                    } else {
                        console.log("tset");
                        try {
                            var jsonRes = JSON.parse(this.responseText);
                            if (jsonRes.error) {
                                alert("Error updating product quantity.");

                                if (action == 'add')
                                    document.getElementById(productCode).value = parseInt(document.getElementById(
                                        productCode).value) - 1;
                                else if (action == 'min')
                                    document.getElementById(productCode).value = parseInt(document.getElementById(
                                        productCode).value) + 1;
                            }
                        } catch (error) {}
                    }
                };
                xmlhttp.open("GET", "/inbound-updateProdQty/" + inboundId + "/" + productCode + "/" + action, true);
                xmlhttp.send();
            }
        }
    </script>
@endsection
