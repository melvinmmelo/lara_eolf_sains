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
                                                            @if ($dprProd->hold)
                                                                <input type="text" class="form-control w-25" name="newHold"
                                                                    id="newHold" value="{{ $dprProd->hold }}" required>
                                                            @endif
                                                        </td>
                                                        <td>{{ $dprProd->price }}</td>
                                                        <td>{{ formatNumber($dprProd->quantity * $dprProd->price) }}</td>
                                                        <td>

                                                            {{-- @if ($deliveryPurchaseReceipt->status == 'Encoding') --}}
                                                            <a href="{{ route('drp.products-update', ['dprId' => $deliveryPurchaseReceipt->id, 'code' => $dprProd->code, 'action' => 'delete']) }}"
                                                                onclick="return confirmDeleteProduct()"
                                                                class="btn btn-sm btn-danger"><i
                                                                    class="fas fa-trash"></i></a>
                                                            {{-- @endif --}}

                                                            <a href="#" onclick="editProduct('{{ $dprProd->code }}', '{{ $dprProd->quantity }}', '{{ $dprProd->hold }}')"
                                                                class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-modal"><i class="fas fa-edit"></i></a>

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
                    <h4 class="modal-title">Edit product</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <form action="{{ route('drp.products-update') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="dprId" id="dpr_id" value="{{ $deliveryPurchaseReceipt->id }}" required readonly>

                        <div class="form-group">
                            <label class="form-label" for="product_code"><i style="color:red">*</i>Product code</label>
                            <input type="text" class="form-control" name="code" id="product_code" value="" required readonly>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="new_quantity"><i style="color:red">*</i>Quantity</label>
                            <input type="number" class="form-control" name="new_quantity" id="new_quantity" value=""
                                required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="hold_qty"><i style="color:red">*</i>Hold</label>
                            <input type="number" class="form-control" name="hold_qty" id="hold_qty" value=""
                                required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="action" value="edit" class="btn btn-success">Save changes</button>
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

        function confirmDeleteProduct() {
            return confirm('Are you sure you want to delete this product?');
        }

        function editProduct(product_code, quantity , hold_qty) {
            $('#product_code').val(product_code);
            $('#new_quantity').val(quantity);
            $('#hold_qty').val(hold_qty);
        }
    </script>
@endsection
