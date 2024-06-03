@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bad Order Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Bad Orders</li>
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

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="form-label" for="name"><i style="color:red">*</i>Customer</label>
                            <select class="form-control select2bs4" id="customer" name="costumer">

                            </select>
                        </div>

                        <div class="col-sm-12">
                            <label class="form-label" for="cust_fname">Red. DR:</label>
                            <input type="text" class="form-control" id="" name="">

                        </div>

                        <div class="col-sm-12">
                            <label class="form-label" for="cust_fname">BO Percentage</label>
                            <input type="text" class="form-control" id="" name="">

                        </div>
                        <div class="col-sm-12">
                            <label class="form-label" for="cust_fname">Remarks</label>
                            <input type="text" class="form-control" id="" name="">

                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label class="form-label" for="name"><i style="color:red">*</i>Item</label>
                        <select class="form-control select2bs4" id="customer" name="costumer">

                        </select>
                    </div>

                    <div class="col-sm-2">
                        <label class="form-label" for="cust_fname">Unit Price</label>
                        <input type="text" class="form-control" id="" name="">

                    </div>

                    <div class="col-sm-2">
                        <label class="form-label" for="cust_fname">Quantity</label>
                        <input type="text" class="form-control" id="" name="">


                    </div>

                    <div class="col-sm-2">
                        <div><label class="form-label" for="cust_fname">&nbsp; </label></div>
                        <button type="button" class="btn btn-success">
                            Add
                        </button>

                    </div>
                </div>

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>

                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Unit Price</th>
                            <th>Amount</th>

                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary">
                    Save
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->


        <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        </div>
        </div>

    </section>



    <!-- /.content -->
@endsection


@section('custom_js')
    <script>
        function confirmSetInactive() {
            return confirm("Are you sure you want to update the product status?")
        }
    </script>
@endsection
