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
                                    <select class="form-control" name="delivery_person_id" id="delivery_person_id" required>
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

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="form-label" for="order_date"><i style="color:red">*</i>Date</label>
                                    <input type="date" class="form-control" name="order_date" id="order_date"
                                        value="{{ $nextDay }}" required>
                                </div>
                            </div>
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
                                                    </tbody>

                                                </table>
                                            </div>

                                            <div class="w-100 d-flex justify-content-end">

                                                <h6 class="font-weight-bold mr-2">Total:</h6>
                                                <div>
                                                    <input type="text" name="total" id="total"
                                                        class="label-input" value="0" readonly>
                                                </div>
                                            </div>

                                            <div id="BOContainer" class="w-100 d-flex justify-content-end">
                                                <h6 class="font-weight-bold mr-2">BO Amount:</h6>
                                                <div>
                                                    <input type="text" name="bo_amount" id="bo_amount"
                                                        class="label-input" value="0" readonly>
                                                </div>
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
                                                    <tr>
                                                        <td colspan="2" class="text-center">No data available</td>
                                                    </tr>
                                                </tbody>

                                            </table>

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

                            <div id="salesInvoiceContainer" style="display: none; margin-left: 25px; margin-top: 10px; margin-bottom: 10px;">
                                <label class="form-label" for="sales_invoice_no">Sales Invoice No.</label>
                                <input type="text" class="form-control" id="sales_invoice_no"
                                       name="sales_invoice_no" placeholder="Enter sales invoice number" maxlength="50" style="max-width: 300px;">
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
                    <button type="submit" id="submitBtn" class="btn btn-default" value="save">Save</button>
                    <input type="hidden" name="form_submit_token" value="{{ Str::random(32) }}">
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // page on load show modalReminder
        $(document).ready(function() {
            // $('#modalReminder').modal('show');
        });

        document.getElementById("BOContainer").style.display = "none";

        const total = document.getElementById("total").value ?? 0;

        let totalBadOrder = 0;

        // Tracks the last-applied price level so the reprice-on-change handler can
        // revert on cancel/error, and so programmatic sets (customer/driver
        // defaults, branch default) aren't mistaken for a user change. Always set
        // the dropdown through applyPriceLevel() to keep this in sync.
        let prevPriceLevel = '';
        function applyPriceLevel(id) {
            if (!id) return;
            document.getElementById('pricelevel_id').value = id;
            prevPriceLevel = String(id);
        }

        function setCustomerName(str) {
            $.ajax({
                type: "GET",
                url: "/get-equipmentcustomerstore/" + str,
                success: function(response) {
                    // console.log(response);
                    document.getElementById('customer_id').value = response.customer_id;
                    document.getElementById('customer').value = response.customer_name;
                    // Auto-fill the price level from the customer's assigned level
                    // (or the branch default). Null means "no default" — leave the
                    // current selection untouched.
                    applyPriceLevel(response.pricelevel_id);
                }
            });
        }

        @if (session('branch_code') == 'EFTO-TAR')

            document.getElementById('delivery_person_id').addEventListener('change', function() {
                var driver = document.getElementById('delivery_person_id').value;
                $.ajax({
                    type: "GET",
                    url: "/dp-details/" + driver,
                    success: function(response) {
                        applyPriceLevel(response.default_price_level);
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
                xmlhttp.open("GET", "/delete-inboundin/" + pcode, true);
                xmlhttp.send();
            }
        }

        function setQty(qty) {
            document.getElementById("qty_toadd").value = qty;
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
                // console.log(newTotal + " added.");

                document.getElementById("bad_order_id").value = "";
                document.getElementById("bo_amount").value = 0;
            }
        });

        // Show/hide sales invoice number field based on "With Invoice" checkbox
        document.getElementById("withInvoice").addEventListener('click', function() {
            if (this.checked) {
                document.getElementById("salesInvoiceContainer").style.display = "block";
            } else {
                document.getElementById("salesInvoiceContainer").style.display = "none";
                // Clear the field when unchecked
                document.getElementById("sales_invoice_no").value = "";
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

            // Pre-select the branch's configured default customer price level on a
            // fresh order. Falls back to the legacy EFTO-CAG default (4) only when
            // no branch default has been flagged on the Price Levels page.
            try {
                @if (!empty($defaultPriceLevelId))
                    applyPriceLevel({{ $defaultPriceLevelId }});
                @else
                    if ($('#branch_code').val() == 'EFTO-CAG') {
                        applyPriceLevel(4);
                    }
                @endif
            } catch (error) {
                console.log("Error setting default price level", error);
            }

            // Re-price staged cart items when the price level changes.
            // New-order line items are staged under inbound_id = 0, so we reprice
            // against that sentinel (same backend as the edit screen). prevPriceLevel
            // is kept in sync via applyPriceLevel() (branch/customer/driver defaults),
            // so the page-load set can't spuriously fire this. An empty cart just
            // updates silently — the next added product already picks up the current
            // level via addProduct().
            $('#pricelevel_id').on('change', function() {
                const newLevel = $(this).val();
                if (!newLevel || newLevel === prevPriceLevel) return;

                const hasItems = document.querySelectorAll('#inboundList input[name="pcodeprice"]').length > 0;
                if (!hasItems) {
                    prevPriceLevel = newLevel;
                    return;
                }

                if (!confirm("Changing the price level will re-price all items in the cart. Continue?")) {
                    $(this).val(prevPriceLevel);
                    return;
                }

                fetch(`/inbound-reprice/0/${newLevel}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            $('#pricelevel_id').val(prevPriceLevel);
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
                        $('#pricelevel_id').val(prevPriceLevel);
                    });
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
                axios.get(`/inboundin/${code}/${qty}/${priceLevelId}`)
                    .then(response => {

                        if (response.data.error) {
                            throw new Error(response.data.error)
                        }

                        document.getElementById("inboundList").innerHTML = response.data;
                        return true;


                    })
                    .catch(error => {
                        alert(error);
                    });
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
            axios.get(`/inbound-updateProdQty/${productCode}/${action}`)
                .then(response => {

                    if (response.data.error) {

                        alert(response.data.error);

                        if (action == 'add') {
                            document.getElementById(productCode).value = parseInt(document.getElementById(
                                productCode).value) - 1;
                        } else if (action == 'min') {
                            document.getElementById(productCode).value = parseInt(document.getElementById(
                                productCode).value) + 1;
                        }

                        return false;

                    }


                    document.getElementById("orderProductSum").innerHTML = response.data;
                    return true;
                })
                .catch(error => {
                    alert(error)
                });
        }



        // Price-level change re-pricing is handled inside $(document).ready above
        // (registered after the EFTO-CAG default so it starts in sync).
    </script>

    <script>
        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
        // Handle form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            var submitBtn = document.getElementById('submitBtn');
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }
            submitBtn.disabled = true;
        });
        
        // Enable form resubmission if there are validation errors
        @if($errors->any())
            document.getElementById('submitBtn').disabled = false;
        @endif
    </script>
@endsection
