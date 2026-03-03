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

        .summary-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .summary-section h5 {
            margin-bottom: 15px;
            color: #495057;
        }

        .summary-section .btn {
            margin: 3px;
            min-width: 100px;
        }
    </style>
@endsection
@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Inbound Inventory - {{ $deliveryPurchaseReceipt->status == 'Completed' ? 'View' : 'Encode' }}
                        Products</h1>
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
                            @can('admin')
                            <div class="mb-3">
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-delete-dpr">
                                    <i class="fas fa-trash"></i> Delete DPR
                                </button>
                            </div>
                            @endcan
                            <form action="{{ route('dpr-product.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">Items</label>
                                            <select class="form-control select2bs4" name="product_code" style="width: 100%;"
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

                        @if ($deliveryPurchaseReceipt->status == 'Completed')
                            <p>Summary</p>
                            <div class="d-flex flex-row mb-2">
                                @foreach ($productsSumm as $productSummary)
                                    <div class="btn btn-default mr-2">
                                        {{ $productSummary['code'] . ': ' . $productSummary['quantity'] }}</div>
                                @endforeach
                            </div>
                            @can('admin')
                            <div class="mb-3 d-flex gap-2">
                                <button type="button" class="btn btn-warning mr-2" data-toggle="modal" data-target="#modal-move-branch">
                                    <i class="fas fa-exchange-alt"></i> Move to Another Branch
                                </button>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-delete-dpr">
                                    <i class="fas fa-trash"></i> Delete DPR
                                </button>
                            </div>
                            @endcan
                        @endif
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table id="dprProductsTb" class="table table-bordered table-striped">
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
                                                                <span class="badge badge-danger">Hold:
                                                                    {{ $dprProd->hold }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $dprProd->price }}</td>
                                                        <td>{{ formatNumber($dprProd->quantity * $dprProd->price) }}</td>
                                                        <td>

                                                            @if ($deliveryPurchaseReceipt->status == 'Encoding')
                                                                <a href="{{ route('dpr.delete', ['drid' => $deliveryPurchaseReceipt->id, 'pcode' => $dprProd->code]) }}"
                                                                    onclick="return confirmDeleteProduct()"
                                                                    class="btn btn-sm btn-danger">Delete</a>

                                                                <a href="#"
                                                                    onclick="holdProduct(`{{ $deliveryPurchaseReceipt->id }}`,`{{ $dprProd->code }}`)"
                                                                    class="btn btn-sm btn-danger">Hold</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                            @endif
                                        </tbody>
                                        <tfoot class="desktop-view">
                                            <tr>
                                                <td colspan="2"></td>
                                                <td>Total:</td>
                                                <td>{{ isset($sum) ? formatNumber($sum) : 0 }}</td>
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
                            <div class="card-footer mt-2">
                                <div class="summary-section mb-3">
                                    <h5 class="font-weight-bold">Summary</h5>
                                    <div class="d-flex flex-row flex-wrap">
                                        @foreach ($productsSumm as $productSummary)
                                            <div class="btn btn-default mr-2 mb-2">
                                                {{ $productSummary['code'] . ': ' . $productSummary['quantity'] }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <a href="{{ route('dpr.save', ['id' => $deliveryPurchaseReceipt->id]) }}"
                                    onclick="return saveDPR();">
                                    <button type="button" class="btn btn-success">Save</button>
                                </a>
                            </div>
                        @endif
                        <!-- /.card-footer-->
                    </div>
                    <!-- /.card -->

    </section>


    <div class="modal fade" id="modal-hold-product">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('dpr.holdProduct') }}" method="post">

                    <div class="modal-body">
                        @csrf

                        <input type="hidden" class="form-control" name="hold_dpr_id" id="hold_dpr_id" value=""
                            required readonly>

                        <input type="hidden" class="form-control" name="hold_pcode" id="hold_pcode" value="" required
                            readonly>

                        <div class="form-group">
                            <label class="form-label" for="hold_qty"><i style="color:red">*</i>Quantity to hold</label>
                            <input type="number" class="form-control" name="hold_qty" id="hold_qty" value=""
                                required>
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>

                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
    <!-- /.content -->

    @can('admin')
    <div class="modal fade" id="modal-move-branch">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Move DPR to Another Branch</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-warning"><strong>Note:</strong> This will reverse inventory on the current branch and apply it to the target branch.</p>

                    <form action="{{ route('dpr.moveBranch', $deliveryPurchaseReceipt->id) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="target_branch_code">Target Branch <span class="text-danger">*</span></label>
                            <select class="form-control" name="target_branch_code" id="target_branch_code" required>
                                <option value="">-- Select Branch --</option>
                                @foreach ($gbranches as $gbranch)
                                    @if ($gbranch->code !== $deliveryPurchaseReceipt->branch_code)
                                        <option value="{{ $gbranch->code }}">{{ $gbranch->code }} — {{ $gbranch->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="move_reason">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="reason" id="move_reason" rows="3" required placeholder="Enter reason for moving..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="move_password">Admin Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" id="move_password" required placeholder="Enter your password to confirm">
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning"><i class="fas fa-exchange-alt"></i> Move</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @can('admin')
    <div class="modal fade" id="modal-delete-dpr">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Delete DPR</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-danger"><strong>Warning:</strong> This will permanently delete this DPR.
                        @if ($deliveryPurchaseReceipt->status === 'Completed')
                            Inventory stocks will be reversed.
                        @endif
                        This action cannot be undone.
                    </p>

                    <form action="{{ route('dpr.destroyDPR', $deliveryPurchaseReceipt->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="form-group">
                            <label for="delete_reason">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="reason" id="delete_reason" rows="3" required placeholder="Enter reason for deleting..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="delete_password">Admin Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" id="delete_password" required placeholder="Enter your password to confirm">
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endcan

@endsection

@section('custom_js')
    <script>
        function saveDPR() {
            let summaryText = '';
            document.querySelectorAll('.summary-section .btn').forEach(btn => {
                summaryText += '\n' + btn.textContent.trim();
            });

            return confirm('Are you sure you want to save this DR with the following quantities?' + summaryText);
        }

        function confirmDeleteProduct() {
            return confirm('Are you sure you want to delete this product?');
        }

        function holdProduct(dpr_id, product_code) {
            $('#hold_dpr_id').val(dpr_id);
            $('#hold_pcode').val(product_code);
            $('#modal-hold-product').modal('show');
        }
    </script>
@endsection
