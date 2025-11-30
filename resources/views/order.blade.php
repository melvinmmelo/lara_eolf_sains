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

        <form id="orderStatusForm" action="{{ route('order.updateStatus') }}" method="POST">

            <!-- Default box -->
            <div class="card">

                <div class="card-body">
                    @csrf
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

                        <a href="{{ route('orders.problematic') }}"><button type="button" class="btn btn-danger">
                                Deleted Orders
                            </button></a>

                        @role('admin')
                            {{-- update order status: --}}
                            <div class="float-right">
                                <select name="new_status" class="form-control d-inline-block" style="width: 150px;">
                                    <option value="">Change Status</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Free">Free</option>
                                </select>
                                <button type="submit" class="btn btn-outline-primary">Update Status</button>
                            </div>
                            {{-- end --}}
                        @endrole
                    </div>
                    <div class="tbContainer">
                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    @role('admin')
                                        <th>
                                            <input class="checkbox" type="checkbox" id="checkAll">
                                        </th>
                                    @endrole
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
                                        if ($inbound->is_with_sf) {
                                            $grandTotal_amount = $inbound->grandTotal - 1000;
                                        } else {
                                            $grandTotal_amount = $inbound->grandTotal;
                                        }
                                        $grandBOTotal[] = $inbound->bo_amount;
                                        $grandDiscount[] = $inbound->discount;
                                        $grandTotal[] = $grandTotal_amount;
                                        $grandTotalBDue[] = $inbound->totalBalance;
                                    @endphp

                                    <tr>
                                        @role('admin')
                                            <td>
                                                <input class="checkbox" type="checkbox" name="order_ids[]"
                                                    value="{{ $inbound->id }}">
                                            </td>
                                        @endrole
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
                                        <td><span
                                                class="label label-primary">{{ formatNumber($inbound->totalBalance) }}</span>
                                        </td>
                                        <td>{{ $inbound->status }}</td>
                                        <td>
                                            @if ($inbound->with_invoice === 1)
                                                <span class="sales-invoice-cell"
                                                      data-inbound-id="{{ $inbound->id }}"
                                                      data-invoice-no="{{ $inbound->sales_invoice_no }}"
                                                      style="cursor: pointer; text-decoration: underline;"
                                                      title="Click to edit">
                                                    {{ $inbound->sales_invoice_no ?: '(Click to add)' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($inbound->status == 'Completed')
                                                {{ number_format($inbound->created_at->diffInDays(now()), 0) }}
                                            @endif
                                        </td>
                                        <td>

                                            <a href="{{ route('order.view', ['inboundId' => $inbound->id]) }}"
                                                class="btn btn-default"><i class="fas fa-eye"></i></button></a>

                                            @if ($inbound->status == 'Completed' or $inbound->status == 'Unpaid')
                                                <a href="#" data-target="#modalDeleteOrder"
                                                    data-toggle="modal"><button class="btn btn-danger"
                                                        onclick="setObIdToDelete(`{{ $inbound->id }}`, `{{ $inbound->degic_no }}`, `{{ $inbound->customer_name }}`)"><i
                                                            class="fas fa-trash"></i></button></a>
                                            @endif

                                            @if ($inbound->status === 'Paid' or $inbound->balance === 0)
                                            @else

                                                {{-- add payment button --}}
                                                @if (!empty($inbound->order_slip_code) && !empty($inbound->order_slip_sno))
                                                    <button type="button" class="btn btn-success"
                                                        onclick="setObId(`{{ $inbound->id }}`, `{{ $inbound->totalBalance }}`)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-secondary" disabled
                                                        title="Order slip must be generated first">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @endif
                                                {{-- end --}}

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
                                    <th></th>
                                    <th>Total:</th>
                                    <th>{{ formatNumber(array_sum($grandTotal)) }} </th>
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

        </form>


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

        <!-- Edit Sales Invoice Modal -->
        <div class="modal fade" id="modal-edit-sales-invoice">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Sales Invoice Number</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="salesInvoiceForm">
                            @csrf
                            <input type="hidden" id="edit_inbound_id" name="inbound_id">

                            <div class="form-group">
                                <label for="edit_sales_invoice_no">Sales Invoice Number</label>
                                <input type="text"
                                       class="form-control"
                                       id="edit_sales_invoice_no"
                                       name="sales_invoice_no"
                                       maxlength="50"
                                       placeholder="Enter sales invoice number">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveSalesInvoice">Save</button>
                    </div>
                </div>
            </div>
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

        // Sales Invoice Edit Functionality
        $(document).on('click', '.sales-invoice-cell', function() {
            const inboundId = $(this).data('inbound-id');
            const invoiceNo = $(this).data('invoice-no');

            $('#edit_inbound_id').val(inboundId);
            $('#edit_sales_invoice_no').val(invoiceNo || '');

            $('#modal-edit-sales-invoice').modal('show');
        });

        $('#saveSalesInvoice').on('click', function() {
            const inboundId = $('#edit_inbound_id').val();
            const salesInvoiceNo = $('#edit_sales_invoice_no').val();

            $.ajax({
                url: '{{ route("sales-invoices.update") }}',
                type: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    inbound_id: inboundId,
                    sales_invoice_no: salesInvoiceNo
                },
                success: function(response) {
                    if (response.success) {
                        // Update the cell display
                        const cell = $(`.sales-invoice-cell[data-inbound-id="${inboundId}"]`);
                        cell.data('invoice-no', salesInvoiceNo);
                        cell.text(salesInvoiceNo || '(Click to add)');

                        $('#modal-edit-sales-invoice').modal('hide');

                        // Show success message
                        alert(response.message || 'Sales invoice number updated successfully.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error updating sales invoice number.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        });

        // Add checkbox functionality
        $(document).ready(function() {
            // Handle "Check All" checkbox
            $('#checkAll').on('click', function() {
                $('.checkbox').prop('checked', $(this).prop('checked'));
            });

            // Update "Check All" state based on individual checkboxes
            $('.checkbox').on('click', function() {
                if ($('.checkbox:checked').length === $('.checkbox').length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            });

            // Form submission validation
            $('#orderStatusForm').on('submit', function(e) {
                const checkedBoxes = $('input[name="order_ids[]"]:checked').length;
                if (checkedBoxes === 0) {
                    e.preventDefault();
                    alert('Please select at least one order to update.');
                    return false;
                }

                if (!$('select[name="new_status"]').val()) {
                    e.preventDefault();
                    alert('Please select a status to update to.');
                    return false;
                }

                return confirm('Are you sure you want to update the status of the selected orders?');
            });
        });
    </script>
@endsection
