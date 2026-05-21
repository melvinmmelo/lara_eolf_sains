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
                    <h1>Edit Order {{ $inbound->code }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Edit order</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        @include('layouts.errors')
        <div class="card">
            <form action="{{ route('order.updateInbound') }}" method="POST" onsubmit="return validateOrderItems();">
                @csrf
                @method('PUT')
                <input type="hidden" name="inbound_id" id="inboundId" class="label-input" value="{{ $inbound->id }}"
                    required readonly>


                <input type="hidden" class="form-control" id="bad_order_id" name="bad_order_id" value="" readonly>


                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="form-label" for="branch_code">Branch Code</label>
                                    <input type="text" class="form-control" name="branch_code" id="branch_code"
                                        value="{{ session('branch_code') }}" required readonly>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="form-label" for="equipment">Equipment</label>
                                    <select class="form-control equipment w-100 select2bs4" name="equipment_id"
                                        id="equipment" onchange="setCustomerName(this.value)" required>
                                        <option value="">--Select--</option>
                                        @foreach ($equipment as $equip)
                                            <option value="{{ $equip->equipmentStore->id }}">
                                                {{ $equip->code . ' ' . $equip->equipmentStore->customer->lastname . ' ' . $equip->equipmentStore->customer->firstname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">

                                <div class="form-group">
                                    <label class="form-label" for="customer"><i style="color:red">*</i>Customer</label>
                                    <input type="hidden" class="form-control" name="customer_id" id="customer_id" required
                                        readonly>
                                    <input type="text" class="form-control" name="customer" id="customer" required
                                        readonly />
                                </div>
                            </div>

                            <div class="col-sm-3">

                                <div class="form-group">
                                    <label class="form-label" for="deliveryPerson"><i style="color:red">*</i>Delivery
                                        Person</label>
                                    <select class="form-control" name="delivery_person_id" id="deliveryPerson" required>
                                        <option value="">--Select--</option>
                                        @foreach ($deliveryPersons as $dperson)
                                            <option value="{{ $dperson->id }}">{{ $dperson->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">

                                <div class="form-group">
                                    <label class="form-label" for="driver_id"><i style="color:red">*</i>Driver</label>
                                    <select class="form-control" name="driver_id" id="driver_id" required>
                                        <option value="">--Select--</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="form-label" for="pricelevel_id"><i style="color:red">*</i>Price
                                        Level</label>
                                    <select class="form-control" name="pricelevel_id" id="pricelevel_id" required>
                                        <option value="">--Select--</option>
                                        @foreach ($pricing as $plevel)
                                            <option value="{{ $plevel->id }}">{{ $plevel->pl_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">

                                <div class="form-group">
                                    <label class="form-label" for="vehicle"><i style="color:red">*</i>Vehicle</label>
                                    <select class="form-control" name="vehicle_id" id="vehicle" required>
                                        <option value="">--Select--</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->plateno }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @role('admin')
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="form-label" for="order_date"><i style="color:red">*</i>Order Date</label>
                                        <input type="date" class="form-control" name="order_date" id="order_date"
                                            value="{{ optional($inbound->order_date)->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                            @endrole
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-1">
                            <div class="product-list" style="min-height: 520px;">
                                <label class="form-label" for="button types">Types</label>
                                <div class="">
                                    @foreach ($productTypes as $type)
                                        <button type="button" class="btn btn-default w-100 mb-2"
                                            onclick="getProducts('{{ $type->code }}')">{{ $type->code }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-1">
                            <div style="min-height: 520px;">
                                <label class="form-label" for="button types">Quantity</label>
                                <div class="">
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(1)">1</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(2)">2</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(3)">3</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(4)">4</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(5)">5</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(10)">10</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(15)">15</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(20)">20</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(25)">25</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(30)">30</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
                                        onclick="setQty(35)">35</button>
                                    <button type="button" class="btn btn-default w-100 mb-2"
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
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="deleteAllProducts()">
                                        <i class="fas fa-trash"></i> Delete All
                                    </button>
                                </div>
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
                                                                            onclick="deleteProduct(`{{ $product['code'] }}`)"><i
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
                                                                                min="1" max="99999" readonly>
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
                                                                            value="{{ formatNumber($product['quantity'] * $product['price']) }}"
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
                                                            <td><h6 class="font-weight-bold mr-2">Total:</h6></td>
                                                            <td><input type="text" name="total" id="total"
                                                                    class="label-input"
                                                                    value="{{ array_sum($totalAmount) }}" readonly></td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="4"></td>
                                                            <td><h6 class="font-weight-bold mr-2">BO Amount:</h6></td>
                                                            <td>
                                                                <div id="BOContainer"
                                                                    class="w-100 d-flex justify-content-end">
                                                                    <div>
                                                                        <input type="text" name="bo_amount"
                                                                            id="bo_amount" class="label-input"
                                                                            value="{{ $inbound->bo_amount }}" readonly>
                                                                    </div>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>


                                            </div>


                                        </div>

                                        <div class="col-sm-4">
                                            <div id="orderProductSum">
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

                            <div class="form-checkbox">
                                <input type="checkbox" id="isFOC" name="foc" value="on">
                                <label for="isFOC">FOC</label>
                            </div>

                            <div class="form-checkbox">
                                <input type="checkbox" id="withSF" name="with_sf" value="on">
                                <label for="withSF">With Delivery Charge</label>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-success" value="saveAndSubmit">Save and complete</button>
                </div>
                <!-- /.card-footer-->
            </form>
        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->

    @include('modal_order_reminder')

@endsection

@section('custom_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/js/select2.min.js"
        integrity="sha512-9p/L4acAjbjIaaGXmZf0Q2bV42HetlCLbv8EP0z3rLbQED2TAFUlDvAezy7kumYqg5T8jHtDdlm1fgIsr5QzKg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>

        // Pre-populate the form fields after select2 has initialized.
        // The layout's $(document).ready initializes .select2bs4, so we run
        // *after* it (callbacks fire in registration order) and use
        // .val(...).trigger('change') so the select2 widget actually updates.
        // Setting .value directly on the underlying <select> can leave the
        // visible select2 widget out of sync (this is what caused the second
        // edit to look "broken" after navigating back from a previous edit).
        $(document).ready(function() {
            // $('#modalReminder').modal('show');

            // Hidden / plain text inputs — set directly first so they're
            // available before any change handlers fire.
            document.getElementById('customer_id').value = "{{ $inbound->customer_id ?? '' }}";
            document.getElementById('customer').value = "{{ $inbound->customer->fullName ?? '' }}";

            // Select2-wrapped <select>s need val().trigger('change') so the
            // visible widget reflects the underlying value.
            $('#pricelevel_id').val("{{ $inbound->pricelevel_id ?? '' }}").trigger('change');
            $('#deliveryPerson').val("{{ $inbound->delivery_person_id ?? '' }}").trigger('change');
            $('#driver_id').val("{{ $inbound->driver_id ?? '' }}").trigger('change');
            $('#vehicle').val("{{ $inbound->vehicle_id ?? '' }}").trigger('change');

            // Equipment fires setCustomerName() onchange which would overwrite
            // the customer fields we just set. Use a no-AJAX path: set the
            // underlying value, then ask select2 to refresh its display.
            $('#equipment').val("{{ $equipmentStore->id ?? '' }}").trigger('change.select2');

            // Re-price-on-change handler. Registered AFTER the initial
            // .trigger('change') above so the page-load value-set doesn't
            // accidentally fire it. Reverts via 'change.select2' (namespaced)
            // which only refreshes the widget — it does NOT re-fire the
            // generic 'change' handler, so the revert can't loop.
            let prevPriceLevel = "{{ $inbound->pricelevel_id ?? '' }}";
            $('#pricelevel_id').on('change', function() {
                const newLevel = $(this).val();
                if (!newLevel || newLevel === prevPriceLevel) return;

                if (!confirm("Changing the price level will re-price all items in the cart. Continue?")) {
                    $(this).val(prevPriceLevel).trigger('change.select2');
                    return;
                }

                fetch(`/inbound-reprice/{{ $inbound->id }}/${newLevel}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            $('#pricelevel_id').val(prevPriceLevel).trigger('change.select2');
                            return;
                        }

                        document.getElementById('inboundList').innerHTML = data.html;

                        const warnings = [];
                        if (data.noPrice && data.noPrice.length) {
                            warnings.push("No price at this level for:\n  - " + data.noPrice.join('\n  - '));
                        }
                        if (data.noStock && data.noStock.length) {
                            warnings.push("Insufficient stocks for:\n  - " + data.noStock.join('\n  - '));
                        }
                        if (document.getElementById('isBadPricing').checked) {
                            warnings.push("Bad order is checked. Please uncheck and recheck it to recompute the BO amount.");
                        }
                        if (warnings.length) alert(warnings.join('\n\n'));

                        prevPriceLevel = newLevel;
                    })
                    .catch(err => {
                        alert("Error re-pricing items.");
                        $('#pricelevel_id').val(prevPriceLevel).trigger('change.select2');
                    });
            });
        });

        @if($inbound->is_with_sf)
            document.getElementById('withSF').checked = true;
        @endif

        // checked is_foc
        @if($inbound->is_foc)
            document.getElementById("isFOC").checked = true;
        @endif

        // check if bad order is checked
        @if($inbound->bo_amount > 0)
            document.getElementById("isBadPricing").checked = true;
        @endif

         @if($inbound->with_invoice)
            document.getElementById("withInvoice").checked = true;
         @endif

        document.getElementById("BOContainer").style.display = "none";

        const total = document.getElementById("total").value ?? 0;

        let totalBadOrder = 0;

        function setCustomerName(str) {
            $.ajax({
                type: "GET",
                url: "/get-equipmentcustomerstore/" + str,
                success: function(response) {
                    // console.log(response);
                    document.getElementById('customer_id').value = response.customer_id;
                    document.getElementById('customer').value = response.customer_name;
                }
            });
        }

        @if(session('branch_code') == 'EFTO-TAR')

            document.getElementById('deliveryPerson').addEventListener('change', function() {
                var driver = document.getElementById('deliveryPerson').value;
                $.ajax({
                    type: "GET",
                    url: "/dp-details/" + driver,
                    success: function(response) {
                        document.getElementById('pricelevel_id').value = response.default_price_level;
                    }
                });
            });

        @endif


        function setObId(str) {
            document.getElementById('ob_id').value = str;
        }

        function deleteProduct(pcode) {
            if (pcode == "") {
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("inboundList").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "/delete-inboundin/" + pcode + "/" + {{ $inbound->id }}, true);
                xmlhttp.send();
            }
        }

        function deleteAllProducts() {
            if (!confirm("Are you sure you want to delete ALL items from this order?")) {
                return;
            }

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("inboundList").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "/delete-all-inboundin/" + {{ $inbound->id }}, true);
            xmlhttp.send();
        }

        function setQty(qty) {
            document.getElementById("qty_toadd").value = qty;
        }

        function validateOrderItems() {
            const itemCount = document.querySelectorAll('#inboundList input[name="quantity"]').length;
            if (itemCount === 0) {
                alert("Cannot save an order with no items. Please add at least one product.");
                return false;
            }
            return true;
        }



        function discardIn() {
            return confirm('Are you sure you want to discard this order?');
        }

        function fetchLastInsertedBadPricing() {
            var total = document.getElementById("total").value;
            var customerId = document.getElementById("customer_id").value;
            var storeId = document.getElementById("equipment").value;

            if (customerId == "" || storeId == "") {

                alert("Please select a customer.");

                document.getElementById("isBadPricing").checked = false;

                return;
            }

            if (totalBadOrder > 0) {
                var newTotal = total - totalBadOrder;
                total.value = newTotal;
                // console.log(newTotal + " deducted1.");
            } {

                fetch(`/lastBadOrderOfCustomer/${customerId}/${storeId}`)
                    .then(response => response.text())
                    .then(data => {

                        data = JSON.parse(data);
                        // console.log(data);

                        if (data == 0) {
                            alert("No bad order found.");
                            return;
                        }

                        const badOrderId = data.id;
                        totalBadOrder = data.amount; // ! update this

                        if (totalBadOrder > total) {
                            alert("Bad order amount is greater than total.");
                            document.getElementById("isBadPricing").checked = false;

                            return;
                        }

                        var newTotal = parseInt(total) - parseInt(totalBadOrder);

                        document.getElementById("bad_order_id").value = badOrderId;
                        document.getElementById("bo_amount").value = totalBadOrder;

                        document.getElementById("total").value = newTotal;
                        // console.log(newTotal + " deducted2.");
                    });
            }
        }

        document.getElementById("isBadPricing").addEventListener('click', function() {
            if (this.checked) {
                fetchLastInsertedBadPricing("{{ $inbound->customer_id ?? '' }}",
                    "{{ $inbound->store_id ?? '' }}");
                document.getElementById("BOContainer").style.display = "block";

            } else {

                document.getElementById("BOContainer").style.display = "none";

                var total = document.getElementById("total").value;
                var newTotal = parseInt(total) + parseInt(totalBadOrder);
                document.getElementById("total").value = newTotal;
                document.getElementById("bo_amount").value = 0;
            }
        });


        $(document).ready(function() {

            document.getElementById("productsListContainer").display = "none";

            var quantitiy = 0;
            $('.quantity-right-plus').click(function(e) {
                e.preventDefault();
                var quantity = parseInt($('#quantity').val());
                $('#quantity').val(quantity + 1);
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
            const priceLevelId = document.getElementById("pricelevel_id").value;

            if (priceLevelId == "" || priceLevelId == null || priceLevelId == undefined) {
                alert("Please select a price level.");
                return;
            }

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
                xmlhttp.open("GET", "/inboundin/" + code + "/" + qty + "/" + priceLevelId + "/" + {{ $inbound->id }}, true);
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
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("orderProductSum").innerHTML = this.responseText;
                    return true;
                } else {
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
            xmlhttp.open("GET", "/inbound-updateProdQty/" + productCode + "/" + action + "/" + {{ $inbound->id }}, true);
            xmlhttp.send();
        }
    </script>
@endsection
