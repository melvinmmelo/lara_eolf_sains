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
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label" for="code">From</label>
                                <input type="date" class="form-control" name="code" required>

                            </div>

                            <div class="col-md-3">
                                <label class="form-label" for="code">To</label>
                                <input type="date" class="form-control" name="code" required>

                            </div>
                        </div>
                    </div>
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

                        </tr>
                    </thead>
                    <tbody>


                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>


                        </tr>

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
                        </tr>
                    </tfoot>
                </table>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-branch">
                    Add New
                </button>

                <button type="button" class="btn btn-success"><i class="fas fa-print"></i>&nbsp;Delivery Receipt</button>
            </div>
            <!-- /.card-footer-->
        </div>



        <!-- /.card -->
        <div class="modal fade" id="modal-branch">
            <div class="modal-dialog">
                <form method="POST" action="#">
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
                                        <label class="form-label" for="code"><i style="color:red">*</i>Date</label>
                                        <input type="date" class="form-control" name="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="code"><i style="color:red">*</i>DR No.</label>
                                        <input type="text" class="form-control" name="" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Generate By</label>
                                        <input type="text" class="form-control" name="">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Total Amount</label>
                                        <input type="text" class="form-control" name="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Bad Orders</label>
                                        <input type="text" class="form-control" name="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Discount</label>
                                        <input type="text" class="form-control" name="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Amount Due</label>
                                        <input type="text" class="form-control" name="">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Amount Paid</label>
                                        <input type="text" class="form-control" name="">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Balance</label>
                                        <input type="text" class="form-control" name="">
                                    </div>
                                </div>
                            </div>


                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save changes</button>

                            </div>
                        </div>
                </form>
                <!-- /.modal-content -->
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
