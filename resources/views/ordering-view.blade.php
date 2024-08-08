@extends('layouts.app')
@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>View Order </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">View order</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        @include('layouts.errors')
        <div class="card">
            <form action="#">
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm">
                                <div class="form-group">
                                    <label class="form-label" for="customer"><i style="color:red">*</i>Customer</label>:
                                    {{ $inbound->degic_no . '/' . $inbound->customer_name }}
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="deliveryPerson"><i style="color:red">*</i>Delivery
                                        Person</label>: {{ $inbound->delivery_person }}
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="deliveryPerson"><i style="color:red">*</i>Driver</label>:
                                    {{ $inbound->driver_name }}
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="pricelevel_id"><i style="color:red">*</i>Price
                                        Level</label>: {{ $priceLevel->pl_name }}
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="vehicle"><i style="color:red">*</i>Vehicle</label>:
                                    {{ $inbound->vehicle_no }}
                                </div>

                                <div class="form-checkbox">
                                    <input type="checkbox" id="withInvoice" name="with_invoice" value="on">
                                    <label for="withInvoice">With Invoice</label>
                                </div>

                                <div class="form-checkbox">
                                    <input type="checkbox" id="isBadPricing" name="bad_order" value="on">
                                    <label for="isBadPricing">Bad order</label>
                                </div>
                            </div>

                            <div class="col-sm-9">
                                <div id="inboundList">
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="table-responsive product-list">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
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
                                                                    <td class="align-middle">
                                                                        {{ $product['code'] . ' ' . $product['description'] }}
                                                                    </td>
                                                                    <td class="align-middle">{{ $product['unit'] }}</td>
                                                                    <td class="align-middle">{{ $product['quantity'] }}</td>
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
                                                            <td colspan="3"></td>
                                                            <td>
                                                                <h6 class="font-weight-bold mr-2">Total:</h6>
                                                            </td>
                                                            <td><input type="text" name="total" id="total"
                                                                    class="label-input"
                                                                    value="{{ array_sum($totalAmount) }}" readonly></td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td>
                                                                <h6 class="font-weight-bold mr-2">BO Amount:</h6>
                                                            </td>
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
                <!-- /.card-footer-->
            </form>
        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->
@endsection
