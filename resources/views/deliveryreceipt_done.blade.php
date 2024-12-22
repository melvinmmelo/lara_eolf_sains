@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    Done Delivery Receipt
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Done Delivery Receipt</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        @include('layouts.errors')

        <div class="card">

            <div class="card-body">

                <form action="{{ route('deliveryreceipt.indexDone') }}" method="GET">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label" for="from_date">From</label>
                                <input type="date" class="form-control" name="from_date" required
                                    value="{{ request('from_date') }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="to_date">To</label>
                                <input type="date" class="form-control" name="to_date" required
                                    value="{{ request('to_date') }}">
                            </div>

                            <div class="col-md-2 mt-4">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>

                <table id="example1" class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>DR No.</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Bad Orders</th>
                            <th>Discount</th>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th>Generate By</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotal = [];
                            $grandTotalDiscount = [];
                            $grandTotalAmtPaid = [];
                            $grandTotalBalance = [];
                        @endphp
                        @foreach ($deliveryReceipts as $receipt)
                            @php
                                    $grandTotal[] = $receipt->inbound->grandTotal;
                                    $grandTotalDiscount[] = $receipt->inbound->discount;
                                    $grandTotalAmtPaid[] = $receipt->inbound->delivered_amount;
                                    $grandTotalBalance[] = $receipt->inbound->totalBalance;
                            @endphp
                            <tr>
                                <td>{{ $receipt->fCreatedAt }}</td>
                                <td>{{ $receipt->code }}</td>
                                <td>{{ $receipt->customer_name }}</td>
                                <td>{{ formatNumber($receipt->inbound->grandTotal) }}</td>
                                <td>{{ formatNumber($receipt->inbound->bo_amount) }}</td>
                                <td>{{ formatNumber($receipt->inbound->discount) }}</td>
                                <td>{{ formatNumber($receipt->inbound->totalAmount) }}</td>
                                <td>{{ formatNumber($receipt->inbound->delivered_amount) }}</td>
                                <td>{{ formatNumber($receipt->inbound->totalBalance) }}</td>
                                <td>{{ $receipt->generated_by }}</td>
                                <td>
                                @if ($receipt->inbound->status === 'Deleted' or $receipt->inbound->status === 'Cancelled')
                                        <span class="badge badge-danger">{{ $receipt->inbound->status }}</span>
                                    @elseif ($receipt->inbound->status === 'Paid')
                                        <span class="badge badge-primary">{{ $receipt->inbound->status }}</span>
                                    @else
                                        <span class="badge badge-success">{{ $receipt->inbound->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('drprint', ['id' => $receipt->id]) }}"><button type="button"
                                            class="btn btn-primary">Print</button></a>

                                    @if (Auth::user()->isSuperAdmin())
                                        <a href="#" onclick="setToUpdateDR('{{ $receipt->id  }}')"><button
                                                type="button" class="btn btn-success"><i class="fas fa-edit"></i></button></a>
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
                            <th>@php echo formatNumber(array_sum($grandTotalDiscount)) @endphp</th>
                            <th>@php echo formatNumber(array_sum($grandTotal)) @endphp</th>
                            <th>{{ formatNumber(array_sum($grandTotalAmtPaid)) }}</th>
                            <th>{{ formatNumber(array_sum($grandTotalBalance)) }}</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
        </div>
    </section>
    <!-- /.content -->

    <div class="modal fade" id="modal-edit-dr">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Delivery Receipt</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('deliveryreceipt.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="text" class="form-control" name="dr_id" id="dr_id" required readonly>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="form-label" for="discount">Discount (%)</label>
                                    <input type="number" class="form-control" name="discount" value="0" required>
                                    {{-- Add check if fixed amount
                                    <input type="checkbox" name="is_fixed_amount" value="1"> Fixed Amount--}}
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_js')
    <script>
        // function setToUpdateBranch() {
        //     // get datatable
        //     var table = $('#example1').DataTable();

        //     // get the data of clicked row
        //     var data = table.row($(this).parents('tr')).data();

        //     document.querySelector('input[name=e_code]').value = data[0];
        //     document.querySelector('input[name=e_name]').value = data[1];
        //     document.querySelector('textarea[name=e_address]').value = data[2];
        //     document.querySelector('input[name=e_office_no]').value = data[3];

        // }

        function setToUpdateBranch() {

            var table = $('#example1').DataTable();

            $('#example1 tbody').on('click', 'tr', function() {

                var data = table.row(this).data();

                document.querySelector('input[name=e_code]').value = data[0];
                document.querySelector('input[name=e_name]').value = data[1];
                document.querySelector('textarea[name=e_address]').value = data[2];
                document.querySelector('input[name=e_office_no]').value = data[3];


            });
        }

        function setToUpdateDR(id) {
            document.getElementById("dr_id").value = id;
            $('#modal-edit-dr').modal('show');
        }
    </script>
@endsection
