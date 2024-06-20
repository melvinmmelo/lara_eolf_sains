@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Products</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
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
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-products">
                        Add New
                    </button>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>

                            <th>Code</th>
                            <th>Type</th>
                            <th>Flavor</th>
                            <th>Created at</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->code }}</td>
                                <td>{{ $product->productType->name }}</td>
                                <td>{{ $product->productVariant->name }}</td>
                                <td>{{ $product->date_created }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                        data-target="#modalEditPrice" onclick="setToUpdate('{{ $product->code }}')">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Flavor</th>
                            <th>Created at</th>
                            {{-- <th>Active</th> --}}
                            <th></th>

                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-products">
                    Add New
                </button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->
    </section>

    <div class="modal fade" id="modal-products">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('product.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Products</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="name"><i style="color:red">*</i>Type</label>
                                    <select class="form-control select2bs4" id="product_type_code" name="product_type_code">
                                        @foreach ($types as $type)
                                            <option value="{{ $type->code }}">{{ $type->code }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label" for="name"><i style="color:red">*</i>Variant</label>
                                    <select class="form-control select2bs4" id="product_variant_code"
                                        name="product_variant_code">
                                        @foreach ($variants as $variant)
                                            <option value="{{ $variant->code }}">{{ $variant->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save
                            changes</button>
                    </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

    <div class="modal fade" id="modalEditPrice">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('product.update') }}">
                @csrf
                @method('PATCH')

                <input type="hidden" name="product_code" id="product_code" required readonly>

                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Product</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <label class="form-label" for="name"><i style="color:red">*</i>Type</label>
                                    <select class="form-control select2bs4" id="product_type_code" name="product_type_code">
                                        @foreach ($types as $type)
                                            <option value="{{ $type->code }}">{{ $type->code }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label" for="name"><i style="color:red">*</i>Variant</label>
                                    <select class="form-control select2bs4" id="product_variant_code"
                                        name="product_variant_code">
                                        @foreach ($variants as $variant)
                                            <option value="{{ $variant->code }}">{{ $variant->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save
                            changes</button>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>

    <!-- /.content -->
@endsection


@section('custom_js')
    <script>
        function confirmSetInactive() {
            return confirm("Are you sure you want to update the product status?")
        }

        function setToUpdate(code) {
            $('#product_code').val(code);
        }
    </script>
@endsection
