@extends('layouts.app')

@section('contents')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pricing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Pricing</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="row mb-2">
            <div class="col-sm-9">

                <div class="card">
                    <div class="card-body">

                        @include('layouts.errors')

                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Pricing Level</th>
                                    <th>Product Code</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pricing as $price)
                                    <tr>
                                        <td>{{ $price->pricelevel->pl_name }}</td>
                                        <td>{{ $price->p_code }}</td>
                                        <td>{{ $price->p_unit }}</td>
                                        <td>{{ $price->p_quant }}</td>
                                        <td>{{ $price->p_price }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#modalEditPrice"
                                                onclick="setToUpdatePrice('{{ $price->id }}')">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Pricing Level</th>
                                    <th>Product Code</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Price</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-price">
                            Add New
                        </button>
                    </div>
                    <!-- /.card-footer-->
                </div>

            </div>

            <div class="col-sm-3">
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group">
                                <label for="inputField">Level</label>
                                <select class="form-control" id="price_level" name="price_level">
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.card -->
            <div class="modal fade" id="modal-price">
                <div class="modal-dialog">
                    <form method="POST" action="/pricing/store">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Add Pricing</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <label class="form-label" for="pricing_id"><i style="color:red">*</i>Price
                                                Level</label>
                                            <select class="form-control" id="pricing_id" name="pricing_id">
                                                @foreach ($pricelevels as $pl)
                                                    <option value="{{ $pl->id }}">{{ $pl->pl_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label" for="price_code"><i style="color:red">*</i>Product
                                                Code</label>
                                            <select class="form-control" id="price_code" name="price_code">
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->code }}">{{ $product->productName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row mb-2">

                                        <div class="col-sm-6">
                                            <label class="form-label" for="quant"><i
                                                    style="color:red">*</i>Quantity</label>
                                            <input type="text" class="form-control" id="quant" name="quant">
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="form-label" for="price_unit"><i
                                                    style="color:red">*</i>Unit</label>
                                            <select class="form-control" id="price_unit" name="price_unit">
                                                <option value="Bag/s">Bag/s</option>
                                                <option value="Box/es">Box/es</option>
                                                <option value="Pc/s">Pc/s</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <label class="form-label" for="price"><i style="color:red">*</i>Price</label>
                                            <input type="text" class="form-control" id="price" name="price">
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

            <div>
    </section>
    <!-- /.content -->


    <div class="modal fade" id="modalEditPrice">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('price.update') }}">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><i style="color:red">*</i>New Price</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <input type="hidden" name="price_id" id="price_id" required readonly>

                        <div class="form-group">
                            <input type="text" class="form-control" id="e_price" name="e_price">
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save price</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        </form>
    </div>
@endsection

@section('custom_js')
    <script>
        function setToUpdatePrice(price_id) {
            $('#price_id').val(price_id);
        }
    </script>
@endsection
