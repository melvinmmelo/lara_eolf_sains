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
                        <li class="breadcrumb-item active">Pricing</li>
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
                <h3 class="card-title">Pricing</h3>

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
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Pricing Level</th>
                            <th>Product Code</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Price</th>

                        </tr>
                    </thead>
                    <tbody>
                    @foreach($pricing as $price)
                        <tr>
                            <td>{{ $price->p_level }}</td>
                            <td>{{ $price->p_code }}</td>
                            <td>{{ $price->p_unit }}</td>
                            <td>{{ $price->p_quant }}</td>
                            <td>{{ $price->p_price }}</td>
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
                                    <label class="form-label" for="price_code">Price Level</label>
                                    <select class="form-control" id="price_level" name="price_level">
                                    @foreach($pricelevels as $pl)
                                        <option value="{{ $pl->pl_name }}">{{ $pl->pl_desc }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label" for="price_unit">Product Code</label>
                                    <select class="form-control" id="price_code" name="price_code">
                                    @foreach ($products as $product)
                                    <option value="{{ $product->code }}">{{ $product->productName }}</option>
                                    @endforeach    
                                </select>
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-2">
                                
                                <div class="col-sm-6">
                                    <label class="form-label" for="price-quantity">Quantity</label>
                                    <input type="text" class="form-control" id="quant" name="quant">
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label" for="price_unit">Unit</label>
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
                                    <label class="form-label" for="price">Price</label>
                                    <input type="text" class="form-control" id="price" name="price">
                                </div>


                            </div>
                        </div>




                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success swalDefaultSuccess">Save changes</button>
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
