@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Product Variants</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Product Variants</li>
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
                            <th>Code</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Created at</th>

                            <th></th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productVariants as $productVariant)
                            <tr>
                                <td>{{ $productVariant->code }}</td>
                                <td>{{ $productVariant->name }}</td>
                                <td>{{ $productVariant->is_active ? 'Active' : 'Not Active' }}</td>
                                <td>{{ $productVariant->date_created }}</td>
                                <td>
                                    <a href="#" data-toggle="modal" data-target="#modalEdit"
                                        onclick="setToUpdateProduct('{{ $productVariant->code }}','{{ $productVariant->name }}')"><button
                                            type="submit" class="btn btn-sm btn-primary">Edit</button></a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Created at</th>

                            <th></th>

                        </tr>
                    </tfoot>
                </table>


            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-product-types">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->


        <div class="modal fade" id="modal-product-types">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('productVariant.store') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Product Variant</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label class="form-label" for="code"><i style="color:red">*</i>Code</label>
                                        <input type="text" class="form-control" name="code"
                                            value="{{ old('code') }}">
                                    </div>
                                    <div class="col-sm-9">
                                        <label class="form-label" for="name"><i style="color:red">*</i>Name</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            </form>
        </div>


        <div class="modal fade" id="modalEdit">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('productVariant.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit product</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <label class="form-label" for="code">Code</label>
                                        <input type="text" class="form-control" name="e_code"
                                            value="{{ old('code') }}" required readonly>

                                    </div>
                                    <div class="col-sm-9">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" class="form-control" name="e_name"
                                            value="{{ old('name') }}" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="address">Active</label>
                                        <br>
                                        <input type="checkbox" id="mySwitch" data-bootstrap-switch data-on-text="on"
                                            data-off-text="off" data-on-color="success" data-off-color="danger"
                                            name="e_status">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            </form>
        </div>

    </section>


    <!-- /.content -->
@endsection

@section('custom_js')
    <script>
        function setToUpdateProduct(code, name) {
            document.querySelector('input[name="e_code"]').value = code;
            document.querySelector('input[name="e_name"]').value = name;
        }
    </script>
@endsection
