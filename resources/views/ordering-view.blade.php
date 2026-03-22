@extends('layouts.app')
@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>View Order {{ $inbound->code }}</h1>
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
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm">
                            <div class="form-group">
                                <h4 class="form-label" for="date_modified">Date Modified: 
                                {{ $inbound->updated_at->format('Y-m-d H:i') }}</h4>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="text-right">
                                <a href="{{ route('order.index') }}" class="btn btn-primary mr-2"><i class="fa fa-arrow-left"></i> Orders</a>
                                @can('admin')
                                <form id="revertForm" action="{{ route('itemdata.revertOrderItems', $inbound->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to revert this order\'s items back to inventory? This action cannot be undone.')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger"><i class="fa fa-undo"></i> Revert Order Items to Inventory</button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    </div>

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
                                    <input type="checkbox" id="withInvoice" name="with_invoice" value="on" disabled>
                                    <label for="withInvoice">With Invoice</label>
                                </div>
    
                                <div class="form-checkbox">
                                    <input type="checkbox" id="isBadPricing" name="bad_order" value="on" disabled>
                                    <label for="isBadPricing">Bad order</label>
                                </div>
    
                                <div class="form-checkbox">
                                    <input type="checkbox" id="isFOC" name="foc" value="on" disabled>
                                    <label for="isFOC">FOC</label>
                                </div>
    
                                <div class="form-checkbox">
                                    <input type="checkbox" id="withSF" name="with_sf" value="on" disabled>
                                    <label for="withSF">With Delivery Charge</label>
                                </div>

                                <div class="form-checkbox">
                                    <input type="checkbox" id="withDR" name="with_dr" value="on" disabled>
                                    <label for="withDR">With DR</label>
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

                                            <table class="table table-bordered table-striped mt-3">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2">Payment Summary</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Status</td>
                                                        <td>{{ $inbound->status }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Paid</td>
                                                        <td>{{ formatNumber($inbound->ledger_delivered_amount) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Balance</td>
                                                        <td>{{ formatNumber($inbound->totalBalance) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Latest Method</td>
                                                        <td>{{ $inbound->payment_type ?: '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Latest Ref. No.</td>
                                                        <td>{{ $inbound->ref_no ?: '-' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <table class="table table-bordered table-striped mt-3">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4">Payment Ledger</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Method</th>
                                                        <th>Ref No.</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($inbound->payments as $payment)
                                                        <tr>
                                                            <td>{{ optional($payment->payment_date)->format('Y-m-d') }}</td>
                                                            <td>{{ $payment->payment_method ?: '-' }}</td>
                                                            <td>{{ $payment->reference_no ?: '-' }}</td>
                                                            <td>{{ formatNumber($payment->amount) }}</td>
                                                        </tr>
                                                        @if($payment->remarks)
                                                            <tr>
                                                                <td colspan="4"><strong>Remarks:</strong> {{ $payment->remarks }}</td>
                                                            </tr>
                                                        @endif
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center">No payment entries yet.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-footer-->
        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->
@endsection

@section('custom_js')
    <script>
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

         @if($inbound->delivery_receipt_id)
            document.getElementById("withDR").checked = true;
         @endif
    </script>
@endsection
