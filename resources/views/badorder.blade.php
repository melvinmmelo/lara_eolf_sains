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

                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>BO Id</th>
                            <th>Customer</th>
                            <th>Created Date</th>
                            <th>Total Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($badOrders as $badOrder)
                        <tr>
                            <td>{{ $badOrder['bo_id'] }}</td>
                            <td>
                                {{ optional($badOrder['customer'])->firstname }} 
                                {{ optional($badOrder['customer'])->lastname }} 
                                ({{ optional($badOrder['storeinfo'])->storename }})
                            </td>
                            <td>{{ $badOrder['created_at'] }}</td>
                            <td>{{ $badOrder['amount'] }}</td>
                            <td>{{ $badOrder['remarks'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>BO Id</th>
                            <th>Customer</th>
                            <th>Created Date</th>
                            <th>Total Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary"  
                        onclick="window.location.href='{{ route('addbadorder.create') }}'">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->


        <!-- /.modal-dialog -->
        <div class="modal fade" id="modal-badorder">
            <div class="modal-dialog">
                <form method="POST" action="#">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add bad order</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="code"><i style="color:red">*</i>Customer</label>
                                        <select class="form-control select2bs4" id="customer" name="customer">

                                        </select>
                                    </div>

                                    <div class="col-sm-12">
                                        <label class="form-label" for="name"><i style="color:red">*</i>Outbound</label>
                                        <select class="form-control select2bs4" id="outbound" name="outbound">

                                        </select>
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

    </section>

@endsection

@section('custom_js')
    <script>
        function confirmSetInactive() {
            return confirm("Are you sure you want to update the product status?")
        }
    </script>
@endsection
