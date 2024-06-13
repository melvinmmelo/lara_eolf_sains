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
                                    <th>Product</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Created at</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pricing as $price)
                                    <tr>
                                        <td>{{ $price->pricelevel->pl_name }}</td>
                                        <td>{{ $price->product->productName ?? $price->productType->name }}</td>
                                        <td>{{ $price->p_unit }}</td>
                                        <td>{{ $price->p_quant }}</td>
                                        <td>{{ $price->p_price }}</td>
                                        <td>{{ $price->date_created }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#modalEditPrice"
                                                onclick="setToUpdatePrice('{{ $price->id }}', '{{ $price->p_quant }}', '{{ $price->p_unit }}', '{{ $price->p_price }}')">Edit</a>
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
                                    <th>Created at</th>
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
                                    <option value="">--Select--</option>
                                    @foreach ($pricelevels as $pL)
                                        <option value="{{ $pL->pl_name }}">{{ $pL->pl_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" id="clear" class="btn btn-primary">Clear</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.card -->
            <div class="modal fade" id="modal-price">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Pricing</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <div>
                                <label class="form-label" for="pricing_id"><i style="color:red">*</i>Price
                                    Level</label>
                                <select class="form-control" id="pricing_id">
                                    <option value="">--Select--</option>
                                    @foreach ($pricelevels as $pl)
                                        <option value="{{ $pl->id }}">{{ $pl->pl_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <form id="ifNotBadPricing" method="POST" action="/pricing/store">
                                @csrf

                                <div class="form-group">
                                    <input type="hidden" name="pricing_id" id="nb_pricing_id" class="form-control" required
                                        readonly>
                                </div>
                                <div class="form-group">

                                    <label class="form-label" for="price_code"><i style="color:red">*</i>Product
                                        Code</label>
                                    <select class="form-control select2bs4" id="price_code" name="price_code">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->code }}">
                                                {{ $product->code . ' ' . $product->productName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="row mb-2">

                                        <div class="col-sm-6">
                                            <label class="form-label" for="quant"><i
                                                    style="color:red">*</i>Quantity</label>
                                            <input type="number" class="form-control" id="quant" name="quant">
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
                                            <input type="number" step=".01" class="form-control" id="price"
                                                name="price">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </form>

                            <form id="ifBadPricing" method="POST" action="/pricing/store">
                                @csrf

                                <div class="form-group">
                                    <input type="hidden" name="pricing_id" id="b_pricing_id" class="form-control"
                                        required readonly>
                                </div>

                                <div class="form-group">

                                    <label class="form-label" for="product_type"><i style="color:red">*</i>Product
                                        Type</label>
                                    <select class="form-control select2bs4" id="product_type" name="product_type">
                                        @foreach ($productTypes as $pType)
                                            <option value="{{ $pType->code }}">
                                                {{ $pType->code . ' ' . $pType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-6">
                                            <label class="form-label" for="price"><i
                                                    style="color:red">*</i>Price</label>
                                            <input type="number" step=".01" class="form-control" id="price"
                                                name="price">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Save changes</button>
                            </form>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal -->
            </div>
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
                            <div class="row mb-2">

                                <div class="col-sm-6">
                                    <label class="form-label" for="e_quant">Quantity</label>
                                    <input type="number" class="form-control" id="e_quant" name="e_quant">
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label" for="e_price_unit">Unit</label>
                                    <select class="form-control" id="e_price_unit" name="e_price_unit">
                                        <option value="Bag/s">Bag/s</option>
                                        <option value="Box/es">Box/es</option>
                                        <option value="Pc/s">Pc/s</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="e_price_unit"><i style="color:red">*</i>Price</label>
                            <input type="numeric" step=".01" class="form-control" id="e_price" name="e_price">
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save price</button>
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
        function setToUpdatePrice(price_id, qty, unit, price) {
            $('#e_quant').val(qty);
            $('#e_price_unit').val(unit);
            $('#price_id').val(price_id);
            $('#e_price').val(price);
        }

        // create a js function that populate datatable example1 search input when select option is changed
        $(document).ready(function() {
            $('#price_level').on('change', function() {
                var value = $(this).val();
                $('#example1_filter input').val(value).trigger('keyup');
            });
        });

        // clear the search input when clear button is clicked
        $(document).ready(function() {
            $('#clear').on('click', function() {
                $('#price_level').val('');
                $('#example1_filter input').val('').trigger('keyup');
            });
        });

        $(document).ready(function() {

            // hide the ifBadPricing form
            $('#ifBadPricing').hide();

            $('#pricing_id').on('change', function() {

                var value = $(this).val();

                // how to get the text of the selected option
                var selectedPricingText = $("#pricing_id option:selected").text();

                console.log(selectedPricingText);

                if (selectedPricingText != 'BAD PRICING') {

                    $('#ifNotBadPricing').show();
                    $('#ifBadPricing').hide();

                    $('#nb_pricing_id').val(value);

                } else {

                    $('#ifNotBadPricing').hide();
                    $('#ifBadPricing').show();
                    $('#b_pricing_id').val(value);

                }
            });
        });
    </script>
@endsection
