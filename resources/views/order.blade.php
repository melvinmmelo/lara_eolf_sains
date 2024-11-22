@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Orders</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        @include('layouts.errors')


        <!-- Default box -->
        <div class="card">

            <div class="card-body">
                <div class="pb-2">
                    <a href="{{ route('order.create') }}"><button type="button" class="btn btn-primary">
                            Add New
                        </button></a>

                    <a href="{{ route('orders.free') }}"><button type="button" class="btn btn-default">
                            Free Orders
                        </button></a>

                    <a href="{{ route('orders.paid') }}"><button type="button" class="btn btn-default">
                            Paid Orders
                        </button></a>
                </div>
                <div class="tbContainer">
                    <table id="example3" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Order No.</th>
                                <th>Degic No.</th>
                                <th>Customer</th>
                                <th>Invoice Amount</th>
                                <th>Discount</th>
                                <th>Bad Orders</th>
                                <th>Balance Due</th>
                                <th>Status</th>
                                <th>W/ SI</th>
                                <th>Days Overdue</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grandBOTotal = [];
                                $grandDiscount = [];
                                $grandTotal = [];
                                $grandTotalBDue = [];
                            @endphp
                            @foreach ($inbounds as $inbound)
                                @php
                                    if($inbound->is_with_sf){
                                        $grandTotal_amount = $inbound->grandTotal - 1000;
                                    }else{
                                        $grandTotal_amount = $inbound->grandTotal;
                                    }
                                    $grandBOTotal[] = $inbound->bo_amount;
                                    $grandDiscount[] = $inbound->discount;
                                    $grandTotal[] = $grandTotal_amount;
                                    $grandTotalBDue[] = $inbound->totalBalance;
                                @endphp

                                <tr>
                                    <td>{{ $inbound->f_created_at }}</td>
                                    <td>{{ $inbound->code }}</td>
                                    <td>{{ $inbound->degic_no }}</td>
                                    <td>{{ $inbound->customer->fullName }}</td>
                                    <td>{{ formatNumber($grandTotal_amount) }}
                                         @if ($inbound->is_with_sf)
                                                (<span class="label label-warning">+1000</span>)
                                            @endif

                                    </td>
                                    <td>{{ formatNumber($inbound->discount) }}</td>
                                    <td>{{ formatNumber($inbound->bo_amount) }}</td>
                                    <td><span class="label label-primary">{{ formatNumber($inbound->totalBalance) }}</span>
                                    </td>
                                    <td>{{ $inbound->status }}</td>
                                    <td>{{ $inbound->with_invoice === 1 ? 'W/ SI' : '' }}</td>
                                    <td>
                                        @if ($inbound->status == 'Completed')
                                            {{ number_format($inbound->created_at->diffInDays(now()), 0) }}
                                        @endif
                                    </td>
                                    <td>

                                        <a href="{{ route('order.view', ['inboundId' => $inbound->id]) }}"
                                            class="btn btn-default"><i class="fas fa-eye"></i></button></a>

                                        @if ($inbound->status == 'Completed' or $inbound->status == 'Unpaid')
                                            <a href="#" data-target="#modalDeleteOrder" data-toggle="modal"><button
                                                    class="btn btn-danger"
                                                    onclick="setObIdToDelete(`{{ $inbound->id }}`, `{{ $inbound->degic_no }}`, `{{ $inbound->customer_name }}`)"><i
                                                        class="fas fa-trash"></i></button></a>
                                        @endif

                                        @if ($inbound->status === 'Paid' or $inbound->balance === 0)
                                        @else
                                            <a href="#"
                                                onclick="setObId(`{{ $inbound->id }}`, `{{ $inbound->totalBalance }}`)"><button
                                                    class="btn btn-success"><i class="fas fa-plus"></i></button></a>

                                            @if ($inbound->delivery_receipt_id === null)
                                                <a href="{{ route('order.edit', ['inboundId' => $inbound->id]) }}"
                                                    class="btn btn-primary"><i class="fas fa-edit"></i></button></a>
                                            @else
                                                @role('admin')
                                                    <a href="{{ route('order.edit', ['inboundId' => $inbound->id]) }}"
                                                        class="btn btn-primary"><i class="fas fa-edit"></i></button></a>
                                                @endrole
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Total:</th>
                                <th>{{ formatNumber(array_sum($grandTotal)) }}</th>
                                <th>{{ formatNumber(array_sum($grandDiscount)) }}</th>
                                <th>{{ formatNumber(array_sum($grandBOTotal)) }}</th>
                                <th>{{ formatNumber(array_sum($grandTotalBDue)) }}</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <a href="{{ route('order.create') }}"><button type="button" class="btn btn-primary">
                        Add New
                    </button></a>
            </div>
            <!-- /.card-footer-->
        </div>

        <!-- /.card -->
        <div class="modal fade" id="modal-delete">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="deleteHeaderTitle">Delete order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('inbound.destroy') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <div class="form-group">

                                            <label class="form-label" for="inbound_id"><i
                                                    style="color:red">*</i>Remarks</label>

                                            <input type="hidden" class="form-control" name="inbound_id" id="inbound_id"
                                                required readonly>

                                            <select class="form-control" name="remarks" id="remarks" required>
                                                <option value="Cancelled">Cancel</option>
                                                <option value="Deleted">Delete</option>
                                                <option value="Wrong entry">Wrong entry</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label" for="remarks_details">Remarks</label>
                                            <textarea class="form-control" name="remarks_details" id="remarks_details" rows="3" required></textarea>
                                        </div>

                                        <div>
                                            Type "Delete" to confirm.
                                            <input type="text" name="confirm_delete" id="confirm_delete"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </div>

                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
        </div>
    </section>
    <!-- /.content -->


    @include('modalAddAmountDelivered')
@endsection

@section('custom_js')
    <script>
        function setObId(obId, totalAmount) {
            if (totalAmount == 0) {

                alert("This order is already paid.");
            }

            $('#ob_id').val(obId);
            $('#delivered_amount').val(totalAmount);

            $('#modalAddAmountDelivered').modal('show');
        }

        function setObIdToDelete(obId, orderId, customerName) {

            $('#deleteHeaderTitle').text('Delete order ' + orderId + ' for ' + customerName + '?');

            $('#inbound_id').val(obId);
            $('#modal-delete').modal('show');
        }
    </script>
@endsection
