@extends('layouts.app')

@section('custom_css')
@endsection

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Materials Inventory</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Materials Inventory</li>
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
            <form action="{{ route('materialsInventory.delete') }}" method="POST">
                @csrf

                <div class="card-body table-responsive">

                    <div class="pb-2">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-inventory">
                            Add New
                        </button>

                        <button type="submit" class="btn btn-default" name="submit_form" value="delete" onclick="return askToDelete()">
                            Delete
                        </button>

                        <button type="button" class="btn btn-default" value="withdraw" data-target="#modal-withdraw" data-toggle="modal">
                            Withdraw
                        </button>
                    </div>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Unit Price</th>
                                <th>Total Amount</th>
                                <th>Remarks</th>
                                <th>Date</th>
                                <th>Modified By</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($materials as $material)
                            @php
                                $totalAmount = $material->quantity * $material->amount;
                            @endphp
                                <tr>
                                    <td>
                                        <input class="checkbox" type="checkbox" name="items[]" id="items"
                                            value="{{ $material->id }}">
                                    </td>
                                    <td>{{ $material->id }}</td>
                                    <td>{{ $material->name }}</td>
                                    <td>{{ $material->unit }}</td>
                                    <td>{{ $material->quantity }}</td>
                                    <td>{{ formatNumber($material->amount) }}</td>
                                    <td>{{ formatNumber($totalAmount) }}</td>
                                    <td>{{ $material->remarks }}</td>
                                    <td>{{ $material->created_at }}</td>
                                    <td>{{ $material->modified_by }}</td>
                                    <td>
                                        <a href="#" id="updateInventoryLink">Update</a> | <a
                                            href="{{ route('materialsInventory.history', ['id' => $material->id]) }}">History</a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                        <tfoot>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Unit Price</th>
                                <th>Amount</th>
                                <th>Remarks</th>
                                <th>Date</th>
                                <th>Modified By</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-inventory">
                        Add New
                    </button>
                </div>
                <!-- /.card-footer-->

                <div class="modal fade" id="modal-withdraw">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Withdraw Materials</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label class="form-label" for="requested_by">Requested by</label>
                                    <input type="text" class="form-control" name="requested_by" id="requested_by" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="issued_by">Issued by</label>
                                    <input type="text" class="form-control" name="issued_by" id="issued_by" value=" {{ auth()->user()->fullName }}">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="withdrawal_date">Date</label>
                                    <input type="date" class="form-control" name="withdrawal_date" id="withdrawal_date" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success" name="submit_form" value="withdraw">Save changes</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- /.card -->
        <div class="modal fade" id="modal-inventory">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create Inventory</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form action="{{ route('materialsInventory.store') }}" method="post">
                            @csrf

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="branch_code">Branch Code</label>
                                        <input type="text" class="form-control" name="branch_code" id="branch_code"
                                            value="{{ session('branch_code') }}" required readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="unit">Unit</label>
                                        <input type="text" class="form-control" name="unit" id="unit"
                                            value="">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="quantity">Quantity</label>
                                        <input type="number" class="form-control" name="quantity" id="quantity"
                                            value="" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="amount">Unit price</label>
                                        <input type="number" class="form-control" name="amount" id="amount"
                                            value="" required>
                                    </div>
                                </div>
                            </div>


                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modal-edit">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Inventory</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form action="{{ route('materialsInventory.update') }}" method="post">
                            @csrf
                            @method('PATCH')

                            <input type="hidden" class="form-control" name="inv_id" id="inv_id" required readonly>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_name">Name</label>
                                        <input type="text" class="form-control" name="e_name" id="e_name"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_unit">Unit</label>
                                        <input type="text" class="form-control" name="e_unit" id="e_unit">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_quantity">Quantity</label>
                                        <input type="number" class="form-control" name="e_quantity" id="e_quantity"
                                            value="" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_amount">Amount</label>
                                        <input type="number" class="form-control" name="e_amount" id="e_amount"
                                            value="" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_location">Location</label>
                                        <input type="text" class="form-control" name="e_location" id="e_location"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="e_remarks">Remarks</label>
                                        <input type="text" class="form-control" name="e_remarks" id="e_remarks">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection

@section('custom_js')
    <script>
        $(document).ready(function() {
            $('#example1').on('click', '#updateInventoryLink', function() {
                var table = $('#example1').DataTable();
                var data = table.row($(this).parents('tr')).data();
                console.log(data);
                $('#inv_id').val(data[1]);
                $('#e_name').val(data[2]);
                $('#e_unit').val(data[3]);
                $('#e_quantity').val(data[4]);
                $('#e_amount').val(data[5]);
                $('#e_location').val(data[6]);
                $('#e_remarks').val(data[7]);

                $('#modal-edit').modal('show');
            });
        });

        function askToDelete() {
            return confirm('Are you sure you want to delete this record(s)?');
        }
    </script>
@endsection
