@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    Delivery Receipt
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Delivery Receipt</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">

            <div class="card-body">

                @include('layouts.errors')


                <table id="example1" class="table table-bordered table-striped">
                <form action="{{ route('deliveryreceipt.index') }}" method="GET">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label" for="from_date">From</label>
                        <input type="date" class="form-control" name="from_date" required value="{{ request('from_date') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="to_date">To</label>
                        <input type="date" class="form-control" name="to_date" required value="{{ request('to_date') }}">
                    </div>

                    <div class="col-md-2 mt-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
        </form>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>DR No.</th>
                            <th>Generate By</th>
                            <th>Total Amount</th>
                            <th>Bad Orders</th>
                            <th>Discount</th>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
            @foreach($deliveryReceipts as $receipt)
            <tr>
                <td>{{ $receipt->date }}</td>
                <td>{{ $receipt->dr_no }}</td>
                <td>{{ $receipt->generated_by }}</td>
                <td>{{ $receipt->total_amount }}</td>
                <td>{{ $receipt->bad_orders }}</td>
                <td>{{ $receipt->discount }}</td>
                <td>{{ $receipt->amount_due }}</td>
                <td>{{ $receipt->amount_paid }}</td>
                <td>{{ $receipt->balance }}</td>
                <td>
                    <a href="{{ route('drprint', ['id' => $receipt->id ]) }}"><button type="button" class="btn btn-primary">Print</button></a>
                </td>
            </tr>
            @endforeach
        </tbody>
                    <tfoot>
                        <tr>
                            <th>Date</th>
                            <th>DR No.</th>
                            <th>Generate By</th>
                            <th>Total Amount</th>
                            <th>Bad Orders</th>
                            <th>Discount</th>
                            <th>Amount Due</th>
                            <th>Amount Paid</th>
                            <th>Balance</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-branch">
                    Add New
                </button>

                {{-- <button type="button" class="btn btn-success"><i class="fas fa-print"></i>&nbsp;Delivery Receipt</button> --}}
            </div>
            <!-- /.card-footer-->
        </div>



        <!-- /.card -->
        <div class="modal fade" id="modal-branch">
            <div class="modal-dialog">
            <form method="POST" action="{{ route('delivery-receipt.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Delivery Receipt</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="date"><i style="color:red">*</i>Date</label>
                                <input type="date" class="form-control" name="date" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="dr_no"><i style="color:red">*</i>DR No.</label>
                                <input type="text" class="form-control" name="dr_no" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="generated_by">Generated By</label>
                                <input type="text" class="form-control" name="generated_by">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="total_amount">Total Amount</label>
                                <input type="text" class="form-control" name="total_amount">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="bad_orders">Bad Orders</label>
                                <input type="text" class="form-control" name="bad_orders">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="discount">Discount</label>
                                <input type="text" class="form-control" name="discount">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="amount_due">Amount Due</label>
                                <input type="text" class="form-control" name="amount_due">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="amount_paid">Amount Paid</label>
                                <input type="text" class="form-control" name="amount_paid">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="form-label" for="balance">Balance</label>
                                <input type="text" class="form-control" name="balance">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save changes</button>
                </div>
            </div>
        </form>
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->



        </div>
    </section>
    <!-- /.content -->
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
    </script>
@endsection
