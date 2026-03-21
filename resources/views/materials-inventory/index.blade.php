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

        <form id="deleteForm" action="{{ route('materialsInventory.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="pb-2">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-inventory">
                                    Add New
                                </button>
                                <button type="submit" class="btn btn-danger" onclick="return askToDelete()">
                                    Delete Selected
                                </button>
                                <a href="{{ route('material-withdrawals.index') }}" class="btn btn-warning">
                                    Material Withdrawal
                                </a>
                                @can('admin')
                                <a href="{{ route('materialsInventory.receive') }}" class="btn btn-success">
                                    <i class="fas fa-truck"></i> Receive Delivery
                                </a>
                                @endcan
                            </div>
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                            <th>Unit</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total Amount</th>
                                            <th>Remarks</th>
                                            <th>Location</th>
                                            <th>Modified By</th>
                                            <th>Actions</th>
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
                                                <td>{{ $material->name }}</td>
                                                <td>{{ $material->unit }}</td>
                                                <td>{{ $material->quantity }}</td>
                                                <td>{{ formatNumber($material->amount) }}</td>
                                                <td>{{ formatNumber($totalAmount) }}</td>
                                                <td>{{ $material->remarks }}</td>
                                                <td>{{ $material->location }}</td>
                                                <td>{{ $material->modified_by }}</td>
                                                <td>
                                                    <a href="#" id="updateInventoryLink">Update</a> | <a
                                                        href="{{ route('materialsInventory.history', ['id' => $material->id]) }}">History</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#modal-inventory">
                                Add New
                            </button>
                        </div>
                        <!-- /.card-footer-->
                    </div>
                </div>
            </div>
        </form>

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
                                        <input type="text" class="form-control" name="amount" id="amount"
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

                        <form action="{{ route('materialsInventory.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH">

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
                                        <input type="text" class="form-control" name="e_amount" id="e_amount"
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
            $('#example1').on('click', '#updateInventoryLink', function(e) {
                e.preventDefault();
                var tr = $(this).closest('tr');
                $('#inv_id').val(tr.find('input[type="checkbox"]').val());
                $('#e_name').val(tr.find('td:eq(1)').text());
                $('#e_unit').val(tr.find('td:eq(2)').text());
                $('#e_quantity').val(tr.find('td:eq(3)').text());
                $('#e_amount').val(tr.find('td:eq(4)').text().replace(/[^0-9.-]+/g, ''));
                $('#e_remarks').val(tr.find('td:eq(6)').text());
                $('#e_location').val(tr.find('td:eq(7)').text());

                $('#modal-edit').modal('show');
            });
        });

        function askToDelete() {
            return confirm('Are you sure you want to delete the selected items?');
        }
    </script>
@endsection
