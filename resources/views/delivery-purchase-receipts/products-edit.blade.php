@extends('layouts.app')

@section('custom_css')
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
                    <h1>Inbound Inventory - Edit Products</h1>
                    <small class="text-red">
                        <ul>
                            <li>This will update the item master data realtime!</li>
                            <li>To add quantity, select the item and enter the quantity. Then, click the "Add" button.</li>
                        </ul>
                    </small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">DPR add items</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">

            <div class="card-body">

                @include('layouts.errors')

                <div>
                    <div id="inboundList">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">DR. No.</label>
                                    <input type="text" class="form-control" id="#"
                                        value="{{ $deliveryPurchaseReceipt->dr_no }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Issued date</label>
                                    <input type="text" class="form-control" id="#"
                                        value="{{ $deliveryPurchaseReceipt->issue_date }}" readonly>
                                </div>
                            </div>


                        </div>

                        {{-- @if ($deliveryPurchaseReceipt->status == 'Encoding') --}}
                        <form action="{{ route('drp.products-update') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Items</label>
                                        <select class="form-control select2bs4" name="code" style="width: 100%;"
                                            required>

                                            <option value="">--Select--</option>

                                            @foreach ($originalProducts as $product)
                                                <option value="{{ $product->code }}">
                                                    {{ $product->code . ' ' . $product->productName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">Quantity</label>
                                        <input type="hidden" class="form-control" name="dprId" id="dprId"
                                            value="{{ $deliveryPurchaseReceipt->id }}" required readonly>
                                        <input type="text" class="form-control" name="quantity" id="quantity" required>

                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div><button name="action" type="submit" class="btn btn-primary" value="add"
                                                style="width: 100%">
                                                <i class="fas fa-plus"></i>Add
                                            </button></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        {{-- @endif --}}
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Unit</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($deliveryPurchaseReceipt->products)
                                                @php
                                                    $sum = 0;
                                                    // convert the json string to array
                                                    $dprProducts = json_decode($deliveryPurchaseReceipt->products);

                                                    if ($dprProducts) {
                                                        usort($dprProducts, function ($a, $b) {
                                                            return $a->order <=> $b->order;
                                                        });
                                                    }

                                                @endphp


                                                @foreach ($dprProducts as $dprProd)
                                                    @php
                                                        //  get sum
                                                        $sum += $dprProd->quantity * $dprProd->price;

                                                    @endphp
                                                    <tr>
                                                        <td>{{ $dprProd->code . ' ' . $dprProd->description }}</td>
                                                        <td>{{ $dprProd->unit }}</td>
                                                        <td>{{ $dprProd->quantity }}
                                                            @if ($dprProd->hold > 0)
                                                                <a href="#"
                                                                    class="btn btn-danger btn-sm">{{ $dprProd->hold }}</a>
                                                            @endif
                                                        </td>
                                                        <td>{{ $dprProd->price }}</td>
                                                        <td>{{ formatNumber($dprProd->quantity * $dprProd->price) }}</td>
                                                        <td>

                                                            {{-- @if ($deliveryPurchaseReceipt->status == 'Encoding') --}}
                                                            <a href="#"
                                                                onclick="confirmDeleteProduct('{{ $dprProd->code }}', '{{ $dprProd->quantity }}', '{{ $dprProd->hold }}')"
                                                                class="btn btn-sm btn-danger" data-toggle="modal"
                                                                data-target="#edit-modal"><i class="fas fa-trash"></i></a>
                                                            {{-- @endif --}}

                                                            <a href="#"
                                                                onclick="editProduct('{{ $dprProd->code }}', '{{ $dprProd->quantity }}', '{{ $dprProd->hold }}')"
                                                                class="btn btn-sm btn-warning" data-toggle="modal"
                                                                data-target="#edit-modal"><i class="fas fa-edit"></i></a>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="d-md-none"><strong>Items</strong></td>
                                                </tr>
                                                <tr>
                                                    <td class="align-middle text-center" colspan="5">No data available.
                                                    </td>
                                                </tr>

                                                <!-- Additional rows here -->
                                                <tr>
                                                    <td colspan="5" class="d-md-none"><strong>Total</strong></td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot class="desktop-view">
                                            <tr>
                                                <td colspan="2"></td>
                                                <td></td>
                                                <td>Total:</td>
                                                <td>{{ isset($sum) ? formatNumber($sum) : 0 }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
    </section>


    <div class="modal fade" id="edit-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Edit product</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <form action="{{ route('drp.products-update') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="dprId" id="dpr_id"
                            value="{{ $deliveryPurchaseReceipt->id }}" required readonly>

                        <div class="form-group">
                            <label class="form-label" for="product_code"><i style="color:red">*</i>Product code</label>
                            <input type="text" class="form-control" name="code" id="product_code" value=""
                                required readonly>
                        </div>

                        <div id="hideIfActionIsDelete">


                            {{-- <div class="form-group">
                                <label class="form-label" for="qty_to_add"><i style="color:red">*</i>Update type</label>
                                <select name="update_type" id="update_type" class="form-control" required>
                                    <option value="plus">Add</option>
                                    <option value="minus">Remove</option>
                                </select>
                            </div> --}}

                            <div class="form-group">
                                <label class="form-label" for="new_quantity"><i style="color:red">*</i>Quantity</label>
                                <input type="number" class="form-control" name="new_quantity" id="new_quantity"
                                    value="0" required>
                            </div>

                            {{-- <div class="form-group">
                                <label class="form-label" for="qty_to_add"><i style="color:red">*</i>Hold Update
                                    type</label>
                                <select name="hold_update_type" id="hold_update_type" class="form-control">
                                    <option value="">--Select--</option>
                                    <option value="plus">Add</option>
                                    <option value="minus">Remove</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="hold_new_quantity"><i style="color:red">*</i>Quantity
                                </label>
                                <p id="currentHoldQuantity"> </p>

                                <input type="number" class="form-control" name="hold_new_quantity"
                                    id="hold_new_quantity" value="0" required>
                            </div> --}}
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="action" id="action" value="edit"
                            class="btn btn-success">Save
                            changes</button>
                    </div>

                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
    <!-- /.content -->


@endsection

@section('custom_js')
    <script>
        function saveDPR() {
            return confirm('Are you sure you want to save this DR?');
        }

        function confirmDeleteProduct(product_code, quantity, hold_qty, action = "delete") {

            $('#hideIfActionIsDelete').hide();

            $('#modal-title').text('Delete product');
            $('#product_code').val(product_code);

            // $('#new_quantity').val(quantity);
            // $('#hold_qty').val(hold_qty);
            $('#action').val(action);

            // document.getElementById('new_quantity').readOnly = true;
            // document.getElementById('hold_qty').readOnly = true;
            document.getElementById('action').innerHTML = "Confirm";

        }


        function editProduct(product_code, quantity, hold_qty, action = "edit") {

            $('#hideIfActionIsDelete').show();

            $('#modal-title').text('Edit product');
            $('#product_code').val(product_code);
            $('#new_quantity').val(quantity);
            // $('#hold_qty').val(hold_qty);
            $('#action').val(action);

            // document.getElementById('new_quantity').readOnly = false;
            // document.getElementById('hold_qty').readOnly = false;
            document.getElementById('action').innerHTML = "Save changes";
        }
    </script>
@endsection
