@extends('layouts.app')

@section('contents')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Product Types</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Product Types</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body">

            @include('layouts.errors')

            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Volume</th>
                        <th>Active</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($productTypes as $productType)
                        <tr>
                            <td>{{ $productType->code }}</td>
                            <td>{{ $productType->name }}</td>
                            <td>{{ $productType->volume }}</td>
                            <td>{{ $productType->is_active }}</td>
                        </tr>
                    @endforeach

                </tbody>
                <tfoot>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Volume</th>
                        <th>Active</th>

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
            <form method="POST" action="{{ route('productsType.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Product Types</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    <label class="form-label" for="code">Code</label>
                                    <input type="text" class="form-control" name="code" value="{{ old('code') }}">
                                </div>
                                <div class="col-sm-9">
                                    <label class="form-label" for="name">Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-label" for="volume">Volume</label>
                                    <input type="text" class="form-control" name="volume" value="{{ old('volume') }}>
                                </div>

                                 <div class="col-sm-6">
                                    <label class="form-label" for="status">Active</label>
                                    <br>
                                    <input type="checkbox" name="is_active" id="mySwitch" data-bootstrap-switch data-on-text="Yes"
                                        data-off-text="No" data-on-color="success" data-off-color="danger">

                                    <div style="margin-bottom: 20px"></div>
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
    </div>

</section>


<!-- /.content -->
@endsection
