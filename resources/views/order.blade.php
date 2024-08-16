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
                                <th>Balance Due</th>
                                <th>Status</th>
                                <th>W/ SI</th>
                                <th>Days Overdue</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $grandTotal = [];
                                $grandTotalBDue = [];
                            @endphp
                            @foreach ($inbounds as $inbound)
                                @php
                                    $total = $inbound->totalAmount;
                                    $grandTotal[] = $total;
                                    $grandTotalBDue[] = $total - $inbound->delivered_amount;
                                @endphp

                                <tr>
                                    <td>{{ $inbound->f_created_at }}</td>
                                    <td>{{ $inbound->code }}</td>
                                    <td>{{ $inbound->degic_no }}</td>
                                    <td>{{ $inbound->customer->fullName }}</td>
                                    <td><span class="label label-primary">{{ formatNumber($total) }}</span></td>
                                    <td>{{ formatNumber($total - $inbound->delivered_amount) }}</td>
                                    <td>{{ $inbound->status }}</td>
                                    <td>{{ $inbound->with_invoice === 1 ? 'W/ SI' : '' }}</td>
                                    <td>
                                        @if ($inbound->status == 'Completed')
                                            {{ number_format($inbound->created_at->diffInDays(now()), 0) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($inbound->status == 'Completed')
                                            <a href="#" data-target="#modalAddAmountDelivered"
                                                data-toggle="modal"><button class="btn btn-success"
                                                    onclick="setObId(`{{ $inbound->id }}`)"><i class="fas fa-plus"></i></button></a>

                                            <a href="#" data-target="#modalDeleteOrder"
                                                data-toggle="modal"><button class="btn btn-danger"
                                                    onclick="setObIdToDelete(`{{ $inbound->id }}`, `{{ $inbound->degic_no }}`, `{{ $inbound->customer_name }}`)"><i class="fas fa-trash"></i></button></a>
                                        @endif

                                        @if ($inbound->is_with_badOrder)
                                            <button class="btn btn-xs btn-danger">W/ BO</button>
                                        @endif

                                        @if ($inbound->status === 'Paid' or $inbound->totalBalance === 0)
                                            <a href="{{ route('order.view', ['inboundId' => $inbound->id]) }}"
                                                class="btn btn-default"><i class="fas fa-eye"></i></button></a>
                                        @else

                                            @if($inbound->delivery_receipt_id === NULL)
                                            <a href="{{ route('order.edit', ['inboundId' => $inbound->id]) }}"
                                                class="btn btn-primary"><i class="fas fa-edit"></i></button></a>
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
                                <th>@php echo formatNumber(array_sum($grandTotal)) @endphp</th>
                                <th>@php echo formatNumber(array_sum($grandTotalBDue)) @endphp</th>
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
                        <form action="{{ route("inbound.destroy") }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="inbound_id"><i style="color:red">*</i>Remarks</label>

                                        <input type="hidden" class="form-control" name="inbound_id" id="inbound_id"
                                            required readonly>

                                        <select class="form-control" name="remarks" id="remarks" required>
                                            <option value="Cancelled">Cancel</option>
                                            <option value="Wrong entry">Wrong entry</option>
                                        </select>

                                        <div>
                                            Type "Delete" to confirm.
                                            <input type="text" name="confirm_delete" id="confirm_delete" class="form-control" required>
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
        function setObId(obId) {
            $('#ob_id').val(obId);
        }

        function setObIdToDelete(obId, orderId, customerName) {

            $('#deleteHeaderTitle').text('Delete order ' + orderId +  ' for ' + customerName + '?');

            $('#inbound_id').val(obId);
            $('#modal-delete').modal('show');
        }
    </script>
@endsection
