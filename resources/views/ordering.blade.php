@extends('layouts.app')

@section('custom_css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.9/dist/css/select2.min.css" rel="stylesheet">



    <style>
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
            max-height: 250px;
            overflow: auto;
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



                <div class="card-header">
                    <h3 class="card-title mr-2">Ordering Info</h3>

                    <input type="text" name="inboundId" id="inboundId" class="label-input" value="{{ $inboundId }}"
                        required readonly>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="form-label" for="price-quantity">Price Level</label>
                                <input type="text" class="form-control" id="#" name="#"
                                    value="{{ $defaultPriceLevel->pl_name }}">
                            </div>

                            <div class="col-sm-3">
                                <label class="form-label" for="price-quantity">Delivery Person</label>
                                <input type="text" class="form-control" id="#" name="#"
                                    value="{{ $deliveryPerson->name }}">
                            </div>

                            <div class="col-sm-3">
                                <label class="form-label" for="price-quantity">Vehicle</label>
                                <input type="text" class="form-control" id="#" name="#"
                                    value="{{ $vehicle->plateno }}">
                            </div>

                            <div class="col-sm-3">
                                <label class="form-label" for="price-quantity">Equipment</label>
                                <input type="text" class="form-control" id="#" name="#"
                                    value="{{ $equipment }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-2">

                            <div class="product-list">
                                <label class="form-label" for="button types">Types</label>
                                <div class="">
                                    @foreach ($productTypes as $type)
                                        <button type="button" class="btn btn-primary w-100 mb-2"
                                            onclick="getProducts('{{ $type->code }}')">{{ $type->code }}</button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-2">
                            <div class="product-list" id="productsListContainer">
                                <div id="productsList"></div>
                            </div>
                        </div>

                        <div class="col-sm-8">
                            <div>
                                <div id="inboundList">
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
                                                        <tr>
                                                            <td class="align-middle text-center" colspan="5">No data
                                                                available.
                                                            </td>
                                                        </tr>

                                                        <!-- Additional rows here -->
                                                        <tr>
                                                            <td colspan="5" class="d-md-none"><strong>Total</strong></td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot class="desktop-view">
                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td>Total:</td>
                                                            <td></td>
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

                            <div class="form-checkbox">
                                <input type="checkbox" id="badOrder" name="bad_order" value="on">
                                <label for="badOrder">Bad order</label>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.9/js/select2.min.js" integrity="sha512-9p/L4acAjbjIaaGXmZf0Q2bV42HetlCLbv8EP0z3rLbQED2TAFUlDvAezy7kumYqg5T8jHtDdlm1fgIsr5QzKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



    <script>

        $("#equipment").select2({
            placeholder: "Select a state",
            allowClear: true
        });


        function discardIn() {
            return confirm('Are you sure you want to discard this inbound?');
        }
        $(document).ready(function() {
            document.getElementById("productsListContainer").display = "none";
        });

        $(document).ready(function() {

            var quantitiy = 0;
            $('.quantity-right-plus').click(function(e) {

                // Stop acting like a button
                e.preventDefault();
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
            if (code == "") {
                document.getElementById("inboundList").innerHTML = "";
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("inboundList").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "/inboundin/" + code, true);
                xmlhttp.send();
            }
        }

        function minusQtyProduct(code) {
            const qty = document.getElementById(code).value;

            if (qty > 1) {
                document.getElementById(code).value = parseInt(qty) - 1;

                const newQty = parseInt(qty) - 1;

                document.getElementById(code).value = newQty;

                const pcodePrice = document.getElementById(code + "_price");
                const pcodeAmt = document.getElementById(code + "_amt");
                const total = document.getElementById("total");

                pcodeAmt.value = parseInt(pcodePrice.value) * newQty;

                total.value = parseInt(total.value) - parseInt(pcodePrice.value);

                updateQty(code, 'min');
            }

        }

        function plusQtyProduct(code) {
            const qty = document.getElementById(code).value;
            if (qty < 99999) {
                const newQty = parseInt(qty) + 1;

                document.getElementById(code).value = newQty;

                const pcodePrice = document.getElementById(code + "_price");
                const pcodeAmt = document.getElementById(code + "_amt");
                const total = document.getElementById("total");

                pcodeAmt.value = parseInt(pcodePrice.value) * newQty;

                total.value = parseInt(total.value) + parseInt(pcodePrice.value);

                updateQty(code, 'add');
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
                        // document.getElementById("inboundProdInput").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "/inbound-updateProdQty/" + inboundId + "/" + productCode + "/" + action, true);
                xmlhttp.send();
            }
        }
    </script>
@endsection
