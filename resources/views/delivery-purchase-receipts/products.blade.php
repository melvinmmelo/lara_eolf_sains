@extends('layouts.app')
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

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Inbound Inventory - Encode Products</h1>
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

                        @if ($deliveryPurchaseReceipt->status == 'Encoding')
                            <form action="{{ route('dpr-product.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="form-label">Items</label>
                                            <select class="form-control select2bs4" name="product_code" style="width: 100%;"
                                                required>

                                                <option value="">--Select--</option>

                                                @foreach ($originalProducts as $product)
                                                    <option value="{{ $product->code }}">{{ $product->code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="form-label">Quantity</label>
                                            <input type="hidden" class="form-control" name="dpr_id" id="dpr_id"
                                                value="{{ $deliveryPurchaseReceipt->id }}" required readonly>
                                            <input type="text" class="form-control" name="qty" id="qty"
                                                required>

                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <div><button type="submit" class="btn btn-primary" style="width: 100%">
                                                    Add
                                                </button></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
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

                                                    usort($dprProducts, function ($a, $b) {
                                                        return $a->code <=> $b->code;
                                                    });

                                                @endphp


                                                @foreach ($dprProducts as $dprProd)
                                                    @php
                                                        //  get sum
                                                        $sum += $dprProd->quantity * $dprProd->price;

                                                    @endphp
                                                    <tr>
                                                        <td>{{ $dprProd->code . ' ' . $dprProd->description }}</td>
                                                        <td>{{ $dprProd->unit }}</td>
                                                        <td>{{ $dprProd->quantity }}</td>
                                                        <td>{{ $dprProd->price }}</td>
                                                        <td>{{ $dprProd->quantity * $dprProd->price }}</td>
                                                        <td>

                                                            @if ($deliveryPurchaseReceipt->status == 'Encoding')
                                                                <a href="{{ route('dpr.delete', ['drid' => $deliveryPurchaseReceipt->id, 'pcode' => $dprProd->code]) }}"
                                                                    onclick="return confirmDeleteProduct()"
                                                                    class="btn btn-sm btn-danger">Delete</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
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
                                            @endif
                                        </tbody>
                                        <tfoot class="desktop-view">
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>Total:</td>
                                                <td>{{ $sum ?? 0 }}</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        @if ($deliveryPurchaseReceipt->status == 'Encoding')
                            <div class="card-footer">
                                <a href="{{ route('dpr.save', ['id' => $deliveryPurchaseReceipt->id]) }}"
                                    onclick="return saveDPR();"><button type="button"
                                        class="btn btn-success">Save</button></a>
                            </div>
                        @endif
                        <!-- /.card-footer-->
                    </div>
                    <!-- /.card -->

    </section>
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
    </script>
@endsection
