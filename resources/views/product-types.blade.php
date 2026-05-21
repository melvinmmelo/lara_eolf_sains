@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Product Types</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
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

            <div class="card-body">

                @include('layouts.errors')
                <div class="pb-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-product-types">
                        Add New
                    </button>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Volume</th>
                            <th>Spoon PCS/BAG</th>
                            <th>Created at</th>
                            <th>Active</th>
                            <th></th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productTypes as $productType)
                            <tr>
                                <td>{{ $productType->sequence_no }}</td>
                                <td>{{ $productType->code }}</td>
                                <td>{{ $productType->name }}</td>
                                <td>{{ $productType->volume }}</td>
                                <td>{{ $productType->spoon_pcs_per_bag }}</td>
                                <td>{{ $productType->date_created }}</td>
                                <td>{!! statusBadge($productType->is_active ? 'Active' : 'Inactive') !!}</td>
                                <td>
                                    <a href="#" data-toggle="modal" data-target="#modalEditPType"
                                        onclick="setToUpdateProduct('{{ $productType->code }}','{{ $productType->name }}','{{ $productType->volume }}','{{ $productType->spoon_pcs_per_bag }}',{{ $productType->is_active }})"><button
                                            type="button" class="btn btn-sm btn-primary">Edit</button></a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Volume</th>
                            <th>Spoon PCS/BAG</th>
                            <th>Created at</th>
                            <th>Active</th>
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
                <form method="POST" action="{{ route('productType.store') }}">
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


                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="form-label" for="volume"><i style="color:red">*</i>Volume</label>
                                        <input type="text" class="form-control" name="volume"
                                            value="{{ old('volume') }}">
                                    </div>

                                    <div class="col-sm-4">
                                        <label class="form-label" for="volume">Spoon PCS/Bag</label>
                                        <input type="number" class="form-control" name="spoon_pcs_per_bag"
                                            value="{{ old('spoon_pcs_per_bag') ?? 0 }}" value="0">
                                    </div>

                                    <div class="col-sm-4">
                                        <label class="form-label" for="status">Active</label>
                                        <br>
                                        <input type="checkbox" name="is_active" id="mySwitch" data-bootstrap-switch
                                            data-on-text="Yes" data-off-text="No" data-on-color="success"
                                            data-off-color="danger">

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


        <div class="modal fade" id="modalEditPType">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('productType.update') }}">
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
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label class="form-label" for="volume">Volume</label>
                                        <input type="text" class="form-control" name="e_volume"
                                            value="{{ old('volume') }}" required>
                                    </div>

                                    <div class="col-sm-3">
                                        <label class="form-label" for="volume">Spoon PCS/Bag</label>
                                        <input type="number" class="form-control" name="e_spoon_pcs_per_bag"
                                            value="{{ old('spoon_pcs_per_bag') }} ?? 0" required>
                                    </div>

                                    <div class="col-sm-3">
                                        <label class="form-label" for="e_is_active">Active</label>
                                        <br>
                                        <input type="checkbox" name="e_is_active" id="eIsActiveSwitch"
                                            data-bootstrap-switch data-on-text="Yes" data-off-text="No"
                                            data-on-color="success" data-off-color="danger">

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

    </section>


    <!-- /.content -->
@endsection


@section('custom_js')
    <script>
        function setToUpdateProduct(code, name, volume, spoon_pcs_per_bag, is_active) {
            document.querySelector('input[name="e_code"]').value = code;
            document.querySelector('input[name="e_name"]').value = name;
            document.querySelector('input[name="e_volume"]').value = volume;
            document.querySelector('input[name="e_spoon_pcs_per_bag"]').value = spoon_pcs_per_bag;

            // Reflect the saved status on the Bootstrap Switch
            $('input[name="e_is_active"]').bootstrapSwitch('state', is_active == 1);
        }
    </script>
@endsection
