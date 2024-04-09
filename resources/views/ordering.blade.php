@extends('layouts.app')

@section('contents')
    <style>
        .buttontypes {
            margin: 5px;
        }
    </style>

    <style>
        /* Target only the <td> elements within the table */
        table.table-bordered td {
            border: none !important;
            /* Remove border from <td> elements */
        }

        .table-container {
            height: 100vh;
        }

        @media (max-width: 767px) {
            th {
                display: none;
            }
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
                            <table class="table table-bordered">
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
                                    <tr class="table-container">
                                        <td style="border: 0px"></td>
                                        <td>aaaa</td>
                                        <td>aaaa</td>
                                        <td>aaa</td>
                                        <td>aaa</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th> </th>
                                        <th> </th>
                                        <th> </th>
                                        <th>Total: </th>
                                        <th> </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="col-sm-4">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product Type</th>
                                        <th>Quantity</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-container">
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
@endsection
