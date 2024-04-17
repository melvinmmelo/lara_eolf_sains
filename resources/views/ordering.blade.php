@extends('layouts.app')

@section('contents')
    <style>
        .buttontypes {
            margin: 5px;
        }
    </style>


    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Ordering</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Ordering</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ordering Info</h3>

                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>

                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-3">
                            <label class="form-label" for="price-quantity">Price Level</label>
                            <input type="text" class="form-control" id="#" name="#">
                        </div>

                        <div class="col-sm-3">
                            <label class="form-label" for="price-quantity">Delivery Person</label>
                            <input type="text" class="form-control" id="#" name="#">
                        </div>

                        <div class="col-sm-3">
                            <label class="form-label" for="price-quantity">Vehicle</label>
                            <input type="text" class="form-control" id="#" name="#">
                        </div>

                        <div class="col-sm-3">
                            <label class="form-label" for="price-quantity">Equipment</label>
                            <input type="text" class="form-control" id="#" name="#">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="form-label" for="button types">Types</label>
                            <div class="buttontypes">
                                <button type="button" class="btn btn-primary">BC</button>
                                <button type="button" class="btn btn-primary">BC</button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="form-label" for="button types">Products</label>
                            <div class="buttontypes">
                                <button type="button" class="btn btn-primary">BC</button>
                                <button type="button" class="btn btn-primary">BC</button>
                            </div>
                        </div>

                    </div>
                </div>
                <div>
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Unit Price</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="d-md-none"><strong>Items</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle">aaaa</td>
                                            <td class="align-middle">

                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <button type="button"
                                                            class="quantity-left-minus btn btn-danger btn-number"
                                                            data-type="minus" data-field="">
                                                            <span class="fas fa-minus"></span>
                                                        </button>
                                                    </div>
                                                    <input type="text" id="quantity" name="quantity"
                                                        class="form-control input-number" value="1" min="1"
                                                        max="99999">
                                                    <div class="input-group-append">
                                                        <button type="button"
                                                            class="quantity-right-plus btn btn-success btn-number"
                                                            data-type="plus" data-field="">
                                                            <span class="fas fa-plus"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">aaa</td>
                                            <td class="align-middle">aaa</td>
                                            <td class="align-middle">aaa</td>
                                        </tr>


                                        <!-- Additional rows here -->
                                        <tr>
                                            <td colspan="5" class="d-md-none"><strong>Total</strong></td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="desktop-view">
                                        <tr>
                                            <td colspan="3"></td>
                                            <td>Total:</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>


                            <style>
                                .input-number {
                                    text-align: center;
                                }

                                @media (max-width: 767px) {

                                    .table-responsive table td,
                                    .table-responsive table th {
                                        display: block;
                                        width: 100%;
                                    }

                                    .table-responsive table th {
                                        display: none;
                                    }

                                    .align-middle {
                                        text-align: left;
                                        padding: 8px;
                                    }

                                    .d-md-table-header {
                                        display: table-header-group !important;
                                        border: none !important;
                                    }

                                    .d-md-none {
                                        display: none;
                                    }


                                    .desktop-view {
                                        display: none;
                                    }
                                }
                            </style>
                        </div>

                        <div class="col-sm-4">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Type</th>
                                        <th>Quantity</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>

                                    </tr>
                                </tbody>

                            </table>
                        </div>
                    </div>


                </div>


            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="button" class="btn btn-danger">Discard</button>
                <button type="button" class="btn btn-success swalDefaultSuccess">Save</button>
            </div>
            <!-- /.card-footer-->
        </div>
        <!-- /.card -->

    </section>
    <!-- /.content -->



    {{-- Quantity button script --}}
    <script>
        $(document).ready(function() {

            var quantitiy = 0;
            $('.quantity-right-plus').click(function(e) {

                // Stop acting like a button
                e.preventDefault();
                // Get the field name
                var quantity = parseInt($('#quantity').val());

                // If is not undefined

                $('#quantity').val(quantity + 1);


                // Increment

            });

            $('.quantity-left-minus').click(function(e) {
                // Stop acting like a button
                e.preventDefault();
                // Get the field name
                var quantity = parseInt($('#quantity').val());

                // If is not undefined

                // Increment
                if (quantity > 0) {
                    $('#quantity').val(quantity - 1);
                }
            });

        });
    </script>
@endsection
